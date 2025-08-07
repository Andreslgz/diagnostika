<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';


if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
$nombre_usuario = $_SESSION['nombre'];

$total_usuarios = $database->count("usuarios");

if ($total_usuarios === false) {
    echo "Error al contar usuarios.";
}

function contar_ordenes_por_estado($estado, $database)
{
    $cantidad = $database->count("ordenes", [
        "estado" => $estado
    ]);

    if ($cantidad === false) {
        // Puedes lanzar una excepción o devolver -1 según lo que prefieras
        echo "Error al contar órdenes con estado '$estado'.";
        return 0;
    }

    return $cantidad;
}

$estado = 'pagado';
$cantidad_ventas_positivas = contar_ordenes_por_estado($estado, $database);

$estado_i = 'pendiente';
$cantidad_ventas_inconclusas = contar_ordenes_por_estado($estado_i, $database);

$total_pagado = $database->sum("ordenes", "total", [
    "estado" => "pagado"
]);

$total_pagado = $total_pagado ?? 0;
$monto_total_ganado = number_format($total_pagado, 2);

$meses = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
$ventas_pagadas = [];
$ventas_pendientes = [];

foreach ($meses as $mes) {
    // Ventas pagadas
    $pagado = $database->sum("ordenes", "total", [
        "estado" => "pagado",
        "fecha[~]" => "-$mes-"
    ]);
    $ventas_pagadas[] = is_numeric($pagado) ? $pagado : 0;

    // Ventas pendientes
    $pendiente = $database->sum("ordenes", "total", [
        "estado" => "pendiente",
        "fecha[~]" => "-$mes-"
    ]);
    $ventas_pendientes[] = is_numeric($pendiente) ? $pendiente : 0;
}

$json_pagadas = json_encode($ventas_pagadas);
$json_pendientes = json_encode($ventas_pendientes);


$estado = $_GET['estado'] ?? 'todos';

// Condición base
$where = [
    "ORDER" => ["o.fecha" => "DESC"],
    "LIMIT" => 10
];

// Agregar condición por estado si no es "todos"
if ($estado !== 'todos') {
    $where["o.estado"] = $estado;
}

$ordenes = $database->select("ordenes(o)", [
    "[>]productos(p)" => ["o.id_producto" => "id_producto"],
    "[>]usuarios(u)"  => ["o.id_usuario" => "id_usuario"]
], [
    "o.id_orden",
    "o.fecha",
    "o.total",
    "o.metodo_pago",
    "o.estado",
    "p.nombre(producto)",
    "p.imagen",
    "u.nombre(cliente)"
], $where);

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
                            <h1 class="page-title">Bienvenido(a)
                                <?php echo $nombre_usuario; ?>
                            </h1>
                            <div>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="javascript:void(0)">Inicio</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                </ol>
                            </div>
                        </div>
                        <!-- PAGE-HEADER END -->

                        <!-- ROW-1 -->
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                                        <div class="card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="mt-2">
                                                        <h6 class="">Total Usuarios</h6>
                                                        <h2 class="mb-0 number-font">
                                                            <?php echo $total_usuarios; ?>
                                                        </h2>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <div class="chart-wrapper mt-1">
                                                            <canvas id="saleschart"
                                                                class="h-8 w-9 chart-dropshadow"></canvas>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                                        <div class="card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="mt-2">
                                                        <h6 class="">Total Ordener Pagados</h6>
                                                        <h2 class="mb-0 number-font">
                                                            <?php echo $cantidad_ventas_positivas; ?>
                                                        </h2>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <div class="chart-wrapper mt-1">
                                                            <canvas id="leadschart"
                                                                class="h-8 w-9 chart-dropshadow"></canvas>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                                        <div class="card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="mt-2">
                                                        <h6 class="">Total Ordenes Pendientes</h6>
                                                        <h2 class="mb-0 number-font">
                                                            <?php echo $cantidad_ventas_inconclusas; ?>
                                                        </h2>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <div class="chart-wrapper mt-1">
                                                            <canvas id="profitchart"
                                                                class="h-8 w-9 chart-dropshadow"></canvas>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xl-3">
                                        <div class="card overflow-hidden">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="mt-2">
                                                        <h6 class="">Monto Total Ganado</h6>
                                                        <h2 class="mb-0 number-font">S/.
                                                            <?php echo $monto_total_ganado; ?>
                                                        </h2>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <div class="chart-wrapper mt-1">
                                                            <canvas id="costchart"
                                                                class="h-8 w-9 chart-dropshadow"></canvas>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ROW-1 END -->

                        <!-- ROW-2 -->
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Analisis de ventas</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex mx-auto text-center justify-content-center mb-4">
                                            <div class="d-flex text-center justify-content-center me-3"><span
                                                    class="dot-label bg-primary my-auto"></span>Ordenes Pagadas</div>
                                            <div class="d-flex text-center justify-content-center"><span
                                                    class="dot-label bg-secondary my-auto"></span>Ordenes Pendientes
                                            </div>
                                        </div>
                                        <div class="chartjs-wrapper-demo">
                                            <canvas id="transactions" class="chart-dropshadow"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- COL END -->

                        </div>
                        <!-- ROW-2 END -->

                        <!-- ROW-4 -->
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title mb-0">Ventas de Productos</h3>
                                    </div>
                                    <div class="card-body pt-4">
                                        <div class="grid-margin">
                                            <div class="">
                                                <div class="panel panel-primary">
                                                    <div class="tab-menu-heading border-0 p-0">
                                                        <div class="tabs-menu1">
                                                            <!-- Tabs -->
                                                            <ul class="nav panel-tabs product-sale">
                                                                <li><a href="?estado=todos"
                                                                        class="<?= $estado === 'todos' ? 'active' : '' ?>">Todos
                                                                        los estados</a></li>
                                                                <li><a href="?estado=pagado"
                                                                        class="<?= $estado === 'pagado' ? 'active text-dark' : 'text-dark' ?>">Pagados</a>
                                                                </li>
                                                                <li><a href="?estado=pendiente"
                                                                        class="<?= $estado === 'pendiente' ? 'active text-dark' : 'text-dark' ?>">Pendientes</a>
                                                                </li>
                                                                <li><a href="?estado=cancelado"
                                                                        class="<?= $estado === 'cancelado' ? 'active text-dark' : 'text-dark' ?>">Cancelados</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="panel-body tabs-menu-body border-0 pt-0">
                                                        <div class="tab-content">
                                                            <div class="tab-pane active" id="tab5">
                                                                <div class="table-responsive">
                                                                    <table id="data-table"
                                                                        class="table table-bordered text-nowrap mb-0">
                                                                        <thead class="border-top">
                                                                            <tr>
                                                                                <th class="bg-transparent border-bottom-0"
                                                                                    style="width: 5%;">Invoice
                                                                                </th>
                                                                                <th
                                                                                    class="bg-transparent border-bottom-0">
                                                                                    Producto</th>
                                                                                <th
                                                                                    class="bg-transparent border-bottom-0">
                                                                                    Cliente</th>
                                                                                <th
                                                                                    class="bg-transparent border-bottom-0">
                                                                                    Fecha</th>
                                                                                <th
                                                                                    class="bg-transparent border-bottom-0">
                                                                                    Monto</th>
                                                                                <th
                                                                                    class="bg-transparent border-bottom-0">
                                                                                    Metodo de Pago</th>
                                                                                <th class="bg-transparent border-bottom-0"
                                                                                    style="width: 10%;">Estado</th>
                                                                                <th class="bg-transparent border-bottom-0"
                                                                                    style="width: 5%;">Acciones
                                                                                </th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <tbody>
                                                                            <?php foreach ($ordenes as $orden): ?>
                                                                            <tr class="border-bottom">
                                                                                <!-- Tracking ID -->
                                                                                <td class="text-center">
                                                                                    <div class="mt-0 mt-sm-2 d-block">
                                                                                        <h6
                                                                                            class="mb-0 fs-14 fw-semibold">
                                                                                            #<?= $orden['id_orden'] ?>
                                                                                        </h6>
                                                                                    </div>
                                                                                </td>

                                                                                <!-- Product -->
                                                                                <td>
                                                                                    <div class="d-flex">
                                                                                        <span class="avatar bradius"
                                                                                            style="background-image: url(<?= $orden['imagen'] ? '../uploads/' . $orden['imagen'] : "$url/panel/assets/images/orders/default.jpg" ?>)">
                                                                                        </span>
                                                                                        <div
                                                                                            class="ms-3 mt-0 mt-sm-2 d-block">
                                                                                            <h6
                                                                                                class="mb-0 fs-14 fw-semibold">
                                                                                                <?= $orden['producto'] ?>
                                                                                            </h6>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>

                                                                                <!-- Customer -->
                                                                                <td>
                                                                                    <div class="d-flex">
                                                                                        <div
                                                                                            class="mt-0 mt-sm-3 d-block">
                                                                                            <h6
                                                                                                class="mb-0 fs-14 fw-semibold">
                                                                                                <?= $orden['cliente'] ?>
                                                                                            </h6>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>

                                                                                <!-- Date -->
                                                                                <td>
                                                                                    <span class="mt-sm-2 d-block">
                                                                                        <?= date('d M Y', strtotime($orden['fecha'])) ?>
                                                                                    </span>
                                                                                </td>

                                                                                <!-- Amount -->
                                                                                <td>
                                                                                    <span
                                                                                        class="fw-semibold mt-sm-2 d-block">
                                                                                        S/.
                                                                                        <?= number_format($orden['total'], 2) ?>
                                                                                    </span>
                                                                                </td>

                                                                                <!-- Payment Mode -->
                                                                                <td>
                                                                                    <div class="d-flex">
                                                                                        <div
                                                                                            class="mt-0 mt-sm-3 d-block">
                                                                                            <h6
                                                                                                class="mb-0 fs-14 fw-semibold">
                                                                                                <?= ucfirst($orden['metodo_pago']) ?>
                                                                                            </h6>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>

                                                                                <!-- Status -->
                                                                                <td>
                                                                                    <div class="mt-sm-1 d-block">
                                                                                        <?php if ($orden['estado'] === 'pagado'): ?>
                                                                                        <span
                                                                                            class="badge bg-success-transparent rounded-pill text-success p-2 px-3">Pagado</span>
                                                                                        <?php elseif ($orden['estado'] === 'pendiente'): ?>
                                                                                        <span
                                                                                            class="badge bg-warning-transparent rounded-pill text-warning p-2 px-3">Pendiente</span>
                                                                                        <?php elseif ($orden['estado'] === 'cancelado'): ?>
                                                                                        <span
                                                                                            class="badge bg-danger-transparent rounded-pill text-danger p-2 px-3">Cancelado</span>
                                                                                        <?php else: ?>
                                                                                        <span
                                                                                            class="badge bg-secondary-transparent rounded-pill text-muted p-2 px-3">
                                                                                            <?= ucfirst($orden['estado']) ?>
                                                                                        </span>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                </td>

                                                                                <!-- Action -->
                                                                                <td>
                                                                                    <div class="g-2">
                                                                                        <a href="editar.php?id=<?= $orden['id_orden'] ?>"
                                                                                            class="btn text-primary btn-sm"
                                                                                            title="Editar">
                                                                                            <span
                                                                                                class="fe fe-edit fs-14"></span>
                                                                                        </a>
                                                                                        <a href="eliminar.php?id=<?= $orden['id_orden'] ?>"
                                                                                            class="btn text-danger btn-sm"
                                                                                            title="Eliminar">
                                                                                            <span
                                                                                                class="fe fe-trash-2 fs-14"></span>
                                                                                        </a>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <?php endforeach; ?>
                                                                        </tbody>

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
                            </div>
                        </div>
                        <!-- ROW-4 END -->
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
    <script src="<?php echo $url; ?>/panel/assets/plugins/bootstrap/js/popper.min.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

    <!-- SPARKLINE JS-->
    <script src="<?php echo $url; ?>/panel/assets/js/jquery.sparkline.min.js"></script>

    <!-- Sticky js -->
    <script src="<?php echo $url; ?>/panel/assets/js/sticky.js"></script>

    <!-- CHART-CIRCLE JS-->
    <script src="<?php echo $url; ?>/panel/assets/js/circle-progress.min.js"></script>

    <!-- PIETY CHART JS-->
    <script src="<?php echo $url; ?>/panel/assets/plugins/peitychart/jquery.peity.min.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/peitychart/peitychart.init.js"></script>

    <!-- SIDEBAR JS -->
    <script src="<?php echo $url; ?>/panel/assets/plugins/sidebar/sidebar.js"></script>

    <!-- Perfect SCROLLBAR JS-->
    <script src="<?php echo $url; ?>/panel/assets/plugins/p-scroll/perfect-scrollbar.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/p-scroll/pscroll.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/p-scroll/pscroll-1.js"></script>

    <!-- INTERNAL CHARTJS CHART JS-->
    <script src="<?php echo $url; ?>/panel/assets/plugins/chart/Chart.bundle.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/chart/rounded-barchart.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/chart/utils.js"></script>


    <!-- INTERNAL Data tables js-->
    <script src="<?php echo $url; ?>/panel/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/datatable/js/dataTables.bootstrap5.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/datatable/dataTables.responsive.min.js"></script>

    <!-- INTERNAL APEXCHART JS -->
    <script src="<?php echo $url; ?>/panel/assets/js/apexcharts.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/apexchart/irregular-data-series.js"></script>

    <!-- INTERNAL Flot JS -->
    <script src="<?php echo $url; ?>/panel/assets/plugins/flot/jquery.flot.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/flot/jquery.flot.fillbetween.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/flot/chart.flot.sampledata.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/flot/dashboard.sampledata.js"></script>

    <!-- INTERNAL Vector js -->
    <script src="<?php echo $url; ?>/panel/assets/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>

    <!-- SIDE-MENU JS-->
    <script src="<?php echo $url; ?>/panel/assets/plugins/sidemenu/sidemenu.js"></script>

    <!-- TypeHead js -->
    <script src="<?php echo $url; ?>/panel/assets/plugins/bootstrap5-typehead/autocomplete.js"></script>
    <script src="<?php echo $url; ?>/panel/assets/js/typehead.js"></script>

    <script>
    function index() {
        'use strict';

        const myCanvas = document.getElementById("transactions");
        myCanvas.height = 330;
        const ctx = myCanvas.getContext("2d");

        // Datos desde PHP
        const ventasPagadas = <?php echo $json_pagadas; ?>;
        const ventasPendientes = <?php echo $json_pendientes; ?>;

        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                datasets: [{
                        label: 'Ventas Pagadas',
                        data: ventasPagadas,
                        backgroundColor: 'rgba(0, 123, 255, 0.05)', // azul muy claro
                        borderColor: 'rgba(0, 123, 255, 0.8)', // azul pastel
                        pointBackgroundColor: 'rgba(0, 123, 255, 0.9)',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        borderWidth: 2,
                        tension: 0.4, // curva más suave
                        fill: true
                    },
                    {
                        label: 'Ventas Pendientes',
                        data: ventasPendientes,
                        backgroundColor: 'rgba(255, 99, 132, 0.05)', // rojo muy claro
                        borderColor: 'rgba(255, 99, 132, 0.8)', // rojo pastel
                        pointBackgroundColor: 'rgba(255, 99, 132, 0.9)',
                        pointBorderColor: '#fff',
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 10,
                        right: 15,
                        bottom: 15,
                        left: 15
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: '#444',
                            font: {
                                size: 13,
                                weight: '500'
                            },
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                let value = context.parsed.y ?? 0;
                                return `${context.dataset.label}: S/. ${value.toFixed(2)}`;
                            }
                        },
                        backgroundColor: '#f9f9f9',
                        titleColor: '#000',
                        bodyColor: '#333',
                        borderColor: '#ccc',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#666',
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#888',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return 'S/. ' + value;
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                }
            }
        });
    }
    </script>

    <!-- Color Theme js -->
    <script src="<?php echo $url; ?>/panel/assets/js/themeColors.js"></script>

    <!-- CUSTOM JS -->
    <script src="<?php echo $url; ?>/panel/assets/js/custom.js"></script>


</body>

</html>