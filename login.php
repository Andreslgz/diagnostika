<?php
// auth.php
declare(strict_types=1);
session_start();

// Si NO hay sesión, redirige a index.php
if (empty($_SESSION['usuario_id'])) {
  header('Location: /index.php');
  exit;
}

// (Opcional) Cabeceras anti-cache en zonas privadas
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Por si algún include imprimió algo (BOM/espacios)
while (ob_get_level() > 0) { ob_end_clean(); }

session_start();
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
$password = isset($_POST['password']) ? trim((string)$_POST['password']) : '';

if ($email === '' || $password === '') {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'message' => 'Debes ingresar correo y contraseña.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Trae solo lo necesario
$usuario = $database->get('usuarios', [
    'id_usuario',
    'password_hash'
], [
    'email' => $email
]);

// Respuesta uniforme (no revelar si el correo existe)
if ($usuario && password_verify($password, $usuario['password_hash'])) {
    // Seguridad: regenerar la sesión en login
    session_regenerate_id(true);
    $_SESSION['usuario_id'] = (int)$usuario['id_usuario'];

    // Redirección según carrito
    $redirectUrl = (!empty($_SESSION['carrito'])) ? 'tienda/carrito.php' : 'index.php';

    http_response_code(200);
    echo json_encode([
        'success'  => true,
        'redirect' => $redirectUrl
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

http_response_code(401); // Unauthorized
echo json_encode([
    'success' => false,
    'message' => 'Correo o contraseña incorrectos. Si aún no tienes una cuenta, por favor regístrate para continuar.'
], JSON_UNESCAPED_UNICODE);
exit;