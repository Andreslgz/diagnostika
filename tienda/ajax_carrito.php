<?php
declare(strict_types=1);

// Este endpoint devuelve SOLO JSON
ini_set('display_errors', '0');
ini_set('log_errors', '1');
header('Content-Type: application/json; charset=utf-8');

// Limpia cualquier salida previa
while (ob_get_level() > 0) { ob_end_clean(); }

session_start();
require_once __DIR__ . '/../includes/db.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Recibe id y cantidad (opcional)
$id = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
$cantidad = isset($_POST['cantidad']) ? max(1, (int)$_POST['cantidad']) : 1;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Producto inválido']);
    exit;
}

// Busca producto
$prod = $database->get('productos', [
    'id_producto',
    'nombre',
    'precio',
    'imagen'
], ['id_producto' => $id]);

if (!$prod) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    exit;
}

// Agrega / incrementa
if (isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
} else {
    $_SESSION['carrito'][$id] = [
        'nombre'   => (string)$prod['nombre'],
        'precio'   => (float)$prod['precio'],
        'imagen'   => (string)$prod['imagen'],
        'cantidad' => $cantidad,
    ];
}

// Calcula totales rápidos para UI
$cartCount = 0;
$cartTotal = 0.0;
foreach ($_SESSION['carrito'] as $item) {
    $cartCount += (int)$item['cantidad'];
    $cartTotal += ((float)$item['precio']) * (int)$item['cantidad'];
}

echo json_encode([
    'success'    => true,
    'message'    => 'Producto añadido al carrito correctamente',
    'cart_count' => $cartCount,
    'cart_total' => number_format($cartTotal, 2, '.', ''),
], JSON_UNESCAPED_UNICODE);
exit;