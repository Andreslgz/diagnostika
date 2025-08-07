<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];

// Parámetros de filtro
$cliente = $_GET['cliente'] ?? '';
$estado = $_GET['estado'] ?? '';
$fecha_inicio = $_GET['fecha_inicio'] ?? '2025-07-06';
$fecha_fin = $_GET['fecha_fin'] ?? '2025-08-05';
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$por_pagina = 20;
$offset = ($pagina - 1) * $por_pagina;

// --- Construir condiciones ---
$condiciones = [];

if (!empty($cliente)) {
    $condiciones["usuarios.nombre[~]"] = $cliente;
}
if (!empty($estado)) {
    $condiciones["ordenes.estado"] = $estado;
}
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $condiciones["ordenes.fecha[<>]"] = [$fecha_inicio, $fecha_fin];
}

$where = [];
if (!empty($condiciones)) {
    $where['AND'] = $condiciones;
}

// --- Obtener total para paginación ---
$total_registros = $database->count(
    "ordenes",
    [
        "[><]usuarios" => ["id_usuario" => "id_usuario"]
    ],
    "*",  // <-- columna a contar
    [
        "AND" => $condiciones
    ]
);

$total_paginas = ceil($total_registros / $por_pagina);

// --- Obtener ventas paginadas ---
$ventas = $database->select("ordenes", [
    "[>]usuarios" => ["id_usuario" => "id_usuario"]
], [
    "ordenes.id_orden",
    "ordenes.fecha",
    "ordenes.total",
    "ordenes.estado",
    "ordenes.metodo_pago",
    "usuarios.nombre(cliente)"
], [
    "AND" => $condiciones,
    "ORDER" => ["ordenes.fecha" => "DESC"],
    "LIMIT" => [$offset, $por_pagina]
]);
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
                            <h1 class="page-title">Productos</h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="panel.php">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Productos</li>
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

                                                        <form method="GET" class="mb-4">
                                                            <div class="row g-3">
                                                                <!-- Nombre -->
                                                                <div class="col-md-3">
                                                                    <label for="nombre"
                                                                        class="form-label fw-semibold">Nombre</label>
                                                                    <input type="text" name="nombre" id="nombre"
                                                                        class="form-control"
                                                                        value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>"
                                                                        placeholder="Nombre del cliente">
                                                                </div>

                                                                <!-- Apellidos -->
                                                                <div class="col-md-3">
                                                                    <label for="apellidos"
                                                                        class="form-label fw-semibold">Apellidos</label>
                                                                    <input type="text" name="apellidos" id="apellidos"
                                                                        class="form-control"
                                                                        value="<?= htmlspecialchars($_GET['apellidos'] ?? '') ?>"
                                                                        placeholder="Apellidos del cliente">
                                                                </div>

                                                                <!-- Estado -->
                                                                <div class="col-md-2">
                                                                    <label for="estado"
                                                                        class="form-label fw-semibold">Estado</label>
                                                                    <select name="estado" id="estado"
                                                                        class="form-select">
                                                                        <option value="">-- Todos --</option>
                                                                        <option value="pagado"
                                                                            <?= ($_GET['estado'] ?? '') === 'pagado' ? 'selected' : '' ?>>
                                                                            Pagado</option>
                                                                        <option value="pendiente"
                                                                            <?= ($_GET['estado'] ?? '') === 'pendiente' ? 'selected' : '' ?>>
                                                                            Pendiente</option>
                                                                        <option value="cancelado"
                                                                            <?= ($_GET['estado'] ?? '') === 'cancelado' ? 'selected' : '' ?>>
                                                                            Cancelado</option>
                                                                    </select>
                                                                </div>

                                                                <!-- Fecha Desde -->
                                                                <div class="col-md-2">
                                                                    <label for="fecha_inicio"
                                                                        class="form-label fw-semibold">Desde</label>
                                                                    <input type="date" name="fecha_inicio"
                                                                        id="fecha_inicio" class="form-control" required
                                                                        value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
                                                                </div>

                                                                <!-- Fecha Hasta -->
                                                                <div class="col-md-2">
                                                                    <label for="fecha_fin"
                                                                        class="form-label fw-semibold">Hasta</label>
                                                                    <input type="date" name="fecha_fin" id="fecha_fin"
                                                                        class="form-control" required
                                                                        value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
                                                                </div>

                                                                <!-- Botones -->
                                                                <div class="col-12 d-flex justify-content-end mt-2">
                                                                    <div class="d-flex gap-2">
                                                                        <button type="submit" class="btn btn-primary">
                                                                            <i class="fe fe-search me-1"></i> Buscar
                                                                        </button>
                                                                        <a href="ventas.php" class="btn btn-secondary">
                                                                            <i class="fe fe-x me-1"></i> Limpiar
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>

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

                        <div class="row" id="ventas-reporte">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="wideget-user mb-2">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">


                                                    <!-- Tabla de Ventas -->
                                                    <table
                                                        class="table border text-nowrap text-md-nowrap table-bordered mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Cliente</th>
                                                                <th>Fecha</th>
                                                                <th>Total</th>
                                                                <th>Estado</th>
                                                                <th>Método Pago</th>
                                                                <th class="text-center">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($ventas)): ?>
                                                                <?php foreach ($ventas as $venta): ?>
                                                                    <tr>
                                                                        <td><?= $venta['id_orden'] ?></td>
                                                                        <td><?= htmlspecialchars($venta['cliente']) ?></td>
                                                                        <td><?= date('d/m/Y', strtotime($venta['fecha'])) ?>
                                                                        </td>
                                                                        <td>S/. <?= number_format($venta['total'], 2) ?></td>
                                                                        <td>
                                                                            <?php if ($venta['estado'] === 'pagado'): ?>
                                                                                <span class="badge bg-success">Pagado</span>
                                                                            <?php elseif ($venta['estado'] === 'pendiente'): ?>
                                                                                <span
                                                                                    class="badge bg-warning text-dark">Pendiente</span>
                                                                            <?php else: ?>
                                                                                <span class="badge bg-danger">Cancelado</span>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                        <td><?= ucfirst($venta['metodo_pago']) ?></td>
                                                                        <td class="text-center align-middle">
                                                                            <a href="detalle_venta.php?id=<?= $venta['id_orden'] ?>"
                                                                                class="btn btn-outline-info btn-sm"
                                                                                title="Ver Detalle">
                                                                                <i class="fe fe-eye"></i>
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <tr>
                                                                    <td colspan="7" class="text-center text-muted">No se
                                                                        encontraron ventas.</td>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>

                                                    <!-- Paginación -->
                                                    <?php if ($total_paginas > 1): ?>
                                                        <div class="d-flex justify-content-end mt-3">
                                                            <nav>
                                                                <ul class="pagination mb-0">
                                                                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                                                        <li
                                                                            class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                                                                            <a class="page-link"
                                                                                href="?pagina=<?= $i ?>&cliente=<?= urlencode($cliente ?? '') ?>&estado=<?= urlencode($estado ?? '') ?>">
                                                                                <?= $i ?>
                                                                            </a>
                                                                        </li>
                                                                    <?php endfor; ?>
                                                                </ul>
                                                            </nav>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- COL-END -->
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