<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0'); ini_set('log_errors', '1');

$nombre = isset($_POST['mc_nomb']) ? trim($_POST['mc_nomb']) : '';
if ($nombre === '') {
  echo json_encode(['success' => false, 'message' => 'Nombre vacío']); exit;
}

// Normaliza
$nombre = mb_strtoupper($nombre, 'UTF-8');

// ¿Ya existe?
$existe = $database->get('marcas', ['id','mc_nomb'], ['mc_nomb' => $nombre]);
if ($existe) {
  echo json_encode(['success' => true, 'id' => $existe['id'], 'mc_nomb' => $existe['mc_nomb']]);
  exit;
}

// Insertar
$id = $database->insert('marcas', ['mc_nomb' => $nombre]);
if ($id) {
  echo json_encode(['success' => true, 'id' => $id, 'mc_nomb' => $nombre]);
} else {
  echo json_encode(['success' => false, 'message' => 'No se pudo insertar']);
}