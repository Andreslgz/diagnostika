<?php
session_start();
require_once __DIR__ . '/includes/db.php'; // Aquí debes tener tu instancia Medoo como $database

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'auth' => false, 'message' => 'No autenticado']);
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$id_producto = intval($_POST['id_producto'] ?? 0);

if (!$id_producto) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit;
}

// Verificar si ya existe en favoritos
$existe = $database->has('favoritos', [
    'id_usuario' => $id_usuario,
    'id_producto' => $id_producto
]);

if ($existe) {
    // Eliminar de favoritos
    $database->delete('favoritos', [
        'AND' => [
            'id_usuario' => $id_usuario,
            'id_producto' => $id_producto
        ]
    ]);
    echo json_encode(['success' => true, 'favorito' => false]);
} else {
    // Insertar en favoritos
    $database->insert('favoritos', [
        'id_usuario' => $id_usuario,
        'id_producto' => $id_producto
    ]);
    echo json_encode(['success' => true, 'favorito' => true]);
}