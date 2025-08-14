<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0');

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

function out($arr, $status = 200) {
    http_response_code($status);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'remove') {
    $index = filter_input(INPUT_POST, 'index', FILTER_VALIDATE_INT);
    if ($index === null || !isset($_SESSION['carrito'][$index])) {
        out(['ok' => false, 'msg' => 'Ítem inválido'], 400);
    }

    unset($_SESSION['carrito'][$index]);
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);

    $total = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }

    out([
        'ok' => true,
        'total' => $total,
        'total_formatted' => '$' . number_format($total, 2),
        'items' => count($_SESSION['carrito'])
    ]);
}

out(['ok' => false, 'msg' => 'Acción inválida'], 400);