<?php
session_start();
require_once __DIR__ . '/../includes/db.php'; // Aquí se instancia $database (Medoo)
require_once __DIR__ . '/config.php';

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $_SESSION['mensaje'] = "Faltan campos obligatorios.";
        header("Location: index.php");
        exit();
    }

    // Buscar usuario por email
    $usuario = $database->get("adm_login", [
        "adm_id",
        "adm_nombre",
        "adm_passw",
        "adm_est"
    ], [
        "adm_email" => $email
    ]);

    if ($usuario) {
        if ($usuario['adm_est'] == 0) {
            $_SESSION['mensaje'] = "Usuario inactivo.";
            header("Location: index.php");
            exit();
        }



        if (password_verify($password, $usuario['adm_passw'])) {
            $_SESSION['usuario_id'] = $usuario['adm_id'];
            $_SESSION['nombre'] = $usuario['adm_nombre'];
            header("Location: panel.php");
            exit();
        } else {
            $_SESSION['mensaje'] = "Contraseña incorrecta.";
        }
    } else {
        $_SESSION['mensaje'] = "Usuario no encontrado.";
    }

    header("Location: index.php");
    exit();
}

// // Para generar un hash de contraseña manualmente:
//echo password_hash('123456', PASSWORD_DEFAULT);
?>
<!doctype html>
<html lang="en" dir="ltr">

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

</head>

<body class="app sidebar-mini ltr login-img">
    <?php if (isset($_SESSION['mensaje'])): ?>
    <div class="modal fade" id="modalMensaje" tabindex="-1" aria-labelledby="modalMensajeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-gradient bg-danger text-white rounded-top-4 py-3">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="modalMensajeLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i> Atención
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center px-4 py-4">
                    <p class="mb-0 fs-5 text-danger fw-semibold">
                        <?php echo $_SESSION['mensaje'];
                            unset($_SESSION['mensaje']); ?>
                    </p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalMensaje'));
        modal.show();
    });
    </script>
    <?php endif; ?>

    <!-- BACKGROUND-IMAGE -->
    <div class="">

        <!-- GLOABAL LOADER -->
        <div id="global-loader">
            <img src="<?php echo $url; ?>/panel/assets/images/loader.svg" class="loader-img" alt="Loader">
        </div>
        <!-- /GLOABAL LOADER -->

        <!-- PAGE -->
        <div class="page">
            <div class="">

                <!-- CONTAINER OPEN -->
                <div class="col col-login mx-auto mt-7">
                    <div class="text-center">
                        <a href="index.php"><img src="<?php echo $url; ?>/panel/assets/images/brand/logo-white.png"
                                class="header-brand-img" alt=""></a>
                    </div>
                </div>

                <div class="container-login100">
                    <div class="wrap-login100 p-6">
                        <form class="login100-form validate-form" method="post" action="index.php">
                            <div class="panel panel-primary">
                                <div class="panel-body tabs-menu-body p-0 pt-5">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab5">
                                            <div class="wrap-input100 validate-input input-group">
                                                <span class="input-group-text bg-white text-muted">
                                                    <i class="zmdi zmdi-email text-muted"></i>
                                                </span>
                                                <input class="input100 border-start-0 form-control ms-0" type="email"
                                                    name="email" value="andreslg20@gmail.com"
                                                    placeholder="Correo Electrónico" required>
                                            </div>

                                            <div class="wrap-input100 validate-input input-group" id="Password-toggle">
                                                <span class="input-group-text bg-white text-muted">
                                                    <i class="zmdi zmdi-eye text-muted"></i>
                                                </span>
                                                <input class="input100 border-start-0 form-control ms-0" type="password"
                                                    name="password" value="123456" placeholder="Contraseña" required>
                                            </div>

                                            <div class="container-login100-form-btn mt-3">
                                                <button type="submit" class="login100-form-btn btn-primary">
                                                    INGRESAR
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- CONTAINER CLOSED -->
            </div>
        </div>
        <!-- End PAGE -->



    </div>
    <!-- BACKGROUND-IMAGE CLOSED -->

    <?php if (isset($_SESSION['mensaje'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalMensaje'));
        modal.show();
    });
    </script>
    <?php endif; ?>


    <!-- JQUERY JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SHOW PASSWORD JS -->
    <script src="<?php echo $url; ?>/panel/assets/js/show-password.min.js"></script>

    <!-- GENERATE OTP JS -->
    <script src="<?php echo $url; ?>/panel/assets/js/generate-otp.js"></script>

    <!-- Perfect SCROLLBAR JS-->
    <script src="<?php echo $url; ?>/panel/assets/plugins/p-scroll/perfect-scrollbar.js"></script>

    <!-- Color Theme js -->
    <script src="<?php echo $url; ?>/panel/assets/js/themeColors.js"></script>

    <!-- CUSTOM JS -->
    <script src="<?php echo $url; ?>/panel/assets/js/custom.js"></script>

</body>


</html>