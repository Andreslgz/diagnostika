<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
$nombre_usuario = $_SESSION['nombre'] ?? '';

// === Helpers ===
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}
function moverImagenSlider($file)
{
    if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) return [false, null, 'No se subió archivo válido'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    if (!in_array($ext, $permitidas)) return [false, null, 'Extensión no permitida'];
    if ($file['size'] > 5 * 1024 * 1024) return [false, null, 'Archivo supera 5MB'];

    $dir = __DIR__ . '/../uploads/slider/';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);

    $nombre = uniqid('sl_', true) . '.' . $ext;
    $destino = $dir . $nombre;
    if (!move_uploaded_file($file['tmp_name'], $destino)) return [false, null, 'No se pudo mover el archivo'];
    return [true, $nombre, null];
}
function borrarImagenSlider($nombre)
{
    if (!$nombre) return;
    $path = __DIR__ . '/../uploads/slider/' . $nombre;
    if (is_file($path)) @unlink($path);
}

// === Acciones ===
$accion = $_GET['ac'] ?? $_POST['ac'] ?? null;
$sl_id  = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['sl_id']) ? (int)$_POST['sl_id'] : null);

// Listado
$sliders = $database->select("slider", "*", ["ORDER" => ["sl_id" => "DESC"]]);

// Eliminar
if ($accion === 'eliminar' && $sl_id) {
    // Buscar para borrar imagen física
    $row = $database->get("slider", ["sl_img"], ["sl_id" => $sl_id]);
    $del = $database->delete("slider", ["sl_id" => $sl_id]);
    if ($del->rowCount() > 0) {
        borrarImagenSlider($row['sl_img'] ?? null);
        $_SESSION['resultado'] = "Slide eliminado correctamente.";
    } else {
        $_SESSION['resultado'] = "No se pudo eliminar el slide.";
    }
    header("Location: sliders.php");
    exit;
}

// Guardar (crear/editar)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'slider') {
    $sl_tit = trim($_POST['sl_tit'] ?? '');
    $sl_est = $_POST['sl_est'] ?? 'activo';
    $sl_img_nueva = null;

    // Validación mínima
    if ($sl_tit === '') {
        $_SESSION['resultado'] = 'El título es obligatorio.';
        header("Location: sliders.php");
        exit;
    }

    // Si viene imagen, procesar
    if (!empty($_FILES['sl_img']['name'])) {
        [$ok, $nombreGuardado, $err] = moverImagenSlider($_FILES['sl_img']);
        if (!$ok) {
            $_SESSION['resultado'] = 'Error de imagen: ' . $err;
            header("Location: sliders.php");
            exit;
        }
        $sl_img_nueva = $nombreGuardado;
    }

    // Editar
    if (isset($_POST['editar']) && $_POST['editar'] == "1") {
        if (!$sl_id) {
            $_SESSION['resultado'] = 'ID de slider no válido.';
            header("Location: sliders.php");
            exit;
        }
        // Obtener la imagen anterior por si hay que borrarla
        $anterior = $database->get("slider", ["sl_img"], ["sl_id" => $sl_id]);

        $data = [
            "sl_tit" => $sl_tit,
            "sl_est" => $sl_est
        ];
        if ($sl_img_nueva) $data["sl_img"] = $sl_img_nueva;

        try {
            $upd = $database->update("slider", $data, ["sl_id" => $sl_id]);
            if ($upd->rowCount() > 0) {
                if ($sl_img_nueva) borrarImagenSlider($anterior['sl_img'] ?? null);
                $_SESSION['resultado'] = 'Slide actualizado correctamente.';
            } else {
                $_SESSION['resultado'] = 'No se realizaron cambios.';
            }
        } catch (Exception $e) {
            // Si falló DB y subimos imagen nueva, la borramos para no dejar basura
            if ($sl_img_nueva) borrarImagenSlider($sl_img_nueva);
            $_SESSION['resultado'] = 'Error al actualizar: ' . $e->getMessage();
        }
    } else {
        // Crear
        try {
            $ins = $database->insert("slider", [
                "sl_tit" => $sl_tit,
                "sl_img" => $sl_img_nueva, // puede ser null si decides permitir crear sin imagen
                "sl_est" => $sl_est
            ]);
            $_SESSION['resultado'] = $ins->rowCount() > 0
                ? 'Slide agregado correctamente.'
                : 'No se realizaron cambios.';
        } catch (Exception $e) {
            if ($sl_img_nueva) borrarImagenSlider($sl_img_nueva);
            $_SESSION['resultado'] = 'Error al guardar: ' . $e->getMessage();
        }
    }

    header("Location: sliders.php");
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
                            <h1 class="page-title">Sliders</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="panel.php">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Sliders</li>
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
                                                                data-bs-target="#modalAgregarSlider">
                                                                <i class="fe fe-plus me-1"></i> Agregar Slide
                                                            </button>
                                                        </div>

                                                        <table
                                                            class="table border text-nowrap text-md-nowrap table-bordered mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Título</th>
                                                                    <th>Imagen</th>
                                                                    <th>Estado</th>
                                                                    <th class="text-center">Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (!empty($sliders)): ?>
                                                                <?php foreach ($sliders as $s): ?>
                                                                <tr>
                                                                    <td><?= (int)$s['sl_id'] ?></td>
                                                                    <td><?= h($s['sl_tit']) ?></td>
                                                                    <td>
                                                                        <?php if (!empty($s['sl_img'])): ?>
                                                                        <img src="../uploads/slider/<?= h($s['sl_img']) ?>"
                                                                            alt=""
                                                                            style="height:42px;border-radius:6px;">
                                                                        <?php else: ?>
                                                                        <span class="text-muted">Sin imagen</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php if (($s['sl_est'] ?? 'activo') === 'activo'): ?>
                                                                        <span class="badge bg-success">Activo</span>
                                                                        <?php else: ?>
                                                                        <span class="badge bg-secondary">Inactivo</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <div class="d-inline-flex gap-2">
                                                                            <button type="button"
                                                                                class="btn btn-outline-primary btn-sm btn-editar-slider"
                                                                                data-id="<?= (int)$s['sl_id'] ?>"
                                                                                data-tit="<?= h($s['sl_tit']) ?>"
                                                                                data-img="<?= h($s['sl_img']) ?>"
                                                                                data-est="<?= h($s['sl_est']) ?>"
                                                                                title="Editar">
                                                                                <i class="fe fe-edit"></i>
                                                                            </button>

                                                                            <button type="button"
                                                                                class="btn btn-outline-danger btn-sm"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modalConfirmarEliminar"
                                                                                data-id="<?= (int)$s['sl_id'] ?>"
                                                                                data-tit="<?= h($s['sl_tit']) ?>"
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
                                                                        encontraron slides.</td>
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

        <div class="modal fade" id="modalAgregarSlider" tabindex="-1" aria-labelledby="modalAgregarSliderLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow rounded-3">
                    <form id="formNuevoSlider" method="POST" action="sliders.php" enctype="multipart/form-data"
                        novalidate>
                        <input type="hidden" name="form" value="slider">

                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-semibold text-dark" id="modalAgregarSliderLabel">Agregar Slide
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body text-muted">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Título</label>
                                <input type="text" class="form-control" name="sl_tit"
                                    placeholder="Ej. Promoción de verano" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Imagen (jpg, png, webp, gif)</label>
                                <input type="file" class="form-control" name="sl_img"
                                    accept=".jpg,.jpeg,.png,.webp,.gif">
                                <small class="text-muted">Máx 5MB</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Estado</label>
                                <select class="form-select" name="sl_est" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-top justify-content-end">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar slide</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modalEditarSlider" tabindex="-1" aria-labelledby="modalEditarSliderLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow rounded-3">
                    <form id="formEditarSlider" method="POST" action="sliders.php" enctype="multipart/form-data"
                        novalidate>
                        <input type="hidden" name="form" value="slider">
                        <input type="hidden" name="editar" value="1">
                        <input type="hidden" name="sl_id" id="edit_sl_id">

                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-semibold text-dark" id="modalEditarSliderLabel">Editar Slide</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body text-muted">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Título</label>
                                <input type="text" class="form-control" name="sl_tit" id="edit_sl_tit" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Imagen actual</label>
                                <div id="edit_preview_img" class="mb-2"></div>
                                <label class="form-label fw-semibold">Cambiar imagen (opcional)</label>
                                <input type="file" class="form-control" name="sl_img"
                                    accept=".jpg,.jpeg,.png,.webp,.gif">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Estado</label>
                                <select class="form-select" name="sl_est" id="edit_sl_est" required>
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
                    `¿Eliminar el slide <strong>${tit}</strong>?`;
                document.getElementById('btnEliminarConfirmado').href =
                    `sliders.php?ac=eliminar&id=${id}`;
            });
        }

        // Editar
        const modalEditar = new bootstrap.Modal(document.getElementById("modalEditarSlider"));
        document.querySelectorAll(".btn-editar-slider").forEach(button => {
            button.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const tit = this.getAttribute("data-tit") || '';
                const img = this.getAttribute("data-img") || '';
                const est = this.getAttribute("data-est") || 'activo';

                document.getElementById("edit_sl_id").value = id;
                document.getElementById("edit_sl_tit").value = tit;
                document.getElementById("edit_sl_est").value = est;

                const cont = document.getElementById("edit_preview_img");
                cont.innerHTML = img ?
                    `<img src="../uploads/slider/${img}" alt="" style="height:60px;border-radius:6px;">` :
                    `<span class="text-muted">Sin imagen</span>`;

                modalEditar.show();
            });
        });
    });
    </script>

</body>

</html>