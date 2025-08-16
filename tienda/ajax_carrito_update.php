<?php
declare(strict_types=1);
session_start();

ini_set('display_errors', '0');
ini_set('log_errors', '1');
header('Content-Type: application/json; charset=utf-8');

// Limpia cualquier salida previa
while (ob_get_level() > 0) { ob_end_clean(); }

if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
  $_SESSION['carrito'] = [];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Método no permitido']); exit;
}

$index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
$delta = isset($_POST['delta']) ? (int)$_POST['delta'] : 0;

if ($index < 0 || !isset($_SESSION['carrito'][$index]) || $delta === 0) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']); exit;
}

// Actualiza cantidad
$_SESSION['carrito'][$index]['cantidad'] += $delta;

// Si quedara <= 0, eliminar
$itemRemoved = false;
if ($_SESSION['carrito'][$index]['cantidad'] <= 0) {
  unset($_SESSION['carrito'][$index]);
  $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar
  $itemRemoved = true;
}

// Recalcular totales
$cartCount = 0;
$cartTotal = 0.0;
$itemSubtotal = 0.0;
$newQty = 0;

foreach ($_SESSION['carrito'] as $i => $it) {
  $qty = (int)$it['cantidad'];
  $price = (float)$it['precio'];
  $cartCount += $qty;
  $cartTotal += $qty * $price;

  if ($i === $index && !$itemRemoved) {
    $newQty = $qty;
    $itemSubtotal = $qty * $price;
  }
}

echo json_encode([
  'success'       => true,
  'item_index'    => $index,
  'item_removed'  => $itemRemoved,
  'new_qty'       => $newQty,
  'item_subtotal' => number_format($itemSubtotal, 2, '.', ''),
  'cart_total'    => number_format($cartTotal, 2, '.', ''),
  'cart_count'    => $cartCount,
  'cart_empty'    => empty($_SESSION['carrito']),
], JSON_UNESCAPED_UNICODE);
exit;