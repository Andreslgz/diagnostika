<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Si se agrega al carrito
if (isset($_POST['agregar_carrito'])) {
    $id = intval($_POST['id_producto']);
    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id]['cantidad']++;
    } else {
        $prod = $database->get('productos', '*', ['id_producto' => $id]);
        if ($prod) {
            $_SESSION['carrito'][$id] = [
                'nombre' => $prod['nombre'],
                'precio' => $prod['precio'],
                'imagen' => $prod['imagen'],
                'cantidad' => 1
            ];
        }
    }
    // ✅ Agregar mensaje de confirmación
    $_SESSION['mensaje_carrito'] = "✅ Producto añadido al carrito correctamente";
    header("Location: index.php");
    exit;
}

$productos = $database->select('productos', '*', [
    "ORDER" => ["id_producto" => "DESC"],
    "LIMIT" => 12
]);

$favoritos_usuario = [];

if (isset($_SESSION['usuario_id'])) {
    $favoritos = $database->select("favoritos", "id_producto", [
        "id_usuario" => $_SESSION['usuario_id']
    ]);

    if ($favoritos) {
        $favoritos_usuario = $favoritos; // ya es array de IDs
    }
}

// Consulta: contar cuántos productos tiene cada marca
$marcas = $database->select(
    "caracteristicas_productos",
    [
        "marca",
        "cantidad" => Medoo\Medoo::raw("COUNT(marca)")
    ],
    [
        "GROUP" => "marca",
        "ORDER" => ["marca" => "ASC"]
    ]
);

// Consulta: contar cuántos productos tiene cada año
$anios = $database->select(
    "caracteristicas_productos",
    [
        "anio",
        "cantidad" => Medoo\Medoo::raw("COUNT(anio)")
    ],
    [
        "GROUP" => "anio",
        "ORDER" => ["anio" => "DESC"] // Ordena de más reciente a más antiguo
    ]
);


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $titulo; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo $url; ?>/styles/main.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
</head>

<body>

    <?php if (!empty($_SESSION['mensaje_carrito'])): ?>
        <div id="alertCarrito"
            class="fixed bottom-6 right-6 flex items-center gap-3 bg-green-600 text-white px-5 py-4 rounded-xl shadow-xl z-50 animate-slide-in">
            <!-- Icono -->
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white/20">
                ✅
            </div>
            <!-- Mensaje -->
            <div class="flex-1">
                <p class="font-semibold text-base">¡Producto añadido!</p>
                <p class="text-sm text-green-100">Se agregó correctamente al carrito.</p>
            </div>
        </div>
        <?php unset($_SESSION['mensaje_carrito']); ?>
    <?php endif; ?>

    <!-- TOP HEADER -->
    <?php require_once __DIR__ . '/../includes/top_header.php'; ?>
    <!-- HEADER - NAVBAR -->
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <main>

        <section class="xl:py-20 py-5 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <div class="grid grid-cols-2 gap-[120px]">
                <div>
                    <!-- FORM REGISTRO -->
                    <div>
                        <div class="flex justify-between w-full mb-5">
                            <h1 class="text-xl font-bold">
                                Register
                            </h1>
                            <div class="flex flex-row gap-2 items-center justify-center">
                                <p class="xl:text-base text-xs text-gray-700">Already have an account?</p>
                                <button data-modal-target="authentication-modal"
                                    data-modal-toggle="authentication-modal" data-active-tab="login"
                                    class="font-semibold xl:text-base text-xs">
                                    Sign In
                                </button>
                            </div>
                        </div>
                        <form action="" class="space-y-4">
                            <div>
                                <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Full
                                    name</label>
                                <div class="relative">
                                    <input name="nombre_completo" type="text"
                                        class="error:border-red-500 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                        placeholder="Juan Pérez" required />
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block mb-2 text-sm xl:text-base font-medium text-gray-900">País</label>
                                    <select name="pais"
                                        class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                        required>
                                        <option value="">Elige un país</option>
                                        <option value="Estados Unidos">Estados Unidos +1</option>
                                        <option value="Perú">Perú +51</option>
                                        <option value="Francia">Francia +33</option>
                                        <option value="Alemania">Alemania +49</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Teléfono</label>
                                    <input name="telefono" type="text"
                                        class="block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="123-456-7890" />
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Email</label>
                                <input name="email" type="email"
                                    class="block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="ejemplo@correo.com" required />
                            </div>
                            <button class="btn-primary w-full py-2.5 font-semibold text-center shadow-lg rounded-lg ">
                                Register
                            </button>
                        </form>
                    </div>
                    <!-- CARRITO -->
                    <div class="mt-10">
                        <div class="flex justify-between">
                            <h2 class="text-xl font-bold">
                                Shopping Cart
                            </h2>
                            <p class="text-gray-700">
                                <?php
                                $total_items = 0;
                                if (!empty($_SESSION['carrito'])) {
                                    foreach ($_SESSION['carrito'] as $item) {
                                        $total_items += $item['cantidad'];
                                    }
                                }
                                echo $total_items . ' selected article' . ($total_items != 1 ? 's' : '');
                                ?>
                            </p>
                        </div>
                        <div class="border border-gray-400 mt-4 rounded-lg p-6">
                            <?php if (!empty($_SESSION['carrito'])): ?>
                                <ul class="space-y-4 max-h-[750px] overflow-y-auto pb-4">
                                    <?php $total = 0; ?>
                                    <?php foreach ($_SESSION['carrito'] as $index => $item): ?>
                                        <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                                        <?php $total += $subtotal; ?>
                                        <li class="flex items-center gap-3" data-item="<?php echo $index; ?>">
                                            <!-- Checkbox al lado izquierdo -->
                                            <div class="flex-shrink-0">
                                                <input type="checkbox" id="producto_<?php echo $index; ?>"
                                                    name="productos_seleccionados[]" value="<?php echo $index; ?>"
                                                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                                    checked>
                                            </div>

                                            <!-- Card del producto -->
                                            <div
                                                class="relative shadow-lg rounded-xl p-4 h-min flex items-center gap-3 border border-gray-100 flex-grow">
                                                <!-- Imagen del producto -->
                                                <div class="flex-shrink-0">
                                                    <div class="w-[80px] h-[80px]">
                                                        <img src="<?php
                                                        echo !empty($item['imagen'])
                                                            ? rtrim($url, '/') . '/uploads/' . htmlspecialchars($item['imagen'], ENT_QUOTES, 'UTF-8')
                                                            : 'https://placehold.co/600x400/png';
                                                        ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>"
                                                            class="w-full h-full object-cover rounded-lg border border-gray-300">
                                                    </div>
                                                </div>

                                                <!-- Contenido derecho -->
                                                <div class="flex-grow flex flex-col justify-between h-full py-1 gap-3">
                                                    <!-- Parte superior: Nombre y botón cerrar -->
                                                    <div class="flex items-start justify-between">
                                                        <h3
                                                            class="font-semibold text-gray-800 text-base uppercase tracking-wide">
                                                            <?php echo htmlspecialchars($item['nombre']); ?>
                                                        </h3>

                                                        <button type="button"
                                                            class="js-remove-item text-gray-600 hover:text-red-600 transition-colors ml-2"
                                                            aria-label="Eliminar <?php echo htmlspecialchars($item['nombre']); ?>"
                                                            title="Eliminar" data-index="<?php echo $index; ?>" <?php if (!empty($item['id_producto'])): ?>
                                                                data-id="<?php echo (int) $item['id_producto']; ?>" <?php endif; ?>>
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <!-- Parte inferior: Precio y controles -->
                                                    <div class="flex items-center justify-between">
                                                        <!-- Precio -->
                                                        <span class="text-lg font-semibold text-gray-800">
                                                            USD. <?php echo number_format($subtotal, 2); ?>
                                                        </span>

                                                        <!-- Controles de cantidad -->
                                                        <div
                                                            class="flex items-center border border-gray-300 rounded-md bg-white">
                                                            <button onclick="updateQuantity(<?php echo $index; ?>, -1)"
                                                                class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors text-lg font-medium">
                                                                −
                                                            </button>
                                                            <span
                                                                class="w-12 text-center text-gray-800 font-medium text-base border-x border-gray-300 py-1">
                                                                <?php echo $item['cantidad']; ?>
                                                            </span>
                                                            <button onclick="updateQuantity(<?php echo $index; ?>, 1)"
                                                                class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors text-lg font-medium">
                                                                +
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                            <?php else: ?>
                                <div class="text-center py-8">
                                    <div class="text-gray-400 mb-4">
                                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 5M7 13l-1.5-5M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Your cart is empty</h3>
                                    <p class="text-gray-600 mb-6">Add some products to get started</p>
                                    <a href="/tienda/"
                                        class="inline-block btn-primary rounded-lg px-6 py-3 text-base font-semibold shadow hover:brightness-110 transition-all duration-200 ease-in-out">
                                        Continue Shopping
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-bold">
                        Order Summary
                    </h2>


                    <div>
                        <div class="flex justify-between items-center mt-4 w-full text-lg">
                            <p>SUBTOTAL</p>
                            <p>USD <?php echo number_format($total, 2); ?></p>
                        </div>
                        <div class="flex justify-between items-center mt-4 w-full text-lg">
                            <p>DISCOUNTS APPLIED</p>
                            <p>- USD <?php echo number_format($total, 2); ?></p>
                        </div>
                        <div class="flex justify-between items-center mt-4 w-full text-lg">
                            <div>
                                <label for="voucher" class="mb-2 block text-sm font-medium text-gray-900 ">
                                    Enter a gift card, voucher or promotional code </label>
                                <div class="flex max-w-md items-center gap-4">
                                    <input type="text" id="voucher"
                                        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 "
                                        placeholder="" required />
                                    <button type="button"
                                        class="flex items-center justify-center rounded-lg bg-blue-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 ">Apply</button>
                                </div>
                            </div>
                            <p>- USD <?php echo number_format($total, 2); ?></p>
                        </div>
                        <hr class="my-6 border-gray-400">
                        <div class="flex justify-between items-center mt-4 w-full text-lg">
                            <p class="font-bold">TOTAL</p>
                            <p>USD <?php echo number_format($total, 2); ?></p>
                        </div>
                        <div class="mt-4 flex items-center gap-4">
                            <img src="/assets/icons/svg/alert.svg" alt="">
                            <p class="text-sm text-gray-400">
                                Total before commissions generated by the following payment methods: PayPal, MoneyGram
                                and Western Union
                            </p>
                        </div>
                        <button class="btn-secondary rounded-lg shadow-lg w-full text-center py-2.5 mt-8 font-bold">
                            PLACE AN ORDER!
                        </button>
                    </div>


                </div>
            </div>
        </section>

    </main>
    <!-- FOOTER -->
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <!-- MODALS -->

    <?php require_once __DIR__ . '/../includes/modal_login_registro.php'; ?>

    <!-- DRAWER -->
    <?php require_once __DIR__ . '/../includes/carrito_home.php'; ?>

    <!-- MODAL PREVISUALIZAR -->
    <?php require_once __DIR__ . '/../includes/modal_previsualizar.php'; ?>

    <div id="alertaFavorito"
        class="hidden fixed top-5 right-5 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50 text-sm"
        role="alert">
        <strong class="font-bold">¡Atención!</strong>
        <span class="block" id="alertaTexto"></span>
    </div>



    <!-- SCRIPTS en este orden -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init();</script>

    <!-- Splide.js DEBE ir antes del modal script -->
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet" />

    <script src="<?php echo $url; ?>/scripts/main.js"></script>
    <script src="<?php echo $url; ?>/scripts/previsualizar_modal.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>


</body>

</html>