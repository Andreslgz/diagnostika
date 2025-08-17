<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
while (ob_get_level() > 0) { @ob_end_clean(); }

session_start();
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['usuario_id'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'message'=>'No autorizado'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'message'=>'Método no permitido'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

$nombreCompleto = trim((string)($_POST['nombre_completo'] ?? ''));
$pais           = trim((string)($_POST['pais'] ?? ''));
$telefono       = trim((string)($_POST['telefono'] ?? ''));
$email          = trim((string)($_POST['email'] ?? ''));

if ($nombreCompleto === '' || $pais === '' || $email === '') {
  http_response_code(400);
  echo json_encode(['ok'=>false,'message'=>'Completa los campos obligatorios.'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'message'=>'Correo inválido.'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  exit;
}

$id = (int)$_SESSION['usuario_id'];

/**
 * Separa un nombre completo en nombre y apellidos.
 * Regla:
 *  - >=3 palabras: apellidos = últimas 2; nombre = resto
 *  - 2 palabras:   apellidos = última;   nombre = primera
 *  - 1 palabra:    nombre = esa;        apellidos = ''
 */
function splitNombreCompleto(string $full): array {
  // Normaliza espacios
  $full = trim(preg_replace('/\s+/', ' ', $full));
  if ($full === '') return ['nombre'=>'', 'apellidos'=>''];

  $parts = explode(' ', $full);

  if (count($parts) >= 3) {
    $apellidos = implode(' ', array_slice($parts, -2));
    $nombre   = implode(' ', array_slice($parts, 0, -2));
  } elseif (count($parts) === 2) {
    $apellidos = $parts[1];
    $nombre   = $parts[0];
  } else { // 1 palabra
    $nombre   = $parts[0];
    $apellidos = '';
  }

  return ['nombre'=>$nombre, 'apellidos'=>$apellidos];
}

try {
  $na = splitNombreCompleto($nombreCompleto);

  // Construye el array de actualización
  $updateData = [
    'nombre'  => $na['nombre'],
    'apellidos'=> $na['apellidos'],
    'pais'     => $pais,
    'telefono' => $telefono,
    'email'    => $email
  ];

  // (Opcional) mantener columna legacy nombre_completo si existe en tu esquema
  // Quita esta línea si ya no usas ese campo:
  //$updateData['nombre_completo'] = $nombreCompleto;

  $res = $database->update('usuarios', $updateData, ['id_usuario' => $id]);

  // Medoo retorna PDOStatement; rowCount() puede ser 0 si no hubo cambios
  if ($res && $res->rowCount() >= 0) {
    echo json_encode(['ok'=>true,'message'=>'Datos actualizados correctamente.'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  } else {
    http_response_code(500);
    echo json_encode(['ok'=>false,'message'=>'No se realizaron cambios.'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
  }
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(
    ['ok'=>false,'message'=>'Error al guardar: '.$e->getMessage()],
    JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES
  );
}