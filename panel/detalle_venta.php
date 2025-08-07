<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];

$id_orden = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_orden <= 0) {
    die("ID de orden no válido.");
}

// Obtener datos de la orden
$orden = $database->get("ordenes", [
    "[>]usuarios" => ["id_usuario" => "id_usuario"],
    "[>]productos" => ["id_producto" => "id_producto"]
], [
    "ordenes.id_orden",
    "ordenes.fecha",
    "ordenes.total",
    "ordenes.estado",
    "ordenes.metodo_pago",
    "usuarios.nombre",
    "usuarios.apellidos",
    "productos.nombre(producto)",
    "productos.precio",
    "productos.descripcion",
    "ordenes.invoice"
], [
    "ordenes.id_orden" => $id_orden
]);

if (!$orden) {
    die("Orden no encontrada.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $nuevo_estado = $_POST['estado'] ?? '';
    $nuevo_comentario = trim($_POST['comentario'] ?? '');

    if (!in_array($nuevo_estado, ['pagado', 'pendiente', 'cancelado'])) {
        $_SESSION['resultado'] = 'Estado inválido.';
        header("Location: detalle_orden.php?id=" . $id_orden);
        exit;
    }

    $actualizado = $database->update("ordenes", [
        "estado" => $nuevo_estado,
        "comentario" => $nuevo_comentario
    ], [
        "id_orden" => $id_orden
    ]);

    $_SESSION['resultado'] = $actualizado->rowCount() > 0
        ? "Orden actualizada correctamente."
        : "No se realizaron cambios.";

    header("Location: detalle_venta.php?id=" . $id_orden);
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
                            <h1 class="page-title">Detalle de Venta - Orden #<?= $orden['id_orden'] ?></h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="panel.php">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Ventas</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <div class="row" id="user-profile">
                            <div class="col-lg-12">
                                <div class="card shadow border-0 rounded-4">
                                    <div class="card-body p-4">

                                        <!-- Encabezado -->
                                        <div class="d-flex justify-content-between align-items-start mb-4">
                                            <div>
                                                <h5 class="fw-bold mb-1">Cliente:</h5>
                                                <p class="mb-0">
                                                    <?= htmlspecialchars($orden['nombre'] . ' ' . $orden['apellidos']) ?>
                                                </p>
                                                <p class="text-muted mb-0"><i class="fe fe-mail me-1"></i>
                                                    <?= htmlspecialchars($orden['email'] ?? '') ?></p>
                                            </div>
                                            <div class="text-end">
                                                <h5 class="fw-bold mb-1">Factura</h5>
                                                <p class="mb-1">Fecha:
                                                    <strong><?= date('d/m/Y', strtotime($orden['fecha'])) ?></strong>
                                                </p>
                                                <p class="mb-1">Código:
                                                    <strong><?= htmlspecialchars($orden['invoice']) ?></strong>
                                                </p>
                                                <span
                                                    class="badge rounded-pill bg-<?= $orden['estado'] === 'pagado' ? 'success' : ($orden['estado'] === 'pendiente' ? 'warning' : 'secondary') ?>">
                                                    <?= ucfirst($orden['estado']) ?>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Acciones -->
                                        <div class="d-flex justify-content-end gap-2 mb-4">
                                            <button class="btn btn-outline-primary rounded-pill"
                                                onclick="window.print()">
                                                <i class="fe fe-printer me-1"></i> Imprimir
                                            </button>
                                            <a href="exportar_pdf.php?id=<?= $orden['id_orden'] ?>" target="_blank"
                                                class="btn btn-outline-danger rounded-pill">
                                                <i class="fe fe-file-text me-1"></i> Exportar PDF
                                            </a>
                                        </div>

                                        <!-- Tabla productos -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Producto</th>
                                                        <th class="text-center">Cantidad</th>
                                                        <th class="text-end">Precio</th>
                                                        <th class="text-end">Sub. Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td><?= htmlspecialchars($orden['producto']) ?></td>
                                                        <td class="text-center">1</td>
                                                        <td class="text-end">$<?= number_format($orden['precio'], 2) ?>
                                                        </td>
                                                        <td class="text-end">$<?= number_format($orden['precio'], 2) ?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4" class="text-end fw-bold">Total</td>
                                                        <td class="text-end fw-bold">
                                                            $<?= number_format($orden['total'], 2) ?> USD</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <hr class="my-4">

                                        <!-- Información adicional -->
                                        <form method="POST" action="detalle_venta.php?id=<?= $orden['id_orden'] ?>">
                                            <div class="row g-4">
                                                <div class="col-md-6 col-lg-3">
                                                    <label for="estado" class="form-label fw-semibold">Estado</label>
                                                    <select name="estado" id="estado" class="form-select" required>
                                                        <option value="">-- Seleccionar --</option>
                                                        <option value="pagado"
                                                            <?= ($orden['estado'] ?? '') === 'pagado' ? 'selected' : '' ?>>
                                                            Pagado</option>
                                                        <option value="pendiente"
                                                            <?= ($orden['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>
                                                            Pendiente</option>
                                                        <option value="cancelado"
                                                            <?= ($orden['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>
                                                            Cancelado</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6 col-lg-3">
                                                    <label class="form-label fw-semibold">Código</label>
                                                    <input type="text" class="form-control bg-light"
                                                        value="<?= htmlspecialchars($orden['invoice']) ?>" disabled>
                                                </div>

                                                <div class="col-md-6 col-lg-3">
                                                    <label class="form-label fw-semibold">Fecha de Pago</label>
                                                    <input type="text" class="form-control bg-light"
                                                        value="<?= date('d/m/Y', strtotime($orden['fecha'])) ?>"
                                                        disabled>
                                                </div>
                                            </div>

                                            <!-- Comentario -->
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <label for="comentario"
                                                        class="form-label fw-semibold">Comentario</label>
                                                    <textarea id="comentario" name="comentario" class="form-control"
                                                        rows="3"
                                                        placeholder="Agrega un comentario..."><?= htmlspecialchars($orden['comentario'] ?? '') ?></textarea>
                                                </div>
                                            </div>

                                            <!-- Botón Guardar -->
                                            <div class="mt-4 text-end">
                                                <button type="submit" name="actualizar" class="btn btn-primary">
                                                    <i class="fe fe-save me-1"></i> Guardar cambios
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- CONTAINER END -->
                </div>
            </div>
            <!--app-content close-->

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

</body>

</html>