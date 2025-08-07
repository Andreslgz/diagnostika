<?php
session_start();
require_once __DIR__ . '/config.php';

$_SESSION = [];
session_destroy();
$url = $url ?? '..';
$titulo = "Sesión cerrada";
$empresa = $empresa ?? 'Diagnostika Diesel Global';
?>

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keywords" content="">

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $url; ?>/panel/assets/images/brand/favicon.ico" />

    <!-- TITLE -->
    <title><?php echo $titulo; ?></title>

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- STYLE CSS -->
    <link href="<?php echo $url; ?>/panel/assets/css/style.css" rel="stylesheet" />
    <link href="<?php echo $url; ?>/panel/assets/css/dark-style.css" rel="stylesheet" />
    <link href="<?php echo $url; ?>/panel/assets/css/transparent-style.css" rel="stylesheet">
    <link href="<?php echo $url; ?>/panel/assets/css/skin-modes.css" rel="stylesheet" />

    <!--- FONT-ICONS CSS -->
    <link href="<?php echo $url; ?>/panel/assets/css/icons.css" rel="stylesheet" />

    <!-- COLOR SKIN CSS -->
    <link id="theme" rel="stylesheet" type="text/css" media="all"
        href="<?php echo $url; ?>/panel/assets/colors/color1.css" />

    <style>
    body {
        background-color: #f5f6fa;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .logout-wrapper {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .logout-card {
        background: #fff;
        border-radius: 12px;
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 420px;
        text-align: center;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
    }

    .logout-card img {
        max-height: 60px;
        margin-bottom: 1.2rem;
    }

    .logout-card h2 {
        color: #3f9d8a;
        font-weight: 700;
    }

    .logout-card p {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    footer {
        text-align: center;
        padding: 1rem;
        font-size: 0.9rem;
        color: #888;
    }
    </style>
</head>

<body>

    <!-- Contenido centrado -->
    <div class="logout-wrapper">
        <div class="logout-card">
            <!-- Logo centrado arriba -->
            <img src="<?= $url ?>/panel/assets/images/brand/logo-white.png" alt="Logo">

            <h2>Sesión cerrada</h2>
            <p>Has cerrado sesión correctamente.</p>
            <a href="index.php" class="btn btn-primary w-100">Volver al inicio</a>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        Copyright © <span id="year"></span>
        <a href="javascript:void(0)"><?= $empresa ?></a> - Todos los derechos reservados.
    </footer>

    <script>
    // Año dinámico
    document.getElementById("year").textContent = new Date().getFullYear();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>