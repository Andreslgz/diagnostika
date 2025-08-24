<?php
require __DIR__ . '/../vendor/autoload.php';

$ENV = "dev"; // o "prod" // "dev"

$BASE_DIR = ($ENV === "dev")
    ? "https://localhost"
    : "https://mysistemaweb.com/diagnostika";

$url = $BASE_DIR;

$titulo = "DIAGNOSTIKA DIESEL GLOBAL";

use Medoo\Medoo;

$database = new Medoo([
    'type' => 'mysql',
    'host' => 'localhost',
    'database' => 'mysiste2_tienda',
    'username' => 'mysiste2_root',
    'password' => 'karen2024'
]);

?>