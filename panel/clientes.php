<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
$nombre_usuario = $_SESSION['nombre'];

// Parámetros de filtro opcionales
$nombre   = $_GET['nombre'] ?? '';
$apellidos   = $_GET['apellidos'] ?? '';
$email    = $_GET['email'] ?? '';
$ciudad   = $_GET['ciudad'] ?? '';
$estado   = $_GET['estado'] ?? '';

// Paginación
$por_pagina = 15;
$pagina     = isset($_GET['pagina']) ? max((int)$_GET['pagina'], 1) : 1;
$offset     = ($pagina - 1) * $por_pagina;

// Construir filtros dinámicos
$filtros = [];

if ($nombre !== '') {
    $filtros["nombre[~]"] = $nombre;
}
if ($apellidos !== '') {
    $filtros["apellidos[~]"] = $apellidos;
}
if ($email !== '') {
    $filtros["email[~]"] = $email;
}
if ($ciudad !== '') {
    $filtros["ciudad[~]"] = $ciudad;
}
if ($estado !== '') {
    $filtros["estado"] = $estado; // Solo si tienes esta columna en la tabla
}

$where = !empty($filtros) ? ["AND" => $filtros] : [];

// Obtener total de resultados para paginación
$total_clientes = $database->count("usuarios", $where);
$total_paginas = ceil($total_clientes / $por_pagina);

// Obtener resultados paginados
$clientes = $database->select("usuarios", [
    "id_usuario",
    "nombre",
    "apellidos",
    "email",
    "telefono",
    "ciudad",
    "fecha_registro",
    "foto_perfil"
], array_merge($where, [
    "ORDER" => ["fecha_registro" => "DESC"],
    "LIMIT" => [$offset, $por_pagina]
]));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_eliminar'])) {
    $id = $_POST['eliminar_id'] ?? null;

    if ($id) {
        // Verificar si existe el cliente
        $cliente = $database->get("usuarios", "*", [
            "id_usuario" => $id
        ]);

        if ($cliente) {
            // Eliminar foto si existe
            if (!empty($cliente['foto_perfil']) && file_exists(__DIR__ . '/../' . $cliente['foto_perfil'])) {
                unlink(__DIR__ . '/../' . $cliente['foto_perfil']);
            }

            // Eliminar cliente
            $eliminado = $database->delete("usuarios", [
                "id_usuario" => $id
            ]);

            $_SESSION['resultado'] = $eliminado->rowCount() > 0
                ? 'Cliente eliminado correctamente.'
                : 'No se pudo eliminar el cliente.';
        } else {
            $_SESSION['resultado'] = 'Cliente no encontrado.';
        }
    } else {
        $_SESSION['resultado'] = 'ID de cliente no válido.';
    }

    header("Location: clientes.php");
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
                            <h1 class="page-title">Clientes</h1>
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
                                <div class="card">
                                    <div class="card-body">
                                        <div class="wideget-user mb-2">
                                            <div class="row">
                                                <div class="col-lg-12 col-md-12">
                                                    <div class="row">
                                                        <form method="GET" action="clientes.php">
                                                            <div class="row g-3 align-items-end">

                                                                <!-- Nombre -->
                                                                <div class="col-md-2">
                                                                    <label for="nombre"
                                                                        class="form-label fw-semibold">Nombre</label>
                                                                    <input type="text" class="form-control" id="nombre"
                                                                        name="nombre" placeholder="Ej. Juan"
                                                                        value="<?= htmlspecialchars($nombre) ?>">
                                                                </div>

                                                                <!-- Nombre -->
                                                                <div class="col-md-3">
                                                                    <label for="apellidos"
                                                                        class="form-label fw-semibold">Apellidos</label>
                                                                    <input type="text" class="form-control"
                                                                        id="apellidos" name="apellidos"
                                                                        placeholder="Ej. Pérez"
                                                                        value="<?= htmlspecialchars($apellidos) ?>">
                                                                </div>

                                                                <!-- Email -->
                                                                <div class="col-md-3">
                                                                    <label for="email"
                                                                        class="form-label fw-semibold">Email</label>
                                                                    <input type="email" class="form-control" id="email"
                                                                        name="email" placeholder="usuario@correo.com"
                                                                        value="<?= htmlspecialchars($email) ?>">
                                                                </div>

                                                                <!-- Ciudad -->
                                                                <div class="col-md-2">
                                                                    <label for="ciudad"
                                                                        class="form-label fw-semibold">Ciudad</label>
                                                                    <input type="text" class="form-control" id="ciudad"
                                                                        name="ciudad" placeholder="Ej. Lima"
                                                                        value="<?= htmlspecialchars($ciudad) ?>">
                                                                </div>

                                                                <!-- Estado (opcional si tienes lógica de activos/inactivos) -->
                                                                <div class="col-md-2">
                                                                    <label for="estado"
                                                                        class="form-label fw-semibold">Estado</label>
                                                                    <select class="form-select" id="estado"
                                                                        name="estado">
                                                                        <option value="">-- Todos --</option>
                                                                        <option value="activo"
                                                                            <?= $estado === 'activo' ? 'selected' : '' ?>>
                                                                            Activo</option>
                                                                        <option value="inactivo"
                                                                            <?= $estado === 'inactivo' ? 'selected' : '' ?>>
                                                                            Inactivo</option>
                                                                    </select>
                                                                </div>

                                                                <!-- Botones -->
                                                                <div class="col-12 d-flex justify-content-end mt-2">
                                                                    <button type="submit" class="btn btn-primary me-2">
                                                                        <i class="fe fe-search me-1"></i> Buscar
                                                                    </button>
                                                                    <a href="clientes.php"
                                                                        class="btn btn-outline-secondary">
                                                                        <i class="fe fe-x me-1"></i> Limpiar
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
                                                            <a href="add_clientes.php" class="btn btn-success">
                                                                <i class="fe fe-plus me-1"></i> Agregar
                                                            </a>
                                                        </div>


                                                        <table
                                                            class="table border text-nowrap text-md-nowrap table-bordered mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Nombre</th>
                                                                    <th>Email</th>
                                                                    <th>Teléfono</th>
                                                                    <th>Ciudad</th>
                                                                    <th>Fecha de Registro</th>
                                                                    <th class="text-center">Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php if (count($clientes) > 0): ?>
                                                                <?php foreach ($clientes as $c): ?>
                                                                <tr>
                                                                    <td><?= $c['id_usuario'] ?></td>
                                                                    <td><?= htmlspecialchars($c['nombre']) ?></td>
                                                                    <td><?= htmlspecialchars($c['email']) ?></td>
                                                                    <td><?= htmlspecialchars($c['telefono']) ?></td>
                                                                    <td><?= htmlspecialchars($c['ciudad']) ?></td>
                                                                    <td><?= date('d/m/Y H:i', strtotime($c['fecha_registro'])) ?>
                                                                    </td>
                                                                    <td class="text-center align-middle">
                                                                        <div class="d-inline-flex gap-2">

                                                                            <a href="edit_clientes.php?id=<?= $c['id_usuario'] ?>"
                                                                                class="btn btn-outline-primary btn-sm"
                                                                                title="Editar">
                                                                                <i class="fe fe-edit"></i>
                                                                            </a>
                                                                            <!-- Botón Eliminar -->
                                                                            <button type="button"
                                                                                class="btn btn-outline-danger btn-sm"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modalConfirmarEliminar"
                                                                                data-id="<?= $c['id_usuario'] ?>"
                                                                                data-nombre="<?= htmlspecialchars($c['nombre'] . ' ' . $c['apellidos']) ?>"
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
                                                                        No se encontraron clientes registrados.
                                                                    </td>
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
                                                                        class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                                                                        <a class="page-link"
                                                                            href="?pagina=<?= $i ?>&nombre=<?= urlencode($nombre) ?>&email=<?= urlencode($email) ?>&ciudad=<?= urlencode($ciudad) ?>&estado=<?= urlencode($estado) ?>">
                                                                            <?= $i ?>
                                                                        </a>
                                                                    </li>
                                                                    <?php endfor; ?>
                                                                </ul>
                                                            </nav>
                                                        </div>
                                                        <?php endif; ?>

                                                        <!-- Modal Confirmar Eliminación -->
                                                        <div class="modal fade" id="modalConfirmarEliminar"
                                                            tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <form method="POST" action="clientes.php">
                                                                    <input type="hidden" name="eliminar_id"
                                                                        id="eliminar_id">
                                                                    <div
                                                                        class="modal-content border-0 shadow rounded-4">
                                                                        <div class="modal-header border-bottom-0">
                                                                            <h5 class="modal-title fw-bold text-danger">
                                                                                <i
                                                                                    class="bi bi-trash-fill me-2"></i>Confirmar
                                                                                eliminación
                                                                            </h5>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p class="mb-0 text-muted">¿Estás seguro de
                                                                                que deseas eliminar al cliente <strong
                                                                                    id="nombre_cliente_eliminar"></strong>?
                                                                            </p>
                                                                        </div>
                                                                        <div class="modal-footer border-top-0">
                                                                            <button type="button"
                                                                                class="btn btn-secondary"
                                                                                data-bs-dismiss="modal">Cancelar</button>
                                                                            <button type="submit"
                                                                                name="confirmar_eliminar"
                                                                                class="btn btn-danger">Eliminar</button>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>

                                                        <script>
                                                        document.addEventListener("DOMContentLoaded", function() {
                                                            const modalEliminar = document.getElementById(
                                                                "modalConfirmarEliminar");
                                                            modalEliminar.addEventListener("show.bs.modal",
                                                                function(event) {
                                                                    const button = event.relatedTarget;
                                                                    const id = button.getAttribute(
                                                                        "data-id");
                                                                    const nombre = button.getAttribute(
                                                                        "data-nombre");

                                                                    document.getElementById("eliminar_id")
                                                                        .value = id;
                                                                    document.getElementById(
                                                                            "nombre_cliente_eliminar")
                                                                        .textContent = nombre;
                                                                });
                                                        });
                                                        </script>

                                                        <!-- Script para cargar ID del cliente a eliminar -->
                                                        <script>
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                            const eliminarModal = document.getElementById(
                                                                'modalConfirmarEliminar');
                                                            eliminarModal.addEventListener('show.bs.modal',
                                                                function(event) {
                                                                    const button = event.relatedTarget;
                                                                    const id = button.getAttribute(
                                                                        'data-id');
                                                                    const enlace = document.getElementById(
                                                                        'btnConfirmarEliminar');
                                                                    enlace.href =
                                                                        `clientes.php?ac=eliminar&id=${id}`;
                                                                });
                                                        });
                                                        </script>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- CONTAINER END -->
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