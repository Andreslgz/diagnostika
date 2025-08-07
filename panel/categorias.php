<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
$nombre_usuario = $_SESSION['nombre'];

$categorias = $database->select("categorias", "*", [
    "ORDER" => ["id_categoria" => "DESC"]
]);

$accion = $_GET['ac'] ?? $_POST['ac'] ?? null;
$id_categoria = $_GET['id'] ?? $_POST['id'] ?? null;

if ($accion === 'eliminar' && $id_categoria) {
    // Verificar si existen productos asociados a esta categoría
    $tiene_productos = $database->has("productos", [
        "id_categoria" => $id_categoria
    ]);

    if ($tiene_productos) {
        $_SESSION['resultado'] = "No se puede eliminar la categoría porque tiene productos asignados.";
    } else {
        $eliminado = $database->delete("categorias", [
            "id_categoria" => $id_categoria
        ]);

        $_SESSION['resultado'] = $eliminado->rowCount() > 0
            ? "Categoría eliminada correctamente."
            : "Error: No se pudo eliminar la categoría.";
    }

    header("Location: categorias.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre      = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $estado      = $_POST['estado'] ?? 'activo';

    if ($nombre === '') {
        $_SESSION['resultado'] = 'El nombre es obligatorio.';
        header("Location: categorias.php");
        exit;
    }

    // Si viene con bandera 'editar', actualiza
    if (isset($_POST['editar']) && $_POST['editar'] == "1") {
        $id_categoria = $_POST['id_categoria'] ?? null;

        if (!$id_categoria) {
            $_SESSION['resultado'] = 'ID de categoría no válido.';
            header("Location: categorias.php");
            exit;
        }

        // Actualizar categoría
        try {
            $actualizado = $database->update("categorias", [
                "nombre"      => $nombre,
                "descripcion" => $descripcion,
                "estado"      => $estado
            ], [
                "id_categoria" => $id_categoria
            ]);

            $_SESSION['resultado'] = $actualizado->rowCount() > 0
                ? 'Categoría actualizada correctamente.'
                : 'No se realizaron cambios.';
        } catch (Exception $e) {
            $_SESSION['resultado'] = 'Error al actualizar: ' . $e->getMessage();
        }
    } else {
        // Insertar nueva categoría
        try {
            $insertado = $database->insert("categorias", [
                "nombre"      => $nombre,
                "descripcion" => $descripcion,
                "estado"      => $estado
            ]);

            $_SESSION['resultado'] = $insertado->rowCount() > 0
                ? 'Categoría agregada correctamente.'
                : 'No se realizaron cambios en la base de datos.';
        } catch (Exception $e) {
            $_SESSION['resultado'] = 'Error al guardar: ' . $e->getMessage();
        }
    }

    header("Location: categorias.php");
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
                            <h1 class="page-title">Categorias</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="panel.php">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Categorias</li>
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
                                                        <div class="col-lg-12 col-md-12">
                                                            <div class="row g-3 mb-4">
                                                                <div class="text-end mb-3">
                                                                    <div class="text-end mb-3">
                                                                        <button class="btn btn-success"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#modalAgregarCategoria">
                                                                            <i class="fe fe-plus me-1"></i> Agregar
                                                                            Categoría
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                                <table
                                                                    class="table border text-nowrap text-md-nowrap table-bordered mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>ID</th>
                                                                            <th>Nombre</th>
                                                                            <th>Descripción</th>
                                                                            <th>Estado</th>
                                                                            <th class="text-center">Acciones</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php if (count($categorias) > 0): ?>
                                                                        <?php foreach ($categorias as $c): ?>
                                                                        <tr>
                                                                            <td><?= $c['id_categoria'] ?></td>
                                                                            <td><?= htmlspecialchars($c['nombre']) ?>
                                                                            </td>
                                                                            <td><?= htmlspecialchars($c['descripcion']) ?>
                                                                            </td>
                                                                            <td>
                                                                                <?php if ($c['estado'] === 'activo'): ?>
                                                                                <span
                                                                                    class="badge bg-success">Activo</span>
                                                                                <?php else: ?>
                                                                                <span
                                                                                    class="badge bg-secondary">Inactivo</span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <div class="d-inline-flex gap-2">
                                                                                    <button type="button"
                                                                                        class="btn btn-outline-primary btn-sm btn-editar-categoria"
                                                                                        data-id="<?= $c['id_categoria'] ?>"
                                                                                        data-nombre="<?= htmlspecialchars($c['nombre']) ?>"
                                                                                        data-descripcion="<?= htmlspecialchars($c['descripcion']) ?>"
                                                                                        data-estado="<?= $c['estado'] ?>"
                                                                                        title="Editar categoría">
                                                                                        <i class="fe fe-edit"></i>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        class="btn btn-outline-danger btn-sm"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#modalConfirmarEliminar"
                                                                                        data-id="<?= $c['id_categoria'] ?>"
                                                                                        data-nombre="<?= htmlspecialchars($c['nombre']) ?>"
                                                                                        title="Eliminar">
                                                                                        <i class="fe fe-trash-2"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <?php endforeach; ?>
                                                                        <?php else: ?>
                                                                        <tr>
                                                                            <td colspan="5"
                                                                                class="text-center text-muted">No se
                                                                                encontraron categorías.</td>
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



        <!-- Modal para Agregar Categoría -->
        <div class="modal fade" id="modalAgregarCategoria" tabindex="-1" aria-labelledby="modalAgregarCategoriaLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow rounded-3">

                    <form id="formNuevaCategoria" method="POST" action="categorias.php" novalidate>

                        <!-- Header -->
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-semibold text-dark" id="modalAgregarCategoriaLabel">
                                Agregar Categoría
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body text-muted">
                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="nombre_categoria" class="form-label text-muted fw-semibold">Nombre</label>
                                <input type="text" class="form-control rounded-3 border-light-subtle shadow-none"
                                    id="nombre_categoria" name="nombre" placeholder="Ej. Electrónica"
                                    style="background-color: #f9fafb;" required>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">
                                <label for="descripcion_categoria"
                                    class="form-label text-muted fw-semibold">Descripción</label>
                                <textarea class="form-control rounded-3 border-light-subtle shadow-none"
                                    id="descripcion_categoria" name="descripcion" placeholder="Descripción opcional"
                                    rows="3" style="background-color: #f9fafb;"></textarea>
                            </div>

                            <!-- Estado -->
                            <div class="mb-3">
                                <label for="estado_categoria" class="form-label fw-semibold text-dark">Estado</label>
                                <select class="form-select rounded-3 border-light-subtle px-3 py-2 shadow-sm"
                                    id="estado_categoria" name="estado"
                                    style="background-color: #f9fafb; min-height: 45px;" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer border-top justify-content-end">
                            <button type="button" class="btn"
                                style="background-color: #00CFFF; color: white; border-radius: 6px;"
                                data-bs-dismiss="modal">
                                Cerrar
                            </button>
                            <button type="submit" name="guardar_categoria" class="btn"
                                style="background-color:#1C00FF; color: white; border-radius: 6px;">
                                Guardar categoría
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>


        <?php if (!empty($_SESSION['resultado'])): ?>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalResultado'));
            modal.show();
        });
        </script>

        <div class="modal fade" id="modalResultado" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-dark fw-semibold w-100 text-center">
                            <i class="bi bi-check-circle-fill text-success fs-3 me-2"></i> Confirmación
                        </h5>
                    </div>
                    <div class="modal-body text-center pt-1">
                        <p class="text-muted mb-4 fs-6"><?= htmlspecialchars($_SESSION['resultado']) ?></p>
                        <a href="categorias.php" class="btn btn-outline-success rounded-pill px-4">
                            <i class="bi bi-box-arrow-right me-1"></i> Aceptar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['resultado']); ?>
        <?php endif; ?>

        <!-- Modal de Confirmación para Eliminar Categoría -->
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

        <!-- Modal Editar Categoría -->
        <div class="modal fade" id="modalEditarCategoria" tabindex="-1" aria-labelledby="modalEditarCategoriaLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow rounded-3">

                    <form id="formEditarCategoria" method="POST" action="categorias.php" novalidate>
                        <!-- Campo oculto para ID -->
                        <input type="hidden" name="id_categoria" id="edit_id_categoria">
                        <input type="hidden" name="editar" value="1">

                        <!-- Header -->
                        <div class="modal-header border-bottom">
                            <h5 class="modal-title fw-semibold text-dark" id="modalEditarCategoriaLabel">
                                Editar Categoría
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>

                        <!-- Body -->
                        <div class="modal-body text-muted">
                            <!-- Nombre -->
                            <div class="mb-3">
                                <label for="edit_nombre_categoria"
                                    class="form-label text-muted fw-semibold">Nombre</label>
                                <input type="text" class="form-control rounded-3 border-light-subtle shadow-none"
                                    id="edit_nombre_categoria" name="nombre" placeholder="Ej. Electrónica"
                                    style="background-color: #f9fafb;" required>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">
                                <label for="edit_descripcion_categoria"
                                    class="form-label text-muted fw-semibold">Descripción</label>
                                <textarea class="form-control rounded-3 border-light-subtle shadow-none"
                                    id="edit_descripcion_categoria" name="descripcion"
                                    placeholder="Descripción opcional" rows="3"
                                    style="background-color: #f9fafb;"></textarea>
                            </div>

                            <!-- Estado -->
                            <div class="mb-3">
                                <label for="edit_estado_categoria"
                                    class="form-label fw-semibold text-dark">Estado</label>
                                <select class="form-select rounded-3 border-light-subtle px-3 py-2 shadow-sm"
                                    id="edit_estado_categoria" name="estado"
                                    style="background-color: #f9fafb; min-height: 45px;" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer border-top justify-content-end">
                            <button type="button" class="btn"
                                style="background-color: #00CFFF; color: white; border-radius: 6px;"
                                data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" name="guardar_edicion" class="btn"
                                style="background-color: #1C00FF; color: white; border-radius: 6px;">
                                Guardar cambios
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>


        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById('modalConfirmarEliminar');
            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nombre = button.getAttribute('data-nombre');
                const texto =
                    `¿Estás seguro de que deseas eliminar la categoría <strong>${nombre}</strong>?`;

                document.getElementById('textoEliminar').innerHTML = texto;
                document.getElementById('btnEliminarConfirmado').href =
                    `categorias.php?ac=eliminar&id=${id}`;
            });
        });
        </script>

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
        // Detectar si existe un mensaje de resultado desde PHP
        const resultadoModalEl = document.getElementById("modalResultado");
        const resultadoBtn = document.getElementById("btnResultado");

        if (resultadoModalEl) {
            const modal = new bootstrap.Modal(resultadoModalEl);
            modal.show();

            if (resultadoBtn) {
                resultadoBtn.addEventListener("click", function() {
                    modal.hide();
                    // window.location.href = "categorias.php"; // Redirecciona para limpiar parámetros
                });
            }
        }
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const modalEditar = new bootstrap.Modal(document.getElementById("modalEditarCategoria"));

        document.querySelectorAll(".btn-editar-categoria").forEach(button => {
            button.addEventListener("click", function() {
                const id = this.getAttribute("data-id");
                const nombre = this.getAttribute("data-nombre") || '';
                const descripcion = this.getAttribute("data-descripcion") || '';
                const estado = this.getAttribute("data-estado") || 'activo';

                // Asignar valores al formulario
                document.getElementById("edit_id_categoria").value = id;
                document.getElementById("edit_nombre_categoria").value = nombre;
                document.getElementById("edit_descripcion_categoria").value = descripcion;
                document.getElementById("edit_estado_categoria").value = estado;

                modalEditar.show();
            });
        });
    });
    </script>

</body>

</html>