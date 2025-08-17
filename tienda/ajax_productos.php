<?php
// mientras depuras puedes poner display_errors a '1'
error_reporting(E_ALL);
ini_set('display_errors', '0');
session_start();

// Salida será SIEMPRE JSON
header('Content-Type: application/json; charset=utf-8');

// Evita que cualquier salida previa rompa el JSON
while (ob_get_level() > 0) { @ob_end_clean(); }
ob_start();

// Conexión a BD (ajusta si tu estructura difiere)
require_once __DIR__ . '/../includes/db.php';

function out(array $arr, int $code = 200): void {
  http_response_code($code);
  @ob_clean();
  echo json_encode($arr, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

// --- Favoritos del usuario actual ---
$favoritos_usuario = [];
if (!empty($_SESSION['usuario_id'] ?? null)) {
  $favs = $database->select('favoritos', 'id_producto', [
    'id_usuario' => (int)$_SESSION['usuario_id']
  ]);
  if (is_array($favs)) {
    $favoritos_usuario = array_values(array_map('intval', $favs));
  }
}

/**
 * Reemplaza placeholders :param por valores para depuración.
 * (Solo lectura, no ejecutar el SQL resultante)
 */
function buildDebugSQL(string $sql, array $params): string {
  uksort($params, fn($a,$b) => strlen($b) <=> strlen($a));
  foreach ($params as $k => $v) {
    $rep = is_numeric($v) ? (string)$v : "'" . addslashes((string)$v) . "'";
    $sql = str_replace($k, $rep, $sql);
  }
  return $sql;
}

try {
  if (!isset($database)) {
    out(['ok' => false, 'msg' => 'Conexión no inicializada ($database)'], 500);
  }

  // Flag de debug (GET/POST ?debug=1)
  $DEBUG = !empty($_POST['debug']) || !empty($_GET['debug']);

  // -------- Input --------
  $page    = max(1, (int)($_POST['page'] ?? $_GET['page'] ?? 1));
  $perPage = 12;

  // Filtros desde POST o GET
  $marcas  = $_POST['marca'] ?? $_GET['marca'] ?? [];
  $anios   = $_POST['anio']  ?? $_GET['anio']  ?? [];

  // Normalizar arrays y limpiar vacíos
  $marcas = array_values(array_filter((array)$marcas, fn($v) => $v !== '' && $v !== null));
  $anios  = array_values(array_filter((array)$anios,  fn($v) => $v !== '' && $v !== null));

  $offset = ($page - 1) * $perPage;
  $limit  = $perPage;

  // -------- Placeholders de filtros --------
  $params  = [];
  $marcaIn = '';
  $anioIn  = '';

  if (!empty($marcas)) {
    $tmp = [];
    foreach ($marcas as $i => $m) {
      $ph = ":m{$i}";
      $tmp[] = $ph;
      $params[$ph] = $m;
    }
    $marcaIn = implode(',', $tmp);
  }

  if (!empty($anios)) {
    $tmp = [];
    foreach ($anios as $i => $a) {
      $ph = ":a{$i}";
      $tmp[] = $ph;
      $params[$ph] = $a;
    }
    $anioIn = implode(',', $tmp);
  }

  // Filtros combinados
  $whereFilters = [];
  if ($marcaIn !== '') $whereFilters[] = "c.marca IN ($marcaIn)";
  if ($anioIn  !== '') $whereFilters[] = "c.anio  IN ($anioIn)";
  $filtersSQL = $whereFilters ? implode(' AND ', $whereFilters) : '1=1';

  // -------- SQL base --------
  if (empty($marcas) && empty($anios)) {
    // Sin filtros: todos los productos (incluye sin características)
    $sqlBase = "
      FROM productos p
      LEFT JOIN caracteristicas_productos c ON c.id_producto = p.id_producto
      WHERE 1=1
    ";
  } else {
    // Con filtros: solo productos con características que coinciden
    $sqlBase = "
      FROM productos p
      INNER JOIN caracteristicas_productos c ON c.id_producto = p.id_producto
      WHERE $filtersSQL
    ";
  }

  // -------- Total --------
  $sqlTotal = "SELECT COUNT(DISTINCT p.id_producto) AS total " . $sqlBase;
  $stmtTotal = $database->query($sqlTotal, $params);
  if (!$stmtTotal) {
    if ($DEBUG) error_log('[SQL TOTAL] ' . buildDebugSQL($sqlTotal, $params));
    out(['ok' => false, 'msg' => 'Error en consulta de total'], 500);
  }
  $rowTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0];
  $total    = (int)($rowTotal['total'] ?? 0);
  $totalPages = max(1, (int)ceil($total / $perPage));

  // -------- Datos --------
  // Incluyo p.descripcion para que puedas usar data-desc en el botón Preview
  $sqlData = "
    SELECT
      p.id_producto,
      p.nombre,
      p.precio,
      p.imagen,
      p.descripcion,
      c.marca,
      c.anio
    $sqlBase
    GROUP BY p.id_producto
    ORDER BY p.id_producto DESC
    LIMIT $limit OFFSET $offset
  ";
  $stmtData = $database->query($sqlData, $params);
  if (!$stmtData) {
    if ($DEBUG) error_log('[SQL DATA] ' . buildDebugSQL($sqlData, $params));
    out(['ok' => false, 'msg' => 'Error en consulta de datos'], 500);
  }
  $productos = $stmtData->fetchAll(PDO::FETCH_ASSOC) ?: [];

  // -------- Adjuntar GALERÍA a cada producto --------
  // Nota: esto hace 1 query por producto. Si te preocupa rendimiento,
  // puedes traer todas las galerías en una sola consulta y agrupar en PHP.
  foreach ($productos as &$prod) {
    $idProd = (int)($prod['id_producto'] ?? 0);

    // Trae solo los nombres de archivo de la galería
    $galeria = $database->select(
      "galeria_productos",
      "gal_img",
      [
        "id_producto" => $idProd,
        "gal_est"     => "activo",
        "ORDER"       => ["gal_id" => "DESC"]
      ]
    );

    // Prefija cada imagen con /uploads/
    $galeria_full = array_map(
      fn($f) => '/uploads/' . ltrim((string)$f, '/'),
      is_array($galeria) ? $galeria : []
    );

    // Imagen principal del producto (primero en la lista)
    $imagen_principal = !empty($prod['imagen'])
      ? '/uploads/' . ltrim((string)$prod['imagen'], '/')
      : null;

    if ($imagen_principal) {
      // Evita duplicado si ya está en galería
      if (!in_array($imagen_principal, $galeria_full, true)) {
        array_unshift($galeria_full, $imagen_principal);
      } else {
        // Asegura que esté en primera posición
        $galeria_full = array_values(array_unique([$imagen_principal, ...$galeria_full]));
      }
    }

    // Agrego campos “amigables” para el frontend
    $prod['imagen_url']  = $imagen_principal ?: '';
    $prod['gallery']     = $galeria_full;                       // ← array listo para data-gallery
    $prod['marca']       = $prod['marca'] ?? '';                // ← para data-brand
    $prod['descripcion'] = $prod['descripcion'] ?? '';          // ← para data-desc
  }
  unset($prod); // buena práctica al usar referencias

  // -------- Debug opcional --------
  if ($DEBUG) {
    error_log('[SQL TOTAL] ' . buildDebugSQL($sqlTotal, $params));
    error_log('[SQL DATA]  ' . buildDebugSQL($sqlData,  $params));
    error_log('[PARAMS] ' . print_r($params, true));
  }

  // -------- Respuesta --------
  $payload = [
    'ok'              => true,
    'data'            => $productos,     // cada item trae: id_producto, nombre, precio, imagen, imagen_url, descripcion, marca, anio, gallery[]
    'total'           => $total,
    'page'            => $page,
    'pages'           => $totalPages,
    'grid_html'       => '',             // si algún día envías HTML del grid, colócalo aquí
    'pagination_html' => '',
    'favorites'       => $favoritos_usuario,
  ];

  // Si activas ?debug=1, también envío SQL ligado y params
  if ($DEBUG) {
    $payload['debug'] = [
      'sqlDataBound'  => buildDebugSQL($sqlData,  $params),
      'sqlTotalBound' => buildDebugSQL($sqlTotal, $params),
      'params'        => $params
    ];
  }

  out($payload);

} catch (Throwable $e) {
  error_log('[ajax_productos] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
  out([
    'ok'    => false,
    'msg'   => 'Error interno',
    'detail'=> $e->getMessage(),
    'debug' => (!empty($_POST['debug']) || !empty($_GET['debug'])) ? ['trace' => $e->getTrace()] : null
  ], 500);
}