<?php
session_start();
require_once __DIR__ . '/includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $usuario = $database->get('usuarios', '*', ['email' => $email]);

    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        $_SESSION['usuario_id'] = $usuario['id_usuario'];

        $redirectUrl = !empty($_SESSION['carrito']) ? 'checkout.php' : 'index.php';

        echo json_encode([
            'success' => true,
            'redirect' => $redirectUrl
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Correo o contraseña incorrectos. Si aún no tienes una cuenta, por favor regístrate para continuar.'
        ]);
    }
    exit;
}