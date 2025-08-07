<?php
require __DIR__ . '/../vendor/autoload.php';

use Medoo\Medoo;

$database = new Medoo([
    'type' => 'mysql',
    'host' => 'localhost',
    'database' => 'mysiste2_tienda',
    'username' => 'mysiste2_root',
    'password' => 'karen2024'
]);