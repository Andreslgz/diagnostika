<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    header("Location: {$url}/index.php");
    exit;
}