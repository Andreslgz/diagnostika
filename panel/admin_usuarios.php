<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
$nombre_usuario = $_SESSION['nombre'] ?? '';

function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$accion = $_GET['ac'] ?? $_POST['ac'] ?? null;
$adm_id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['adm_id']) ? (int)$_POST['adm_id'] : null);

// Listado
$admins = $database->select("adm_login", "*", ["ORDER" => ["adm_id" => "DESC"]]);

// Eliminar
if ($accion === 'eliminar' && $adm_id) {
    $del = $database->delete("adm_login", ["adm_id" => $adm_id]);
    $_SESSION['resultado'] = $del->rowCount() > 0 ? "Administrador eliminado correctamente." : "No se pudo eliminar.";
    header("Location: admin_usuarios.php");
    exit;
}

// Guardar (crear / editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'admin') {
    $adm_nombre = trim($_POST['adm_nombre'] ?? '');
    $adm_login  = trim($_POST['adm_login'] ?? '');
    $adm_email  = trim($_POST['adm_email'] ?? '');
    $adm_passw  = $_POST['adm_passw'] ?? '';
    $adm_est    = (int)($_POST['adm_est'] ?? 1);

    if ($adm_nombre === '' || $adm_login === '' || $adm_email === '') {
        $_SESSION['resultado'] = 'Nombre, usuario y email son obligatorios.';
        header("Location: admin_usuarios.php");
        exit;
    }

    if (isset($_POST['editar']) && $_POST['editar'] == "1") {
        if (!$adm_id) {
            $_SESSION['resultado'] = 'ID no válido.';
            header("Location: admin_usuarios.php");
            exit;
        }

        $data = [
            "adm_nombre" => $adm_nombre,
            "adm_login"  => $adm_login,
            "adm_email"  => $adm_email,
            "adm_est"    => $adm_est
        ];

        if ($adm_passw !== '') {
            $data["adm_passw"] = password_hash($adm_passw, PASSWORD_DEFAULT);
        }

        try {
            $upd = $database->update("adm_login", $data, ["adm_id" => $adm_id]);
            $_SESSION['resultado'] = $upd->rowCount() > 0 ? 'Administrador actualizado.' : 'No se realizaron cambios.';
        } catch (Exception $e) {
            $_SESSION['resultado'] = 'Error al actualizar: ' . $e->getMessage();
        }
    } else {
        if ($adm_passw === '') {
            $_SESSION['resultado'] = 'La contraseña es obligatoria.';
            header("Location: admin_usuarios.php");
            exit;
        }
        try {
            $ins = $database->insert("adm_login", [
                "adm_nombre" => $adm_nombre,
                "adm_login"  => $adm_login,
                "adm_email"  => $adm_email,
                "adm_passw"  => password_hash($adm_passw, PASSWORD_DEFAULT),
                "adm_est"    => $adm_est
            ]);
            $_SESSION['resultado'] = $ins->rowCount() > 0 ? 'Administrador agregado.' : 'No se realizaron cambios.';
        } catch (Exception $e) {
            $_SESSION['resultado'] = 'Error al guardar: ' . $e->getMessage();
        }
    }

    header("Location: admin_usuarios.php");
    exit;
}

// Eliminar
if ($accion === 'eliminar' && $adm_id) {
    // opcional: evitar que un usuario se elimine a sí mismo
    if ((int)$adm_id === (int)($_SESSION['usuario_id'] ?? 0)) {
        $_SESSION['resultado'] = 'No puedes eliminar tu propio usuario en sesión.';
    } else {
        $del = $database->delete("adm_login", ["adm_id" => $adm_id]);
        $_SESSION['resultado'] = $del->rowCount() > 0
            ? "Administrador eliminado correctamente."
            : "No se pudo eliminar.";
    }
    header("Location: admin_usuarios.php");
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
                            <h1 class="page-title">Administradores</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="panel.php">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Administradores</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <!-- ROW-1 -->
                        <div class="row" id="user-profile">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="wideget-user mb-2">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="row">

                                                        <div class="text-end mb-3">
                                                            <button class="btn btn-success" data-bs-toggle="modal"
                                                                data-bs-target="#modalAgregarAdmin">
                                                                <i class="fe fe-plus me-1"></i> Agregar Administrador
                                                            </button>
                                                        </div>

                                                        <table
                                                            class="table border text-nowrap text-md-nowrap table-bordered mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Nombre</th>
                                                                    <th>Usuario</th>
                                                                    <th>Email</th>
                                                                    <th>Estado</th>
                                                                    <th class="text-center">Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (!empty($admins)): ?>
                                                                <?php foreach ($admins as $a): ?>
                                                                <tr>
                                                                    <td><?= (int)$a['adm_id'] ?></td>
                                                                    <td><?= h($a['adm_nombre']) ?></td>
                                                                    <td><?= h($a['adm_login']) ?></td>
                                                                    <td><?= h($a['adm_email']) ?></td>
                                                                    <td>
                                                                        <?php if ((int)$a['adm_est'] === 1): ?>
                                                                        <span class="badge bg-success">Activo</span>
                                                                        <?php else: ?>
                                                                        <span class="badge bg-secondary">Inactivo</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="d-inline-flex gap-2">
                                                                            <button type="button"
                                                                                class="btn btn-outline-primary btn-sm btn-editar-admin"
                                                                                data-id="<?= (int)$a['adm_id'] ?>"
                                                                                data-nombre="<?= h($a['adm_nombre']) ?>"
                                                                                data-login="<?= h($a['adm_login']) ?>"
                                                                                data-email="<?= h($a['adm_email']) ?>"
                                                                                data-est="<?= (int)$a['adm_est'] ?>"
                                                                                title="Editar">
                                                                                <i class="fe fe-edit"></i>
                                                                            </button>

                                                                            <button type="button"
                                                                                class="btn btn-outline-danger btn-sm"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modalConfirmarEliminar"
                                                                                data-id="<?= (int)$a['adm_id'] ?>"
                                                                                data-nombre="<?= h($a['adm_nombre']) ?>"
                                                                                title="Eliminar">
                                                                                <i class="fe fe-trash-2"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                                <?php else: ?>
                                                                <tr>
                                                                    <td colspan="6" class="text-center text-muted">No se
                                                                        encontraron administradores.</td>
                                                                </tr>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>

                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- COL-END -->
                        </div>
                        <!-- ROW-1 END -->


                    </div>
                    <!-- CONTAINER END -->
                </div>
            </div>
            <!--app-content close-->

        </div>

        <div class="modal fade" id="modalAgregarAdmin" tabindex="-1" aria-labelledby="modalAgregarAdminLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow rounded-3">
                    <form method="POST" action="admin_usuarios.php" novalidate>
                        <input type="hidden" name="form" value="admin">

                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-semibold text-dark" id="modalAgregarAdminLabel">Agregar
                                Administrador</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body text-muted">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" class="form-control" name="adm_nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Usuario</label>
                                <input type="text" class="form-control" name="adm_login" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" name="adm_email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <input type="password" class="form-control" name="adm_passw" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Estado</label>
                                <select class="form-select" name="adm_est" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modalEditarAdmin" tabindex="-1" aria-labelledby="modalEditarAdminLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow rounded-3">
                    <form method="POST" action="admin_usuarios.php" novalidate>
                        <input type="hidden" name="form" value="admin">
                        <input type="hidden" name="editar" value="1">
                        <input type="hidden" name="adm_id" id="edit_adm_id">

                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-semibold text-dark" id="modalEditarAdminLabel">Editar
                                Administrador</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body text-muted">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" class="form-control" name="adm_nombre" id="edit_adm_nombre" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Usuario</label>
                                <input type="text" class="form-control" name="adm_login" id="edit_adm_login" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" name="adm_email" id="edit_adm_email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Contraseña (dejar vacío si no cambia)</label>
                                <input type="password" class="form-control" name="adm_passw">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Estado</label>
                                <select class="form-select" name="adm_est" id="edit_adm_est" required>
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-dark fw-semibold w-100 text-center">
                            <i class="bi bi-exclamation-circle text-danger fs-3 me-2"></i> Confirmar eliminación
                        </h5>
                    </div>
                    <div class="modal-body text-center pt-1">
                        <p class="text-muted fs-6 mb-4" id="textoEliminar"></p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="#" id="btnEliminarConfirmado" class="btn btn-danger btn-sm rounded-pill px-4">
                                <i class="fe fe-trash-2 me-1"></i> Eliminar
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4"
                                data-bs-dismiss="modal">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>






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

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Eliminar
        const modalDel = document.getElementById('modalConfirmarEliminar');
        if (modalDel) {
            modalDel.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                const id = btn.getAttribute('data-id');
                const nombre = btn.getAttribute('data-nombre') || '';
                document.getElementById('textoEliminar').innerHTML =
                    `¿Eliminar al administrador <strong>${nombre}</strong>?`;
                document.getElementById('btnEliminarConfirmado').href =
                    `admin_usuarios.php?ac=eliminar&id=${id}`;
            });
        }

        // Editar
        const modalEditar = new bootstrap.Modal(document.getElementById("modalEditarAdmin"));
        document.querySelectorAll(".btn-editar-admin").forEach(button => {
            button.addEventListener("click", function() {
                document.getElementById("edit_adm_id").value = this.getAttribute("data-id");
                document.getElementById("edit_adm_nombre").value = this.getAttribute(
                    "data-nombre");
                document.getElementById("edit_adm_login").value = this.getAttribute(
                    "data-login");
                document.getElementById("edit_adm_email").value = this.getAttribute(
                    "data-email");
                document.getElementById("edit_adm_est").value = this.getAttribute("data-est");
                modalEditar.show();
            });
        });
    });
    </script>



</body>

</html>