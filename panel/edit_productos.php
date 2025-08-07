<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];
$id_producto = $_GET['id'] ?? $_POST['id_producto'] ?? null;

if (!$id_producto) {
    $_SESSION['mensaje'] = "ID de producto no proporcionado.";
    header("Location: productos.php");
    exit;
}

$producto = $database->get("productos", "*", ["id_producto" => $id_producto]);

if (!$producto) {
    $_SESSION['mensaje'] = "Producto no encontrado.";
    header("Location: productos.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    $nombre         = trim($_POST['nombre']);
    $id_categoria   = $_POST['id_categoria'];
    $precio         = $_POST['precio'];
    $stock          = $_POST['stock'];
    $estado         = $_POST['estado'];
    $descripcion    = $_POST['descripcion'] ?? '';
    $desc_ampliado  = $_POST['desc_ampliado'] ?? '';
    $imagen         = $producto['imagen'];

    if (!empty($_FILES['imagen']['name'])) {
        $nombreImagen = uniqid('prod_') . '.' . pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $rutaDestino = __DIR__ . '/../uploads/' . $nombreImagen;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
            $imagen = $nombreImagen;
        }
    }

    $actualizado = $database->update("productos", [
        "id_categoria"   => $id_categoria,
        "nombre"         => $nombre,
        "descripcion"    => $descripcion,
        "precio"         => $precio,
        "stock"          => $stock,
        "imagen"         => $imagen,
        "desc_ampliado"  => $desc_ampliado,
        "estado"         => $estado
    ], [
        "id_producto" => $id_producto
    ]);

    // Guardar mensaje en sesión
    $_SESSION['resultado'] = $actualizado->rowCount() > 0
        ? 'Producto actualizado correctamente.'
        : 'No se realizaron cambios.';

    // Redirigir para evitar resubmit al recargar
    header("Location: edit_productos.php?id=" . $id_producto);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

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

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
                            <h1 class="page-title">Productos</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="panel.php">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <!-- ROW-1 -->
                        <div class="row" id="user-profile">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Editar Productos</h3>
                                    </div>
                                    <div class="card-body">

                                        <form action="edit_productos.php" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="guardar" value="1">
                                            <input type="hidden" name="id_producto" value="<?= $id_producto ?>">
                                            <div class="row g-3">
                                                <!-- Nombre -->
                                                <div class="col-md-6">
                                                    <label for="nombre" class="form-label fw-semibold">Nombre del
                                                        Producto</label>
                                                    <input type="text" name="nombre" id="nombre" class="form-control"
                                                        value="<?= htmlspecialchars($producto['nombre']) ?>" required>
                                                </div>

                                                <!-- Categoría -->
                                                <div class="col-md-6">
                                                    <label for="id_categoria"
                                                        class="form-label fw-semibold">Categoría</label>
                                                    <select name="id_categoria" id="id_categoria" class="form-select"
                                                        required>
                                                        <option value="">-- Selecciona una categoría --</option>
                                                        <?php
                                                        $categorias = $database->select("categorias", ["id_categoria", "nombre"], ["estado" => "activo"]);
                                                        foreach ($categorias as $cat) {
                                                            $selected = $cat['id_categoria'] == $producto['id_categoria'] ? 'selected' : '';
                                                            echo "<option value=\"{$cat['id_categoria']}\" $selected>" . htmlspecialchars($cat['nombre']) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <!-- Precio -->
                                                <div class="col-md-4">
                                                    <label for="precio" class="form-label fw-semibold">Precio
                                                        (S/.)</label>
                                                    <input type="number" step="0.01" name="precio" id="precio"
                                                        class="form-control"
                                                        value="<?= htmlspecialchars($producto['precio']) ?>" required>
                                                </div>

                                                <!-- Stock -->
                                                <div class="col-md-4">
                                                    <label for="stock" class="form-label fw-semibold">Stock</label>
                                                    <input type="number" name="stock" id="stock" class="form-control"
                                                        value="<?= htmlspecialchars($producto['stock']) ?>" required>
                                                </div>

                                                <!-- Estado -->
                                                <div class="col-md-4">
                                                    <label for="estado" class="form-label fw-semibold">Estado</label>
                                                    <select name="estado" id="estado" class="form-select" required>
                                                        <option value="activo"
                                                            <?= $producto['estado'] == 'activo' ? 'selected' : '' ?>>
                                                            Activo</option>
                                                        <option value="inactivo"
                                                            <?= $producto['estado'] == 'inactivo' ? 'selected' : '' ?>>
                                                            Inactivo</option>
                                                    </select>
                                                </div>

                                                <!-- Imagen -->
                                                <div class="col-md-6">
                                                    <label for="imagen" class="form-label fw-semibold">Imagen del
                                                        Producto</label>

                                                    <div class="card border shadow-sm p-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="flex-grow-1">
                                                                <input type="file" name="imagen" id="imagen"
                                                                    class="form-control" accept="image/*">
                                                                <small class="form-text text-muted">Formatos: JPG, PNG,
                                                                    WEBP. Tamaño máximo recomendado: 2MB</small>
                                                            </div>

                                                            <?php
                                                            // Validar si hay imagen local
                                                            $imagen_local = !empty($producto['imagen']) && file_exists(__DIR__ . '/../uploads/' . $producto['imagen']);
                                                            $imagen_url = !$imagen_local && !empty($producto['url_imagen']);
                                                            ?>

                                                            <?php if ($imagen_local): ?>
                                                            <div class="ms-3">
                                                                <img src="../uploads/<?= htmlspecialchars($producto['imagen']) ?>"
                                                                    alt="Imagen actual" class="img-thumbnail border"
                                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                            </div>
                                                            <?php elseif ($imagen_url): ?>
                                                            <div class="ms-3">
                                                                <img src="<?= htmlspecialchars($producto['url_imagen']) ?>"
                                                                    alt="Imagen desde URL" class="img-thumbnail border"
                                                                    style="width: 80px; height: 80px; object-fit: cover;">
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>

                                                        <?php if ($imagen_local): ?>
                                                        <div class="mt-2 text-muted small">
                                                            Imagen actual (archivo):
                                                            <strong><?= htmlspecialchars($producto['imagen']) ?></strong>
                                                        </div>
                                                        <?php elseif ($imagen_url): ?>
                                                        <div class="mt-2 text-muted small">
                                                            Imagen actual (URL): <a
                                                                href="<?= htmlspecialchars($producto['url_imagen']) ?>"
                                                                target="_blank">Ver imagen</a>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <!-- Descripción -->
                                                <div class="col-md-12">
                                                    <label for="descripcion"
                                                        class="form-label fw-semibold">Descripción</label>
                                                    <textarea name="descripcion" id="descripcion" class="form-control"
                                                        rows="3"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
                                                </div>

                                                <!-- Descripción Ampliada -->
                                                <div class="col-md-12">
                                                    <label for="desc_ampliado"
                                                        class="form-label fw-semibold">Descripción Ampliada</label>
                                                    <textarea name="desc_ampliado" id="desc_ampliado"
                                                        class="form-control"
                                                        rows="4"><?= htmlspecialchars($producto['desc_ampliado']) ?></textarea>
                                                </div>

                                                <!-- Botones -->
                                                <div class="col-12 d-flex justify-content-end mt-2">
                                                    <button type="submit" name="guardar" class="btn btn-success">
                                                        <i class="fe fe-save me-1"></i> Guardar Cambios
                                                    </button>
                                                    <a href="productos.php" class="btn btn-secondary ms-2">
                                                        <i class="fe fe-x me-1"></i> Cancelar
                                                    </a>
                                                </div>
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

        <?php if (isset($_SESSION['resultado'])): ?>

        <!-- Modal de Resultado Ejecutivo -->
        <div class="modal fade" id="resultadoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow rounded-4">

                    <!-- Encabezado sin color fuerte, diseño limpio -->
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-dark fw-semibold w-100 text-center">
                            <i class="bi bi-check-circle-fill text-success fs-3 me-2"></i> Actualizado
                        </h5>
                    </div>

                    <!-- Cuerpo con mensaje -->
                    <div class="modal-body text-center pt-1">
                        <p class="text-muted mb-4 fs-6">
                            <?= htmlspecialchars($_SESSION['resultado']) ?>
                        </p>
                        <a href="productos.php" class="btn btn-outline-success rounded-pill px-4">
                            <i class="bi bi-box-arrow-right me-1"></i> Volver a Productos
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = new bootstrap.Modal(document.getElementById('resultadoModal'));
            modal.show();
        });
        </script>
        <?php unset($_SESSION['resultado']); ?>
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



</body>

</html>