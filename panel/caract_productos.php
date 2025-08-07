<?php
session_start();
require_once __DIR__ . '/../includes/db.php'; // Aquí se instancia $database (Medoo)
require_once __DIR__ . '/config.php';

$id_producto = $_REQUEST['id_producto'] ?? null;

if (!$id_producto) {
    die("ID de producto no proporcionado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $data = [
        "id_producto"            => $id_producto,
        "marca"                  => $_POST['marca'] ?? '',
        "aplicacion"             => $_POST['aplicacion'] ?? '',
        "software_type"          => $_POST['software_type'] ?? '',
        "system"                 => $_POST['system'] ?? '',
        "columna1"               => $_POST['columna1'] ?? '',
        "anio"                   => $_POST['anio'] ?? '',
        "on_highway"             => $_POST['on_highway'] ?? '',
        "off_highway"            => $_POST['off_highway'] ?? '',
        "precio_standar"         => $_POST['precio_standar'] ?? 0,
        "precio_medium"          => $_POST['precio_medium'] ?? 0,
        "precio_min"             => $_POST['precio_min'] ?? 0,
        "tiempo_instalacion"     => $_POST['tiempo_instalacion'] ?? '',
        "tamano_archivo_gb"      => $_POST['tamano_archivo_gb'] ?? '',
        "espacio"                => $_POST['espacio'] ?? '',
        "gb"                     => $_POST['espaciogb'] ?? '',
        "computer_requirements"  => $_POST['computer_requirements'] ?? '',
        "complejidad"            => $_POST['complejidad'] ?? '',
        "valoracion_cliente"     => $_POST['valoracion_cliente'] ?? '',
        "database_language"      => $_POST['database_language'] ?? '',
        "implicaciones"          => $_POST['implicaciones'] ?? '',
        "description_en"         => $_POST['description_en'] ?? '',
        "supported"              => $_POST['supported'] ?? '',
        "compatible_interface"   => $_POST['compatible_interface'] ?? '',
        "powerapps_id"           => $_POST['powerapps_id'] ?? '',
        "top_sell"               => $_POST['top_sell'] ?? ''
    ];

    $existe = $database->has("caracteristicas_productos", [
        "id_producto" => $id_producto
    ]);

    if ($existe) {
        $resultado = $database->update("caracteristicas_productos", $data, [
            "id_producto" => $id_producto
        ]);

        echo json_encode([
            "success" => true,
            "mensaje" => $resultado->rowCount() > 0
                ? "Características actualizadas correctamente."
                : "No se realizaron cambios."
        ]);
    } else {
        $resultado = $database->insert("caracteristicas_productos", $data);

        echo json_encode([
            "success" => $resultado->rowCount() > 0,
            "mensaje" => $resultado->rowCount() > 0
                ? "Características agregadas correctamente."
                : "Error al insertar datos."
        ]);
    }

    exit;
}

// Obtener características existentes
$caracteristicas = $database->get("caracteristicas_productos", "*", [
    "id_producto" => $id_producto
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
    <?php
    $id_producto = $_REQUEST['id_producto'] ?? '';
    ?>

    <div class="page">
        <div class="page-main">

            <div class="row" id="user-profile">
                <div class="col-lg-12">

                    <div class="card">
                        <div class="card-body">
                            <div class="wideget-user mb-2">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">

                                        <form id="formCaracteristicas">
                                            <input type="hidden" name="id_producto"
                                                value="<?= htmlspecialchars($id_producto) ?>">

                                            <div class="row g-3">
                                                <!-- Primera fila -->
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="marca" id="marca"
                                                            placeholder="Marca"
                                                            value="<?= $caracteristicas['marca'] ?? '' ?>" required>
                                                        <label for="marca">Marca</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="aplicacion"
                                                            id="aplicacion" placeholder="Aplicación"
                                                            value="<?= $caracteristicas['aplicacion'] ?? '' ?>">
                                                        <label for="aplicacion">Aplicación</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="software_type"
                                                            id="software_type" placeholder="Tipo de Software"
                                                            value="<?= $caracteristicas['software_type'] ?? '' ?>">
                                                        <label for="software_type">Tipo de Software</label>
                                                    </div>
                                                </div>

                                                <!-- Segunda fila -->
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="system"
                                                            id="system" placeholder="Sistema"
                                                            value="<?= $caracteristicas['system'] ?? '' ?>">
                                                        <label for="system">Sistema</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="columna1"
                                                            id="columna1" placeholder="Columna1"
                                                            value="<?= $caracteristicas['columna1'] ?? '' ?>">
                                                        <label for="columna1">Columna1</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="number" class="form-control" name="anio" id="anio"
                                                            placeholder="Año"
                                                            value="<?= $caracteristicas['anio'] ?? '' ?>">
                                                        <label for="anio">Año</label>
                                                    </div>
                                                </div>

                                                <!-- Tercera fila -->
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="on_highway" id="on_highway">
                                                            <option value="T"
                                                                <?= ($caracteristicas['on_highway'] ?? '') === 'T' ? 'selected' : '' ?>>
                                                                T
                                                            </option>
                                                            <option value="O"
                                                                <?= ($caracteristicas['on_highway'] ?? '') === 'O' ? 'selected' : '' ?>>
                                                                O
                                                            </option>
                                                        </select>
                                                        <label for="on_highway">On-Highway</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="off_highway" id="off_highway">
                                                            <option value="T"
                                                                <?= ($caracteristicas['off_highway'] ?? '') === 'T' ? 'selected' : '' ?>>
                                                                T
                                                            </option>
                                                            <option value="O"
                                                                <?= ($caracteristicas['off_highway'] ?? '') === 'O' ? 'selected' : '' ?>>
                                                                O
                                                            </option>
                                                        </select>
                                                        <label for="off_highway">Off-Highway</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.01" class="form-control"
                                                            name="precio_standar" id="precio_standar"
                                                            placeholder="Precio estándar"
                                                            value="<?= $caracteristicas['precio_standar'] ?? '' ?>">
                                                        <label for="precio_standar">Precio Estándar ($)</label>
                                                    </div>
                                                </div>

                                                <!-- Cuarta fila -->
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.01" class="form-control"
                                                            name="precio_medium" id="precio_medium"
                                                            placeholder="Precio medio"
                                                            value="<?= $caracteristicas['precio_medium'] ?? '' ?>">
                                                        <label for="precio_medium">Precio Medio ($)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.01" class="form-control"
                                                            name="precio_min" id="precio_min"
                                                            placeholder="Precio mínimo"
                                                            value="<?= $caracteristicas['precio_min'] ?? '' ?>">
                                                        <label for="precio_min">Precio Mínimo ($)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control"
                                                            name="tiempo_instalacion" id="tiempo_instalacion"
                                                            placeholder="Tiempo de instalación"
                                                            value="<?= $caracteristicas['tiempo_instalacion'] ?? '' ?>">
                                                        <label for="tiempo_instalacion">Tiempo de Instalación</label>
                                                    </div>
                                                </div>

                                                <!-- Quinta fila -->
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.1" class="form-control"
                                                            name="tamano_archivo_gb" id="tamano_archivo_gb"
                                                            placeholder="Tamaño de archivo"
                                                            value="<?= $caracteristicas['tamano_archivo_gb'] ?? '' ?>">
                                                        <label for="tamano_archivo_gb">Tamaño Archivo (GB)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="number" step="0.1" class="form-control"
                                                            name="espacio" id="espacio_necesario_gb"
                                                            placeholder="Espacio necesario"
                                                            value="<?= $caracteristicas['espacio'] ?? '' ?>">
                                                        <label for="espacio">Espacio</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" step="0.1" class="form-control" name="gb"
                                                            id="gb" placeholder="Espacio GB"
                                                            value="<?= $caracteristicas['gb'] ?? '' ?>">
                                                        <label for="espacio_gb">GB</label>
                                                    </div>
                                                </div>

                                                <!-- Sexta fila -->
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <textarea class="form-control" name="computer_requirements"
                                                            id="computer_requirements" placeholder="Requisitos PC"
                                                            style="height: 100px"><?= $caracteristicas['computer_requirements'] ?? '' ?></textarea>


                                                        <label for="requisitos_computadora">Requisitos
                                                            Computadora</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="complejidad" id="complejidad">
                                                            <option value="Baja"
                                                                <?= ($caracteristicas['complejidad'] ?? '') === 'Baja' ? 'selected' : '' ?>>
                                                                Baja
                                                            </option>
                                                            <option value="Media"
                                                                <?= ($caracteristicas['complejidad'] ?? '') === 'Media' ? 'selected' : '' ?>>
                                                                Media
                                                            </option>
                                                            <option value="Alta"
                                                                <?= ($caracteristicas['complejidad'] ?? '') === 'Alta' ? 'selected' : '' ?>>
                                                                Alta
                                                            </option>
                                                        </select>
                                                        <label for="complejidad">Complejidad</label>
                                                    </div>
                                                </div>

                                                <!-- Séptima fila -->
                                                <div class="col-md-4">
                                                    <div class="form-floating">


                                                        <select class="form-select" name="valoracion_cliente"
                                                            id="valoracion_cliente">
                                                            <option value="Baja"
                                                                <?= ($caracteristicas['valoracion_cliente'] ?? '') === 'Baja' ? 'selected' : '' ?>>
                                                                Baja
                                                            </option>
                                                            <option value="Media"
                                                                <?= ($caracteristicas['valoracion_cliente'] ?? '') === 'Media' ? 'selected' : '' ?>>
                                                                Media
                                                            </option>
                                                            <option value="Alta"
                                                                <?= ($caracteristicas['valoracion_cliente'] ?? '') === 'Alta' ? 'selected' : '' ?>>
                                                                Alta
                                                            </option>
                                                        </select>

                                                        <label for="valoracion_cliente">Valoración Cliente</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="database_language"
                                                            id="database_language" placeholder="Idioma de base de datos"
                                                            value="<?= $caracteristicas['database_language'] ?? '' ?>">
                                                        <label for="database_language">Idioma Base Datos</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="implicaciones"
                                                            id="implicaciones" placeholder="Implicaciones"
                                                            value="<?= $caracteristicas['implicaciones'] ?? '' ?>">
                                                        <label for="implicaciones">Implicaciones</label>
                                                    </div>
                                                </div>

                                                <!-- Octava fila -->
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <textarea class="form-control" name="description_en"
                                                            id="description_en" placeholder="Descripción en inglés"
                                                            style="height: 100px"><?= $caracteristicas['description_en'] ?? '' ?></textarea>
                                                        <label for="description_en">Descripción (EN)</label>
                                                    </div>
                                                </div>

                                                <!-- Novena fila -->
                                                <div class="col-md-12">
                                                    <div class="form-floating">
                                                        <textarea class="form-control" name="supported" id="supported"
                                                            placeholder="Soporte"
                                                            style="height: 100px"><?= $caracteristicas['supported'] ?? '' ?></textarea>
                                                        <label for="supported">Supported</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-floating">


                                                        <textarea class="form-control" name="compatible_interface"
                                                            id="compatible_interface" placeholder="Interface Comptible"
                                                            style="height: 100px"><?= $caracteristicas['compatible_interface'] ?? '' ?></textarea>

                                                        <label for="compatible_interface">Interfaz Compatible</label>
                                                    </div>
                                                </div>

                                                <!-- Décima fila -->
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="powerapps_id"
                                                            id="powerapps_id" placeholder="PowerApps ID"
                                                            value="<?= $caracteristicas['powerapps_id'] ?? '' ?>">
                                                        <label for="powerapps_id">PowerApps ID</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="top_sell" id="top_sell">
                                                            <option value="sí"
                                                                <?= ($caracteristicas['top_sell'] ?? '') === 'sí' ? 'selected' : '' ?>>
                                                                Sí
                                                            </option>
                                                            <option value="no"
                                                                <?= ($caracteristicas['top_sell'] ?? '') === 'no' ? 'selected' : '' ?>>
                                                                No
                                                            </option>
                                                        </select>
                                                        <label for="top_sell">Top Sell</label>
                                                    </div>
                                                </div>

                                                <!-- Botón de envío -->
                                                <div class="col-12 text-end mt-3">
                                                    <button type="submit" class="btn btn-info">
                                                        <i class="fe fe-save me-1"></i> Guardar
                                                    </button>
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

        </div>
    </div>


    <!-- Modal Ejecutivo Reducido -->
    <div class="modal fade" id="resultadoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content rounded-4 shadow border-0">

                <!-- Cabecera simple -->
                <div class="modal-header bg-info text-white border-0 py-2 px-3 rounded-top-4">
                    <h6 class="modal-title fw-semibold m-0">Resultado</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <!-- Cuerpo del modal reducido -->
                <div class="modal-body text-center p-4">
                    <!-- Ícono visual -->
                    <div id="resultadoIcono" class="mb-3"></div>

                    <!-- Mensaje dinámico -->
                    <div id="resultadoMensaje" class="fs-6 text-muted mb-2"></div>

                    <!-- Botón de acción -->
                    <button type="button" class="btn btn-info btn-sm mt-3 px-4" id="cerrarModalBtn">Aceptar</button>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("formCaracteristicas");
        const resultadoMensaje = document.getElementById("resultadoMensaje");
        const resultadoModalEl = document.getElementById("resultadoModal");

        if (form) {
            form.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(form);

                fetch("caract_productos.php", {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        // 1. Cerrar el modal principal si está abierto
                        const modalCaracteristicasEl = document.getElementById(
                            "modalCaracteristicas");
                        const modalInstance = bootstrap.Modal.getInstance(
                            modalCaracteristicasEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }

                        // 2. Mostrar mensaje en modal resultado tras pequeña pausa
                        setTimeout(() => {
                            resultadoMensaje.innerText = data.mensaje;

                            const resultadoModal = new bootstrap.Modal(
                                resultadoModalEl);
                            resultadoModal.show();

                            // 3. Redireccionar al aceptar
                            const cerrarModalBtn = document
                                .getElementById(
                                    "cerrarModalBtn");
                            cerrarModalBtn.onclick = () => {
                                window.top.location =
                                    "productos.php";
                            };
                        }, 400); // Esperar animación del primer modal
                    })
                    .catch(err => {
                        console.error("Error en la solicitud:", err);
                        resultadoMensaje.innerText =
                            "Error al procesar la solicitud.";

                        const resultadoModal = new bootstrap.Modal(
                            resultadoModalEl);
                        resultadoModal.show();
                    });
            });
        }
    });
    </script>
</body>



</html>