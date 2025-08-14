<?php
// declare(strict_types=1); // mejor desactivado mientras depuras

//header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0'); // para depurar pon '1' temporalmente
session_start();

// Evita que cualquier salida previa rompa el JSON
if (ob_get_level() === 0) { ob_start(); }
@ob_clean();

require_once __DIR__ . '../../includes/db.php'; // verifica ruta

function out(array $arr, int $code = 200): void {
  http_response_code($code);
  if (ob_get_length() !== false) { @ob_clean(); }
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

/**
 * Reemplaza placeholders :param por valores con comillas para depuración.
 * NO ejecutar el resultado; es solo para leerlo.
 */
function buildDebugSQL(string $sql, array $params): string {
  // Ordenar por longitud descendente para evitar que :m1 reemplace dentro de :m10
  uksort($params, function($a, $b) { return strlen($b) <=> strlen($a); });
  foreach ($params as $k => $v) {
    if (is_numeric($v)) {
      $rep = (string)$v;
    } else {
      $rep = "'" . addslashes((string)$v) . "'";
    }
    $sql = str_replace($k, $rep, $sql);
  }
  return $sql;
}

try {
  if (!isset($database)) {
    out(['ok' => false, 'msg' => 'Conexión no inicializada ($database)'], 500);
  }

  // ---- DEBUG FLAG ----
  $DEBUG = !empty($_POST['debug']) || !empty($_GET['debug']);

  // -------- Input --------
  $page    = (int)($_POST['page'] ?? $_GET['page'] ?? 1);
  $page    = max(1, $page);
  $perPage = 12;

  // Filtros desde POST o GET
  $marcas  = $_POST['marca'] ?? $_GET['marca'] ?? [];
  $anios   = $_POST['anio']  ?? $_GET['anio']  ?? [];

  // Normalizar arrays y limpiar vacíos
  $marcas = array_values(array_filter((array)$marcas, function($v){ return $v !== '' && $v !== null; }));
  $anios  = array_values(array_filter((array)$anios,  function($v){ return $v !== '' && $v !== null; }));

  $offset = ($page - 1) * $perPage;
  $limit  = max(1, (int)$perPage);
  $offset = max(0, (int)$offset);

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

  // -------- SQL base (estricto al filtrar) --------
  if (empty($marcas) && empty($anios)) {
    // Sin filtros: todos los productos (incluye sin características)
    $sqlBase = "
      FROM productos p
      LEFT JOIN caracteristicas_productos c ON c.id_producto = p.id_producto
      WHERE 1=1
    ";
  } else {
    // Con filtros: SOLO productos con características que coinciden
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

  // -------- Datos --------
  $sqlData = "
    SELECT p.id_producto, p.nombre, p.precio, p.imagen, c.marca, c.anio
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

  // -------- Debug opcional --------
  if ($DEBUG) {
    error_log('[SQL TOTAL] ' . buildDebugSQL($sqlTotal, $params));
    error_log('[SQL DATA]  ' . buildDebugSQL($sqlData,  $params));
    error_log('[PARAMS] ' . print_r($params, true));
  }

  /// -------- Respuesta --------
$payload = [
  'ok'              => true,
  'data'            => $productos,   // <- array crudo de productos
  'total'           => $total,
  'page'            => $current ?? $page,
  'pages'           => $totalPages,
  'grid_html'       => $cards,       // <- HTML renderizado del grid
  'pagination_html' => $pagination   // <- HTML de la paginación
];

// Si activas ?debug=1, también te envío SQL ligado y params
if (!empty($DEBUG)) {
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
    'debug' => (!empty($_POST['debug']) || !empty($_GET['debug'])) ? [
      'trace' => $e->getTrace()
    ] : null
  ], 500);
}
?>
