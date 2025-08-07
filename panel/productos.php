<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];

// ---------- ELIMINACIÓN (solo si se solicita) ----------
$accion = $_GET['ac'] ?? $_POST['ac'] ?? null;
$id     = $_GET['id'] ?? $_POST['id'] ?? null;

if ($accion === 'eliminar' && $id) {
    $eliminado = $database->delete("productos", [
        "id_producto" => $id
    ]);

    $_SESSION['resultado'] = $eliminado->rowCount() > 0
        ? "Producto eliminado correctamente."
        : "Error: No se pudo eliminar el producto.";

    header("Location: productos.php");
    exit;
}

// ---------- FILTROS Y LISTADO ----------
$nombre    = $_GET['nombre']    ?? '';
$categoria = $_GET['categoria'] ?? '';
$estado    = $_GET['estado']    ?? '';

$por_pagina = 15;
$pagina     = isset($_GET['pagina']) ? max((int)$_GET['pagina'], 1) : 1;
$offset     = ($pagina - 1) * $por_pagina;

// Filtros
$filtros = [];

if ($nombre !== '') {
    $filtros["productos.nombre[~]"] = $nombre;
}
if ($categoria !== '') {
    $filtros["productos.id_categoria"] = $categoria;
}
if ($estado !== '') {
    $filtros["productos.estado"] = $estado;
}

$where = !empty($filtros) ? ["AND" => $filtros] : [];

// Total para paginación
$total_productos = $database->select("productos", [
    "[>]categorias" => ["id_categoria" => "id_categoria"]
], "productos.id_producto", $where);

$total = count($total_productos);
$total_paginas = ceil($total / $por_pagina);

// Consulta principal
$productos = $database->select("productos", [
    "[>]categorias" => ["id_categoria" => "id_categoria"]
], [
    "productos.id_producto",
    "productos.nombre",
    "productos.precio",
    "productos.stock",
    "productos.estado",
    "categorias.nombre(categoria)"
], array_merge($where, [
    "ORDER" => ["productos.id_producto" => "DESC"],
    "LIMIT" => [$offset, $por_pagina]
]));

// Categorías activas
$categorias = $database->select("categorias", [
    "id_categoria",
    "nombre"
], [
    "estado" => "activo",
    "ORDER" => ["nombre" => "ASC"]
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

                                                    <form method="GET" action="productos.php">
                                                        <div class="row g-3 mb-4">
                                                            <!-- Nombre del producto -->
                                                            <div class="col-md-4">
                                                                <label for="nombre"
                                                                    class="form-label fw-semibold">Nombre de
                                                                    Producto</label>
                                                                <input type="text" class="form-control" id="nombre"
                                                                    name="nombre" placeholder="Buscar producto..."
                                                                    value="<?= htmlspecialchars($_GET['nombre'] ?? '') ?>">
                                                            </div>

                                                            <!-- Categoría -->
                                                            <div class="col-md-4">
                                                                <label for="categoria"
                                                                    class="form-label fw-semibold">Categoría</label>
                                                                <select class="form-select" id="categoria"
                                                                    name="categoria">
                                                                    <option value="">-- Todas las categorías --</option>
                                                                    <?php foreach ($categorias as $cat): ?>
                                                                        <option value="<?= $cat['id_categoria'] ?>"
                                                                            <?= (isset($_GET['categoria']) && $_GET['categoria'] == $cat['id_categoria']) ? 'selected' : '' ?>>
                                                                            <?= htmlspecialchars($cat['nombre']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>

                                                            <!-- Estado -->
                                                            <div class="col-md-4">
                                                                <label for="estado"
                                                                    class="form-label fw-semibold">Estado</label>
                                                                <select class="form-select" id="estado" name="estado">
                                                                    <option value="">-- Todos --</option>
                                                                    <option value="activo"
                                                                        <?= ($_GET['estado'] ?? '') === 'activo' ? 'selected' : '' ?>>
                                                                        Activo</option>
                                                                    <option value="no_activo"
                                                                        <?= ($_GET['estado'] ?? '') === 'no_activo' ? 'selected' : '' ?>>
                                                                        No Activo</option>
                                                                </select>
                                                            </div>

                                                            <!-- Botones -->
                                                            <div class="col-12 d-flex justify-content-end mt-2">
                                                                <button type="submit" class="btn btn-primary me-2">
                                                                    <i class="fe fe-search me-1"></i> Buscar
                                                                </button>
                                                                <a href="productos.php" class="btn btn-secondary">
                                                                    <i class="fe fe-x me-1"></i> Cancelar
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </form>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- COL-END -->
                        </div>
                        <!-- ROW-1 END -->

                        <div class="row" id="user-profile">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="wideget-user mb-2">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="row g-3 mb-4">
                                                        <div class="col-12 d-flex justify-content-end mt-2">
                                                            <a href="add_productos.php" class="btn btn-success">
                                                                <i class="fe fe-plus me-1"></i> Agregar
                                                            </a>
                                                        </div>

                                                        <table
                                                            class="table border text-nowrap text-md-nowrap table-bordered mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Producto</th>
                                                                    <th>Categoría</th>
                                                                    <th>Precio</th>
                                                                    <th>Stock</th>
                                                                    <th>Estado</th>
                                                                    <th class="text-center">Caracteristicas</th>
                                                                    <th class="text-center">Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (count($productos) > 0): ?>
                                                                    <?php foreach ($productos as $p): ?>
                                                                        <tr>
                                                                            <td><?= $p['id_producto'] ?></td>
                                                                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                                                                            <td><?= htmlspecialchars($p['categoria']) ?>
                                                                            </td>
                                                                            <td>S/. <?= number_format($p['precio'], 2) ?>
                                                                            </td>
                                                                            <td><?= $p['stock'] ?></td>
                                                                            <td>
                                                                                <?php if ($p['estado'] === 'activo'): ?>
                                                                                    <span class="badge bg-success">Activo</span>
                                                                                <?php else: ?>
                                                                                    <span class="badge bg-secondary">No
                                                                                        activo</span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <button class="btn btn-outline-info btn-sm"
                                                                                    title="Ver características"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#modalCaracteristicas"
                                                                                    data-id="<?= $p['id_producto'] ?>">
                                                                                    <i class="fe fe-settings"></i>
                                                                                </button>
                                                                            </td>
                                                                            <td class="text-center align-middle">
                                                                                <div class="d-inline-flex gap-2">
                                                                                    <a href="edit_productos.php?id=<?= $p['id_producto'] ?>"
                                                                                        class="btn btn-outline-primary btn-sm"
                                                                                        title="Editar">
                                                                                        <span class="fe fe-edit"></span>
                                                                                    </a>
                                                                                    <!-- Botón que activa el modal -->
                                                                                    <button type="button"
                                                                                        class="btn btn-outline-danger btn-sm"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#modalConfirmarEliminar"
                                                                                        data-id="<?= $p['id_producto'] ?>"
                                                                                        data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                                                                        title="Eliminar">
                                                                                        <i class="fe fe-trash-2"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php else: ?>
                                                                    <tr>
                                                                        <td colspan="7" class="text-center text-muted">
                                                                            No se
                                                                            encontraron productos.</td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                            </tbody>
                                                        </table>

                                                        <!-- Paginación derecha -->
                                                        <?php if ($total_paginas > 1): ?>
                                                            <div class="d-flex justify-content-end mt-3">
                                                                <nav>
                                                                    <ul class="pagination mb-0">
                                                                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                                                                            <li
                                                                                class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                                                                                <a class="page-link"
                                                                                    href="?pagina=<?= $i ?>&nombre=<?= urlencode($nombre) ?>&categoria=<?= urlencode($categoria) ?>&estado=<?= urlencode($estado) ?>">
                                                                                    <?= $i ?>
                                                                                </a>
                                                                            </li>
                                                                        <?php endfor; ?>
                                                                    </ul>
                                                                </nav>
                                                            </div>
                                                        <?php endif; ?>

                                                        <!-- Modal moderno para Características del Producto -->
                                                        <div class="modal fade" id="modalCaracteristicas" tabindex="-1"
                                                            aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                                <div class="modal-content border-0 shadow-lg rounded-4">
                                                                    <div
                                                                        class="modal-header bg-gradient bg-info text-white rounded-top-4 py-3 d-flex align-items-center justify-content-between">
                                                                        <h5 class="modal-title fw-bold mb-0 d-flex align-items-center"
                                                                            id="modalLabel">
                                                                            <i class="fe fe-settings me-2 fs-4"></i>
                                                                            Características del producto
                                                                        </h5>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-outline-light d-flex align-items-center"
                                                                            data-bs-dismiss="modal">
                                                                            <i class="fe fe-x me-1"></i> Cerrar
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body p-0" id="modalBodyContent"
                                                                        style="height: 80vh;">
                                                                        <iframe id="iframeCaracteristicas" src=""
                                                                            frameborder="0"
                                                                            style="width: 100%; height: 100%; border: none;"></iframe>
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
                            <!-- CONTAINER END -->
                        </div>
                    </div>
                    <!--app-content close-->

                </div>

                <!-- Modal de Confirmación de Eliminación -->
                <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content border-0 shadow rounded-4">

                            <!-- Encabezado con ícono y texto neutro -->
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title text-dark fw-semibold w-100 text-center">
                                    <i class="bi bi-exclamation-circle-fill text-danger fs-3 me-2"></i> ¿Eliminar
                                    producto?
                                </h5>
                            </div>

                            <!-- Cuerpo con pregunta y botones -->
                            <div class="modal-body text-center pt-1">
                                <p class="text-muted mb-4 fs-6">
                                    ¿Estás seguro que deseas eliminar <strong id="nombreProducto"></strong>?
                                </p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="#" id="btnEliminarConfirmado"
                                        class="btn btn-outline-danger rounded-pill px-4">
                                        <i class="bi bi-trash me-1"></i> Sí, eliminar
                                    </a>
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                                        data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <?php if (isset($_SESSION['resultado'])): ?>
                    <!-- Modal de confirmación moderna -->
                    <div class="modal fade" id="modalConfirmarResultado" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content border-0 shadow rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title text-dark fw-semibold w-100 text-center">
                                        <i class="bi bi-check-circle-fill text-success fs-3 me-2"></i> Confirmación
                                    </h5>
                                </div>
                                <div class="modal-body text-center pt-1">
                                    <p class="text-muted mb-4 fs-6"><?= htmlspecialchars($_SESSION['resultado']) ?></p>
                                    <a href="productos.php" class="btn btn-outline-success rounded-pill px-4">
                                        <i class="bi bi-box-arrow-right me-1"></i> Volver a Productos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const modal = new bootstrap.Modal(document.getElementById('modalConfirmarResultado'));
                            modal.show();
                        });
                    </script>
                    <?php
                    // LIMPIAR el mensaje de sesión para evitar mostrarlo nuevamente
                    unset($_SESSION['resultado']);
                    ?>
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

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('modalCaracteristicas');
                    const iframe = document.getElementById('iframeCaracteristicas');

                    modal.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const idProducto = button.getAttribute('data-id');

                        // Mostrar el formulario dentro del iframe
                        iframe.src = 'caract_productos.php?id_producto=' + encodeURIComponent(idProducto);
                    });

                    // Opcional: limpiar iframe al cerrar
                    modal.addEventListener('hidden.bs.modal', function() {
                        iframe.src = '';
                    });
                });
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modalEliminar = document.getElementById('modalConfirmarEliminar');
                    const btnEliminar = document.getElementById('btnEliminarConfirmado');
                    const nombreProducto = document.getElementById('nombreProducto');

                    modalEliminar.addEventListener('show.bs.modal', function(event) {
                        const button = event.relatedTarget;
                        const idProducto = button.getAttribute('data-id');
                        const nombre = button.getAttribute('data-nombre');

                        nombreProducto.textContent = nombre;

                        btnEliminar.setAttribute('href', 'productos.php?ac=eliminar&id=' + idProducto);
                    });
                });
            </script>
</body>

</html>