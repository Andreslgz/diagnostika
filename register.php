<?php
session_start();
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json');

function json_res($ok, $extra = [])
{
    echo json_encode(array_merge(['success' => $ok], $extra));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_res(false, ['message' => 'Método no permitido.']);
}

$nombreCompleto  = trim($_POST['nombre_completo'] ?? '');
$email           = trim($_POST['email'] ?? '');
$password        = trim($_POST['password'] ?? '');
$passwordConfirm = trim($_POST['password_confirm'] ?? '');
$telefono        = trim($_POST['telefono'] ?? '');
$pais            = trim($_POST['pais'] ?? '');
$codigoReferido  = trim($_POST['codigo_referido'] ?? '');
$aceptaTerminos  = isset($_POST['terms']);

if ($nombreCompleto === '' || $email === '' || $password === '' || $passwordConfirm === '') {
    json_res(false, ['message' => 'Completa los campos obligatorios.']);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_res(false, ['message' => 'Correo inválido.']);
}
if ($password !== $passwordConfirm) {
    json_res(false, ['message' => 'Las contraseñas no coinciden.']);
}
if (!$aceptaTerminos) {
    json_res(false, ['message' => 'Debes aceptar los términos y condiciones.']);
}

// ¿Correo ya existe?
try {
    $existe = $database->has('usuarios', ['email' => $email]);
    if ($existe) {
        json_res(false, ['message' => 'Este correo ya está registrado. Inicia sesión.']);
    }

    // Separar nombre/apellidos
    $partes = preg_split('/\s+/', $nombreCompleto, 2);
    $nombre = $partes[0] ?? '';
    $apellidos = $partes[1] ?? '';

    // Cifrar contraseña
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Insertar
    $database->insert('usuarios', [
        'nombre'         => $nombre,
        'apellidos'      => $apellidos,
        'email'          => $email,
        'password_hash'  => $passwordHash,
        'fecha_registro' => date('Y-m-d H:i:s'),
        'telefono'       => $telefono,
        'Pais'           => $pais,            // según tu columna exacta en la imagen
        'codigo_referido' => $codigoReferido,
        'estado'         => 'activo'
    ]);

    $id = $database->id(); // último id insertado en Medoo
    if (!$id) {
        json_res(false, ['message' => 'No se pudo crear la cuenta.']);
    }

    // AUTOLOGIN
    $_SESSION['usuario_id'] = $id;

    // Redirección según carrito
    $redirect = (!empty($_SESSION['carrito'])) ? 'checkout.php' : 'index.php';

    json_res(true, [
        'message'  => '¡Registro exitoso! Te hemos conectado automáticamente.',
        'redirect' => $redirect
    ]);
} catch (Throwable $e) {
    // Puedes loguear $e->getMessage() en un archivo si quieres
    json_res(false, ['message' => 'Error del servidor. Intenta más tarde.']);
}