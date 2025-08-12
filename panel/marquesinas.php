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
function normalizarUrl($u)
{
    $u = trim((string)$u);
    if ($u === '') return '';
    // Si no tiene esquema, agrega https://
    if (!preg_match('~^https?://~i', $u)) $u = 'https://' . $u;
    return $u;
}

$accion = $_GET['ac'] ?? $_POST['ac'] ?? null;
$mq_id  = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['mq_id']) ? (int)$_POST['mq_id'] : null);

// Listado
$items = $database->select("marquesina", "*", ["ORDER" => ["mq_id" => "DESC"]]);

// Eliminar
if ($accion === 'eliminar' && $mq_id) {
    $del = $database->delete("marquesina", ["mq_id" => $mq_id]);
    $_SESSION['resultado'] = ($del->rowCount() > 0) ? "Mensaje eliminado correctamente." : "No se pudo eliminar.";
    header("Location: marquesinas.php");
    exit;
}

// Crear / Editar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'marquesina') {
    $mq_tit = trim($_POST['mq_tit'] ?? '');
    $mq_url = normalizarUrl($_POST['mq_url'] ?? '');
    $mq_est = $_POST['mq_est'] ?? 'activo';

    if ($mq_tit === '') {
        $_SESSION['resultado'] = 'El título es obligatorio.';
        header("Location: marquesinas.php");
        exit;
    }

    if (isset($_POST['editar']) && $_POST['editar'] == "1") {
        if (!$mq_id) {
            $_SESSION['resultado'] = 'ID no válido.';
            header("Location: marquesinas.php");
            exit;
        }
        try {
            $upd = $database->update("marquesina", [
                "mq_tit" => $mq_tit,
                "mq_url" => $mq_url,
                "mq_est" => $mq_est
            ], ["mq_id" => $mq_id]);

            $_SESSION['resultado'] = $upd->rowCount() > 0 ? 'Mensaje actualizado.' : 'No se realizaron cambios.';
        } catch (Exception $e) {
            $_SESSION['resultado'] = 'Error al actualizar: ' . $e->getMessage();
        }
    } else {
        try {
            $ins = $database->insert("marquesina", [
                "mq_tit" => $mq_tit,
                "mq_url" => $mq_url,
                "mq_est" => $mq_est
            ]);
            $_SESSION['resultado'] = $ins->rowCount() > 0 ? 'Mensaje agregado.' : 'No se realizaron cambios.';
        } catch (Exception $e) {
            $_SESSION['resultado'] = 'Error al guardar: ' . $e->getMessage();
        }
    }

    header("Location: marquesinas.php");
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
                            <h1 class="page-title">Marquesina</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="panel.php">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Marquesina</li>
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
                                                                data-bs-target="#modalAgregarMarquesina">
                                                                <i class="fe fe-plus me-1"></i> Agregar Mensaje
                                                            </button>
                                                        </div>

                                                        <table
                                                            class="table border text-nowrap text-md-nowrap table-bordered mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Título</th>
                                                                    <th>URL</th>
                                                                    <th>Estado</th>
                                                                    <th class="text-center">Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (!empty($items)): ?>
                                                                <?php foreach ($items as $m): ?>
                                                                <tr>
                                                                    <td><?= (int)$m['mq_id'] ?></td>
                                                                    <td><?= h($m['mq_tit']) ?></td>
                                                                    <td>
                                                                        <?php if (!empty($m['mq_url'])): ?>
                                                                        <a href="<?= h($m['mq_url']) ?>" target="_blank"
                                                                            rel="noopener"><?= h($m['mq_url']) ?></a>
                                                                        <?php else: ?>
                                                                        <span class="text-muted">—</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if (($m['mq_est'] ?? 'activo') === 'activo'): ?>
                                                                        <span class="badge bg-success">Activo</span>
                                                                        <?php else: ?>
                                                                        <span class="badge bg-secondary">Inactivo</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="d-inline-flex gap-2">
                                                                            <button type="button"
                                                                                class="btn btn-outline-primary btn-sm btn-editar-mq"
                                                                                data-id="<?= (int)$m['mq_id'] ?>"
                                                                                data-tit="<?= h($m['mq_tit']) ?>"
                                                                                data-url="<?= h($m['mq_url']) ?>"
                                                                                data-est="<?= h($m['mq_est']) ?>"
                                                                                title="Editar">
                                                                                <i class="fe fe-edit"></i>
                                                                            </button>

                                                                            <button type="button"
                                                                                class="btn btn-outline-danger btn-sm"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modalConfirmarEliminar"
                                                                                data-id="<?= (int)$m['mq_id'] ?>"
                                                                                data-tit="<?= h($m['mq_tit']) ?>"
                                                                                title="Eliminar">
                                                                                <i class="fe fe-trash-2"></i>
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                                <?php else: ?>
                                                                <tr>
                                                                    <td colspan="5" class="text-center text-muted">No se
                                                                        encontraron mensajes.</td>
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

        <div class="modal fade" id="modalAgregarMarquesina" tabindex="-1" aria-labelledby="modalAgregarMarquesinaLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow rounded-3">
                    <form method="POST" action="marquesinas.php" novalidate>
                        <input type="hidden" name="form" value="marquesina">

                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-semibold text-dark" id="modalAgregarMarquesinaLabel">Agregar
                                Mensaje</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body text-muted">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Título</label>
                                <input type="text" class="form-control" name="mq_tit" placeholder="Ej. 50% de descuento"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">URL (opcional)</label>
                                <input type="text" class="form-control" name="mq_url"
                                    placeholder="https://tusitio.com/promo">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Estado</label>
                                <select class="form-select" name="mq_est" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-top justify-content-end">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modalEditarMarquesina" tabindex="-1" aria-labelledby="modalEditarMarquesinaLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow rounded-3">
                    <form method="POST" action="marquesinas.php" novalidate>
                        <input type="hidden" name="form" value="marquesina">
                        <input type="hidden" name="editar" value="1">
                        <input type="hidden" name="mq_id" id="edit_mq_id">

                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-semibold text-dark" id="modalEditarMarquesinaLabel">Editar Mensaje
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body text-muted">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Título</label>
                                <input type="text" class="form-control" name="mq_tit" id="edit_mq_tit" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">URL (opcional)</label>
                                <input type="text" class="form-control" name="mq_url" id="edit_mq_url">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Estado</label>
                                <select class="form-select" name="mq_est" id="edit_mq_est" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-top justify-content-end">
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
                const tit = btn.getAttribute('data-tit') || '';
                document.getElementById('textoEliminar').innerHTML =
                    `¿Eliminar el mensaje <strong>${tit}</strong>?`;
                document.getElementById('btnEliminarConfirmado').href =
                    `marquesinas.php?ac=eliminar&id=${id}`;
            });
        }

        // Editar
        const modalEditar = new bootstrap.Modal(document.getElementById("modalEditarMarquesina"));
        document.querySelectorAll(".btn-editar-mq").forEach(button => {
            button.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const tit = this.getAttribute("data-tit") || '';
                const url = this.getAttribute("data-url") || '';
                const est = this.getAttribute("data-est") || 'activo';

                document.getElementById("edit_mq_id").value = id;
                document.getElementById("edit_mq_tit").value = tit;
                document.getElementById("edit_mq_url").value = url;
                document.getElementById("edit_mq_est").value = est;

                modalEditar.show();
            });
        });
    });
    </script>

</body>

</html>