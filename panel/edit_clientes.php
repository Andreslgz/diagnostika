<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];

// Obtener ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    $_SESSION['resultado'] = 'ID de cliente no válido.';
    header("Location: clientes.php");
    exit;
}

// Obtener cliente actual
$cliente = $database->get("usuarios", "*", ["id_usuario" => $id]);
if (!$cliente) {
    $_SESSION['resultado'] = 'Cliente no encontrado.';
    header("Location: clientes.php");
    exit;
}

// MODALES
$mostrar_modal = false;
$mensaje_modal = '';
if (isset($_SESSION['resultado'])) {
    $mostrar_modal = true;
    $mensaje_modal = $_SESSION['resultado'];
    unset($_SESSION['resultado']);
}

// Actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre     = trim($_POST['nombre'] ?? '');
    $apellidos  = trim($_POST['apellidos'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $telefono   = trim($_POST['telefono'] ?? '');
    $direccion  = trim($_POST['direccion'] ?? '');
    $ciudad     = trim($_POST['ciudad'] ?? '');
    $foto       = $_FILES['foto_perfil'] ?? null;

    if ($nombre === '' || $apellidos === '' || $email === '') {
        $_SESSION['resultado'] = 'Los campos Nombre, Apellidos y Email son obligatorios.';
        header("Location: edit_clientes.php?id=$id");
        exit;
    }

    // Validar email duplicado
    $existe = $database->has("usuarios", [
        "AND" => [
            "email" => $email,
            "id_usuario[!]" => $id
        ]
    ]);
    if ($existe) {
        $_SESSION['resultado'] = 'Ya existe otro cliente con ese correo electrónico.';
        header("Location: edit_clientes.php?id=$id");
        exit;
    }

    // Procesar imagen
    $ruta_foto = $cliente['foto_perfil'];
    if ($foto && $foto['tmp_name']) {
        $ext = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nuevo_nombre = uniqid('cliente_', true) . '.' . $ext;
        $destino = __DIR__ . '/../uploads/clientes/' . $nuevo_nombre;

        if (move_uploaded_file($foto['tmp_name'], $destino)) {
            $ruta_foto = 'uploads/clientes/' . $nuevo_nombre;
        }
    }

    // Actualizar datos
    $actualizado = $database->update("usuarios", [
        "nombre"      => $nombre,
        "apellidos"   => $apellidos,
        "email"       => $email,
        "telefono"    => $telefono,
        "direccion"   => $direccion,
        "ciudad"      => $ciudad,
        "foto_perfil" => $ruta_foto
    ], [
        "id_usuario" => $id
    ]);

    $_SESSION['resultado'] = $actualizado->rowCount() > 0
        ? 'Cliente actualizado correctamente.'
        : 'No se realizaron cambios.';

    header("Location: edit_clientes.php?id=$id");
    exit;
}
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
    <link id="style" href="<?php echo $url; ?>/panel/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />

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

    <script>
    // Establecer configuración en localStorage desde PHP
    const sashConfig = {
        sashprimaryColor: "#FEB81D",
        sashlightMode: "true",
        sashprimaryTransparent: "#000000",
        sashprimaryBorderColor: "#000000",
        sashhorizontal: "true",
        sashprimaryHoverColor: "#FEB81D"
    };

    Object.entries(sashConfig).forEach(([key, value]) => {
        localStorage.setItem(key, value);
    });
    </script>

</head>

<body class="app sidebar-mini ltr light-mode">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="<?php echo $url; ?>/panel/assets/images/loader.svg" class="loader-img" alt="Loader">
    </div>
    <!-- /GLOBAL-LOADER -->

    <!-- PAGE -->
    <div class="page">
        <div class="page-main">

            <!-- app-Header -->
            <?php require_once __DIR__ . '/header_top.php'; ?>
            <!-- /app-Header -->

            <!--APP-SIDEBAR-->
            <?php require_once __DIR__ . '/header_menu.php'; ?>


            <!--app-content open-->
            <div class="main-content app-content mt-0">
                <div class="side-app">

                    <!-- CONTAINER -->
                    <div class="main-container container-fluid">

                        <!-- PAGE-HEADER -->
                        <div class="page-header">
                            <h1 class="page-title">Registrar Nuevo Cliente</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="panel.php">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Clientes</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <!-- ROW-1 -->
                        <div class="row" id="user-profile">
                            <div class="col-lg-12">
                                <div class="card shadow rounded-4">

                                    <div class="card-body">


                                        <form action="edit_clientes.php?id=<?= $id ?>" method="POST"
                                            enctype="multipart/form-data" novalidate>
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nombre *</label>
                                                    <input type="text" name="nombre" class="form-control"
                                                        value="<?= htmlspecialchars($cliente['nombre']) ?>" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Apellidos *</label>
                                                    <input type="text" name="apellidos" class="form-control"
                                                        value="<?= htmlspecialchars($cliente['apellidos']) ?>" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Email *</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="<?= htmlspecialchars($cliente['email']) ?>" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Teléfono</label>
                                                    <input type="text" name="telefono" class="form-control"
                                                        value="<?= htmlspecialchars($cliente['telefono']) ?>">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Dirección</label>
                                                    <input type="text" name="direccion" class="form-control"
                                                        value="<?= htmlspecialchars($cliente['direccion']) ?>">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Ciudad</label>
                                                    <input type="text" name="ciudad" class="form-control"
                                                        value="<?= htmlspecialchars($cliente['ciudad']) ?>">
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Foto de perfil</label>
                                                    <input type="file" name="foto_perfil" class="form-control">
                                                    <?php if ($cliente['foto_perfil']): ?>
                                                    <img src="../<?= $cliente['foto_perfil'] ?>"
                                                        class="img-thumbnail mt-2" style="max-height: 100px;">
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="mt-4 d-flex justify-content-between">
                                                <a href="clientes.php" class="btn btn-secondary">Cancelar</a>
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ROW-1 END -->


                    </div>
                    <!-- CONTAINER END -->
                </div>
            </div>
            <!--app-content close-->

        </div>

        <?php if ($mostrar_modal): ?>
        <div class="modal fade" id="modalResultadoEditar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-dark fw-semibold w-100 text-center">
                            <i class="bi bi-info-circle-fill text-info fs-3 me-2"></i> Resultado
                        </h5>
                    </div>
                    <div class="modal-body text-center pt-1">
                        <p class="text-muted mb-4"><?= htmlspecialchars($mensaje_modal) ?></p>
                        <button onclick="window.location.href='clientes.php'"
                            class="btn btn-outline-info rounded-pill px-4">
                            <i class="bi bi-check-circle me-1"></i> Aceptar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            new bootstrap.Modal(document.getElementById("modalResultadoEditar")).show();
        });
        </script>
        <?php endif; ?>


        <!-- FOOTER -->
        <?php require_once __DIR__ . '/footer.php'; ?>
        <!-- FOOTER END -->

    </div>

    <!-- BACK-TO-TOP -->
    <a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>

    <!-- JQUERY JS -->
    <script src="<?php echo $url; ?>/panel/assets/js/jquery.min.js"></script>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sticky js -->
    <script src="<?php echo $url; ?>/panel/assets/js/sticky.js"></script>

    <!-- SIDEBAR JS -->
    <script src="<?php echo $url; ?>/panel/assets/plugins/sidebar/sidebar.js"></script>

    <!-- Perfect SCROLLBAR JS-->
    <script src="<?php echo $url; ?>/panel/assets/plugins/p-scroll/perfect-scrollbar.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/p-scroll/pscroll.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/p-scroll/pscroll-1.js"></script>



    <!-- SIDE-MENU JS-->
    <script src="<?php echo $url; ?>/panel/assets/plugins/sidemenu/sidemenu.js"></script>

    <!-- TypeHead js -->
    <script src="<?php echo $url; ?>/panel/assets/plugins/bootstrap5-typehead/autocomplete.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/js/typehead.js"></script>


    <!-- Color Theme js -->
    <script src="<?php echo $url; ?>/panel/assets/js/themeColors.js"></script>

    <!-- CUSTOM JS -->
    <script src="<?php echo $url; ?>/panel/assets/js/custom.js"></script>

    <?php if ($mostrar_modal): ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalResultado = new bootstrap.Modal(document.getElementById('modalResultado'));
        modalResultado.show();
    });
    </script>
    <?php endif; ?>

    <?php if ($mostrar_modal_cliente): ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalCliente = new bootstrap.Modal(document.getElementById('modalResultadoCliente'));
        modalCliente.show();
    });
    </script>
    <?php endif; ?>

</body>

</html>