<?php
require __DIR__ . '/../vendor/autoload.php';

$url = "https://diagnostika:8890";
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
<script>
    window.BASE_DIR = <?php echo json_encode($url); ?>;
</script>