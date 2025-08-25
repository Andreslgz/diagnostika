<?php
session_start();

// echo $_SESSION['usuario_id'];

if (empty($_SESSION['usuario_id'])) {
    header("Location: {$url}/index.php");
    exit;
}