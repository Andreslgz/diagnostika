<?php
require __DIR__ . '/../vendor/autoload.php';

$url = "http://localhost";
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
    const BASE_DIR = "<?php echo $url; ?>";
</script>