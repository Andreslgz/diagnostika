<?php
// public/index.php (añadir lógica para mostrar el carrito)
session_start();
require_once __DIR__ . '/includes/db.php';

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
                'cantidad' => 1
            ];
        }
    }
    // ✅ Agregar mensaje de confirmación
    $_SESSION['mensaje_carrito'] = "✅ Producto añadido al carrito correctamente";
    header("Location: index.php");
    exit;
}

$categorias = $database->select('categorias', '*');
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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>cDIAGNOSTIKA DIESEL GLOBAL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles/main.css" />
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
    <section class="bg-black xl:py-1.5 py-0.5 overflow-hidden" id="top-header">
        <div class="marquee-container">
            <p class="text-white font-semibold marquee-text xl:text-base text-xs">
                $50 off your first purchase • Free shipping on orders over $100 • 30%
                discount on selected items • Limited time offer
            </p>
        </div>
    </section>
    <!-- HEADER - NAVBAR -->
    <header>
        <nav class="bg-header border-gray-200 px-4 xl:py-0 py-3 lg:px-6">
            <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-2xl">
                <a href="https://flowbite.com" class="flex items-center">
                    <img src="assets/icons/Logotipo.svg" class="mr-3 h-6 sm:h-9" alt="Flowbite Logo" />
                </a>
                <div class="flex items-center lg:order-2">
                    <form id="search-form" class="hidden mr-3 w-full lg:inline-block">
                        <label for="search-bar" class="mb-2 text-sm font-medium text-gray-900 sr-only">Busca tu
                            producto</label>
                        <div class="relative">
                            <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="search" id="search-bar"
                                class="block py-2 px-4 pl-10 w-full btn-primary text-sm text-gray-900 rounded-full border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Busca tu producto" required />
                        </div>
                    </form>


                    <?php if (!isset($_SESSION['usuario_id'])): ?>
                        <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                            data-active-tab="login"
                            class="bg-black text-white rounded-lg px-3 py-1 text-nowrap xl:mr-3 cursor-pointer xl:text-base text-sm"
                            type="button">
                            Iniciar sesión
                        </button>
                        <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                            data-active-tab="register"
                            class="text-gray-500 border border-solid border-gray-500 rounded-lg px-3 py-1 cursor-pointer hidden lg:inline-block"
                            type="button">
                            Registro
                        </button>

                    <?php endif; ?>

                    <button
                        class="text-white focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm xl:ml-6 ml-4 cursor-pointer"
                        type="button" data-drawer-target="drawer-right-example" data-drawer-show="drawer-right-example"
                        data-drawer-placement="right" aria-controls="drawer-right-example">
                        <div
                            class="btn-secondary rounded-full xl:w-[60px] w-[40px] xl:h-[60px] h-[40px] flex items-center justify-center">
                            <img src="assets/icons/Cart.svg" alt="" class="xl:w-[39px] w-[25px]" />
                        </div>
                    </button>

                    <div class="hidden z-50 my-4 w-48 text-base list-none bg-white rounded divide-y divide-gray-100 shadow"
                        id="language-dropdown">
                        <ul class="py-1" role="none">
                            <li>
                                <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100"
                                    role="menuitem">
                                    <div class="inline-flex items-center">English (US)</div>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100"
                                    role="menuitem">
                                    <div class="inline-flex items-center">Español (ES)</div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <button data-collapse-toggle="mobile-menu-search" type="button"
                        class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                        aria-controls="mobile-menu-search" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1"
                    id="mobile-menu-search">
                    <form class="flex items-center mt-4 lg:hidden">
                        <label for="search-mobile" class="sr-only">Search</label>
                        <div class="relative w-full">
                            <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input type="search" id="search-mobile"
                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                                placeholder="Search for anything..." required />
                        </div>
                        <button type="submit"
                            class="inline-flex items-center p-2.5 ml-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                            Buscar
                        </button>
                    </form>
                    <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-0 lg:mt-0 lg:h-full">
                        <li class="lg:h-full lg:flex lg:items-center btn-secondary xl:py-3">
                            <a href="#"
                                class="block py-2 pr-4 pl-3 border-b border-gray-100 font-semibold text-white lg:px-6 lg:py-5 lg:h-full lg:flex lg:items-center lg:border-0">Inicio</a>
                        </li>
                        <li class="lg:h-full lg:flex lg:items-center xl:py-3">
                            <a href="#"
                                class="block text-gray-600 py-2 pr-4 pl-3 border-b border-gray-100 lg:hover:bg-white lg:border-0 lg:hover:text-blue-700 lg:px-6 lg:py-5 lg:h-full lg:flex lg:items-center">Tienda</a>
                        </li>
                        <li class="lg:h-full lg:flex lg:items-center xl:py-3">
                            <a href="#"
                                class="block text-gray-600 py-2 pr-4 pl-3 border-b border-gray-100 lg:hover:bg-white lg:border-0 lg:hover:text-blue-700 lg:px-6 lg:py-5 lg:h-full lg:flex lg:items-center">Contacto</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <!-- Hero -->
        <section id="image-carousel" class="splide" aria-label="Beautiful Images">
            <div class="splide__track xl:h-[85vh] h-[70vh]">
                <ul class="splide__list">
                    <li class="splide__slide">
                        <img src="assets/images/hero1.jpg" alt="" class="" />
                    </li>
                    <li class="splide__slide">
                        <img src="assets/images/hero2.jpg" alt="" class="" />
                    </li>
                    <li class="splide__slide">
                        <img src="assets/images/hero3.jpg" alt="" class="" />
                    </li>
                </ul>
            </div>
        </section>
        <!-- STATISTICS -->
        <section class="bg-statistics">
            <div class="py-6 px-4 xl:px-0 mx-auto max-w-screen-2xl gap-10 overflow-hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" data-aos="fade-up">
                    <div class="xl:p-4 p-1 rounded-lg flex flex-row xl:gap-4 gap-3 items-center">
                        <img src="assets/icons/banner/icon1.svg" alt="" class="xl:w-auto xl:h-auto w-[25%] h-auto" />
                        <div class="">
                            <p class="font-bold xl:text-3xl text-lg">1 Año</p>
                            <p class="xl:text-lg text-base font-medium text-nowrap xl:mt-0 -mt-2">
                                de garantia
                            </p>
                        </div>
                    </div>
                    <div class="xl:p-4 p-1 rounded-lg flex flex-row xl:gap-4 gap-3 items-center">
                        <img src="assets/icons/banner/icon2.svg" alt="" class="xl:w-auto xl:h-auto w-[25%] h-auto" />
                        <div class="">
                            <p class="font-bold xl:text-3xl text-lg">24/7</p>
                            <p class="xl:text-lg text-base font-medium text-nowrap xl:mt-0 -mt-2">
                                Soporte técnico
                            </p>
                        </div>
                    </div>
                    <div class="xl:p-4 p-1 rounded-lg flex flex-row xl:gap-4 gap-3 items-center">
                        <img src="assets/icons/banner/icon3.svg" alt="" class="xl:w-auto xl:h-auto w-[25%] h-auto" />
                        <div class="">
                            <p class="font-bold xl:text-3xl text-lg">Cobertura</p>
                            <p class="xl:text-lg text-base font-medium text-nowrap xl:mt-0 -mt-2">
                                Global
                            </p>
                        </div>
                    </div>
                    <div class="xl:p-4 p-1 rounded-lg flex flex-row xl:gap-4 gap-3 items-center">
                        <img src="assets/icons/banner/icon4.svg" alt="" class="xl:w-auto xl:h-auto w-[25%] h-auto" />
                        <div class="">
                            <p class="font-bold xl:text-3xl text-lg">123K +</p>
                            <p class="xl:text-lg text-base font-medium text-nowrap xl:mt-0 -mt-2">
                                Instalaciones
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- PRODUCTS -->
        <section data-aos="fade-up">
            <div class="xl:py-20 py-14 px-4 mx-auto max-w-screen-2xl overflow-hidden">
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-10 w-full gap-4 sm:gap-0">
                    <h2 class="uppercase font-extrabold xl:text-3xl lg:text-2xl md:text-xl text-lg">
                        El software más demandado
                    </h2>
                    <button
                        class="btn-primary rounded px-4 sm:px-6 lg:px-8 py-2 uppercase font-bold text-sm sm:text-base xl:text-lg flex items-center gap-2 cursor-pointer hover:underline underline-offset-4 self-start sm:self-auto">
                        Ver Todo
                        <img src="assets/icons/svg/tabler--chevron-right.svg" alt="" />
                    </button>
                </div>
                <div class="mx-auto max-w-screen-xl">
                    <!-- Products Carousel -->
                    <section id="products-carousel" class="splide" aria-label="Featured Products">
                        <div class="splide__track">
                            <ul class="splide__list">
                                <?php foreach ($productos as $prod): ?>
                                    <li
                                        class="splide__slide !pb-4 sm:!pb-6 lg:!pb-10 !pr-2 sm:!pr-4 lg:!pr-6 !pl-2 sm:!pl-4 lg:!pl-6">
                                        <div
                                            class="border border-gray-100 border-solid shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg p-3 sm:p-4 lg:p-6 flex flex-col gap-3 sm:gap-3 h-full">
                                            <div class="flex justify-end -mb-1">
                                                <button type="button" class="favorito-btn p-1 sm:p-2"
                                                    data-id="<?php echo $prod['id_producto']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        fill="<?php echo in_array($prod['id_producto'], $favoritos_usuario) ? 'currentColor' : 'none'; ?>"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-7 h-7 sm:w-7 sm:h-7 transition-all duration-200 <?php echo in_array($prod['id_producto'], $favoritos_usuario) ? 'text-red-600' : 'text-gray-600'; ?>">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6.75 3.75h10.5a.75.75 0 01.75.75v15.375a.375.375 0 01-.6.3L12 16.5l-5.4 3.675a.375.375 0 01-.6-.3V4.5a.75.75 0 01.75-.75z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <img src="<?php echo !empty($prod['imagen']) ? 'uploads/' . $prod['imagen'] : 'https://placehold.co/600x400/png'; ?>"
                                                alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                                                class="w-full h-46 sm:h-40 lg:h-48 object-fit rounded-md" />
                                            <p
                                                class="inline font-semibold text-sm sm:text-base lg:text-xl text-balance leading-tight uppercase">
                                                <?php echo htmlspecialchars($prod['nombre']); ?>
                                            </p>
                                            <p class="inline text-lg sm:text-xl lg:text-2xl uppercase font-bold">
                                                USD <?php echo number_format($prod['precio'], 2); ?>
                                            </p>
                                            <div class="flex flex-col gap-2 sm:gap-3 mt-auto">
                                                <form method="post">
                                                    <input type="hidden" name="id_producto"
                                                        value="<?php echo $prod['id_producto']; ?>">
                                                    <button type="submit" name="agregar_carrito"
                                                        class="btn-secondary inline w-full py-1.5 sm:py-2 rounded-lg uppercase font-semibold text-sm sm:text-base">
                                                        Agregar al carrito
                                                    </button>
                                                </form>
                                                <button
                                                    class="inline border border-gray-400 rounded-lg py-1.5 sm:py-2 uppercase font-semibold text-sm sm:text-base">
                                                    Previsualizar
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </section>
                </div>
                <section class="mx-auto max-w-screen-xl mt-6 grid xl:grid-cols-6 grid-cols-2 xl:gap-6 gap-8 mt-10">
                    <div
                        class="brands-bg rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg">
                        <img src="assets/images/logos/logo1.svg" alt="" />
                    </div>
                    <div
                        class="brands-bg rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg">
                        <img src="assets/images/logos/logo2.svg" alt="" />
                    </div>
                    <div
                        class="brands-bg rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg">
                        <img src="assets/images/logos/logo3.svg" alt="" />
                    </div>
                    <div
                        class="brands-bg rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg">
                        <img src="assets/images/logos/logo4.svg" alt="" />
                    </div>
                    <div
                        class="brands-bg rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg">
                        <img src="assets/images/logos/logo5.svg" alt="" />
                    </div>
                    <div
                        class="brands-bg rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg">
                        <img src="assets/images/logos/logo6.svg" alt="" />
                    </div>
                </section>
            </div>
        </section>
        <!-- BANNERS -->
        <section class="px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <div class="flex flex-row">
                        <div class="btn-secondary w-[30%] h-auto rounded-l-xl p-4">
                            <img src="assets/icons/banner/banner1.svg" alt="" class="size-[125px]" />
                            <p class="text-2xl font-extrabold text-banner-1">
                                En la carretera
                            </p>
                            <p class="text-white text-lg font-semibold">Paquete</p>
                            <button
                                class="mt-5 btn-primary rounded-lg px-8 py-2 uppercase font-bold xl:text-base text-base flex items-center gap-2 cursor-pointer hover:underline underline-offset-4">
                                Ver Todo
                                <img src="assets/icons/svg/tabler--chevron-right.svg" alt="" />
                            </button>
                        </div>
                        <div class="w-[70%] h-auto image-anime">
                            <img src="assets/images/banner1.jpg" alt=""
                                class="w-full h-full object-cover rounded-r-xl" />
                        </div>
                    </div>
                    <div class="absolute top-0 right-0 flex">
                        <div class="btn-secondary px-4 py-1 font-extrabold text-4xl">
                            +
                        </div>
                        <div class="btn-primary px-5 py-1 flex items-center italic font-bold">
                            100 Programas incluidos
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="flex flex-row">
                        <div class="bg-banner2 w-[30%] h-auto rounded-l-xl p-4">
                            <img src="assets/icons/banner/banner1.svg" alt="" class="size-[125px]" />
                            <p class="text-2xl font-extrabold text-banner-1">
                                En la carretera
                            </p>
                            <p class="text-white text-lg font-semibold">Paquete</p>
                            <button
                                class="mt-5 btn-primary rounded-lg px-8 py-2 uppercase font-bold xl:text-base text-base flex items-center gap-2 cursor-pointer hover:underline underline-offset-4">
                                Ver Todo
                                <img src="assets/icons/svg/tabler--chevron-right.svg" alt="" />
                            </button>
                        </div>
                        <div class="w-[70%] h-auto image-anime">
                            <img src="assets/images/banner2.jpg" alt=""
                                class="w-full h-full object-cover rounded-r-xl" />
                        </div>
                    </div>
                    <div class="absolute top-0 right-0 flex">
                        <div class="btn-secondary px-4 py-1 font-extrabold text-4xl">
                            +
                        </div>
                        <div class="btn-primary px-5 py-1 flex items-center italic font-bold">
                            100 Programas incluidos
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- STATITICS 2 -->
        <section class="my-20 bg-statistics-2 bg-gradient-to-b from-white to-[#D9D9D9]">
            <div class="py-20 px-4 mx-auto max-w-screen-2xl overflow-hidden">
                <img src="assets/icons/Logotipo.svg" alt="" class="mx-auto block flex items-center h-full w-[440px]" />
                <div class="grid grid-cols-2 mt-16">
                    <img src="assets/images/estadisticas_crop.png" alt="" class="w-full h-full">
                    <div class="flex flex-col gap-8">
                        <div class="btn-primary p-4 rounded-xl flex items-center gap-4">
                            <div
                                class="btn-secondary size-[115px] rounded-full flex items-center justify-center flex-shrink-0">
                                <img src="assets/icons/estadisticas/1.svg" alt="">
                            </div>
                            <div class="flex-1 flex flex-col">
                                <h2 class="font-bold text-xl">Soporte técnico especializado</h2>
                                <p class="text-gray-900">
                                    Le apoyamos antes, durante y después de la instalación con asistencia remota para
                                    responder a sus preguntas y ayudarle a trabajar con confianza.
                                </p>
                            </div>
                        </div>
                        <div class="btn-primary p-4 rounded-xl flex items-center gap-4">
                            <div
                                class="btn-secondary size-[115px] rounded-full flex items-center justify-center flex-shrink-0">
                                <img src="assets/icons/estadisticas/2.svg" alt="">
                            </div>
                            <div class="flex-1 flex flex-col">
                                <h2 class="font-bold text-xl">Cobertura global y soporte inmediato</h2>
                                <p class="text-gray-900">
                                    Asistimos a técnicos y mecánicos de todo el mundo con soporte rápido por WhatsApp y
                                    Telegram. Estés donde estés, te tenemos cubierto.
                                </p>
                            </div>
                        </div>
                        <div class="btn-primary p-4 rounded-xl flex items-center gap-4">
                            <div
                                class="btn-secondary size-[115px] rounded-full flex items-center justify-center flex-shrink-0">
                                <img src="assets/icons/estadisticas/3.svg" alt="">
                            </div>
                            <div class="flex-1 flex flex-col">
                                <h2 class="font-bold text-xl">Instalación de software de diagnóstico profesional</h2>
                                <p class="text-gray-900">
                                    Convertimos su portátil en una potente herramienta para el diagnóstico de camiones y
                                    maquinaria pesada. El software se entrega instalado, activado y listo para usar.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" max-w-6xl mx-auto mt-20">
                    <div
                        class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-gray-800 overflow-hidden">
                        <div class="py-5 px-8 text-center">
                            <h2 class="text-6xl lg:text-8xl font-extrabold text-gray-800 mb-2">
                                123K<span class="text-blue-600">+</span>
                            </h2>
                            <p class="text-xl lg:text-2xl text-gray-600 font-bold">
                                Installations
                            </p>
                        </div>

                        <div class="py-5 px-8 text-center">

                            <h2 class="text-6xl lg:text-8xl font-extrabold text-gray-800 mb-2">
                                60<span class="text-blue-600">+</span>
                            </h2>
                            <p class="text-xl lg:text-2xl text-gray-600 font-bold">
                                Cities
                            </p>

                        </div>

                        <div class="py-5 px-8 text-center">
                            <h2 class="text-6xl lg:text-8xl font-extrabold text-gray-800 mb-2">
                                10K<span class="text-blue-600">+</span>
                            </h2>
                            <p class="text-xl lg:text-2xl text-gray-600 font-bold">
                                Clients
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- PROCESO DE COMPRA -->
        <section class="py-20 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <h2 class="uppercase font-extrabold xl:text-3xl text-lg mb-10 text-start mb-14">
                PROCESO DE COMPRA
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <div class="relative">
                    <div
                        class="absolute z-50 btn-secondary -top-[25px] left-[15px] px-4 py-1 font-extrabold text-3xl aspect-square flex items-center">
                        <p>1</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/1.jpg" alt="" class="aspect-square object-cover" />
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute z-50 btn-secondary -top-[25px] left-[15px] px-4 py-1 font-extrabold text-3xl aspect-square flex items-center">
                        <p>2</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/2.jpg" alt="" class="aspect-square object-cover" />
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute z-50 btn-secondary -top-[25px] left-[15px] px-4 py-1 font-extrabold text-3xl aspect-square flex items-center">
                        <p>3</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/3.jpg" alt="" class="aspect-square object-cover" />
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute z-50 btn-secondary -top-[25px] left-[15px] px-4 py-1 font-extrabold text-3xl aspect-square flex items-center">
                        <p>4</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/4.jpg" alt="" class="aspect-square object-cover" />
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute z-50 btn-secondary -top-[25px] left-[15px] px-4 py-1 font-extrabold text-3xl aspect-square flex items-center">
                        <p>5</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/5.jpg" alt="" class="aspect-square object-cover" />
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 justify-items-center w-full mt-10">
                <div class="flex flex-col items-center gap-7">
                    <div class="btn-secondary w-[185px] h-[185px] rounded-full flex items-center">
                        <img src="assets/icons/procesocompra/1.svg" class="size-[125px] mx-auto" alt="" />
                    </div>
                    <p class="text-center text-xl font-bold text-balance">
                        Elige tu software o paquete
                    </p>
                </div>
                <div class="flex flex-col items-center gap-7">
                    <div class="btn-secondary w-[185px] h-[185px] rounded-full flex items-center">
                        <img src="assets/icons/procesocompra/2.svg" class="size-[125px] mx-auto" alt="" />
                    </div>
                    <p class="text-center text-xl font-bold text-balance">
                        Añade al carrito y completa tu compra.
                    </p>
                </div>
                <div class="flex flex-col items-center gap-7">
                    <div class="btn-secondary w-[185px] h-[185px] rounded-full flex items-center">
                        <img src="assets/icons/procesocompra/3.svg" class="size-[125px] mx-auto" alt="" />
                    </div>
                    <p class="text-center text-xl font-bold text-balance">
                        Envía tu número de pedido por WhatsApp
                    </p>
                </div>
                <div class="flex flex-col items-center gap-7">
                    <div class="btn-secondary w-[185px] h-[185px] rounded-full flex items-center">
                        <img src="assets/icons/procesocompra/4.svg" class="size-[125px] mx-auto" alt="" />
                    </div>
                    <p class="text-center text-xl font-bold text-balance">
                        Realizar el pago
                    </p>
                </div>
                <div class="flex flex-col items-center gap-7">
                    <div class="btn-secondary w-[185px] h-[185px] rounded-full flex items-center">
                        <img src="assets/icons/procesocompra/5.svg" class="size-[125px] mx-auto" alt="" />
                    </div>
                    <p class="text-center text-xl font-bold text-balance">
                        Instalación remota
                    </p>
                </div>
            </div>
        </section>
        <!-- METODO DE PAGO Y HERRAMIENTAS -->
        <section class="py-20 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <div class="container mx-auto  mb-4 shadow-xl rounded-b-xl" x-data="{ tab: 'tab1' }">
                <ul class="flex w-full">
                    <li class="flex-1 -mb-px">
                        <a class="block rounded-t-xl  w-full text-center py-3 px-4 rounded-t border-t !border-l font-extrabold text-2xl"
                            href="#"
                            :class="{ 'bg-white text-gray-900 font-extrabold border-l border-t-8 border-r border-[#FFBD47]': tab == 'tab1'}"
                            @click.prevent="tab = 'tab1'">Método de pago</a>
                    </li>
                    <li class="flex-1 -mb-px">
                        <a class="block rounded-t-xl   w-full text-center py-3 px-4 rounded-t border-t !border-r font-extrabold text-gray-500 text-2xl"
                            href="#"
                            :class="{ 'bg-white text-gray-900 font-extrabold border-t-8 border-l border-[#FFBD47]': tab == 'tab2'}"
                            @click.prevent="tab = 'tab2'">Herramientas de instalación remota</a>
                    </li>
                </ul>
                <div class="content rounded-b-xl   bg-white  border-l border-r border-b border-[#FFBD47] pt-4 border-t">
                    <div x-show="tab == 'tab1'" class="p-16">
                        <p class="text-2xl">
                            En DDG aceptamos PayPal, Western Union y MoneyGram; generamos la orden de pago para que
                            usted la complete; tenga en cuenta que PayPal incluye una tarifa adicional.
                        </p>
                        <div class="flex justify-between w-full mt-10">
                            <img src="/assets/icons/svg/paymet-methos/paypal.svg" alt="">
                            <img src="/assets/icons/svg/paymet-methos/westernunion.svg" alt="">
                            <img src="/assets/icons/svg/paymet-methos/moneygram.svg" alt="">
                        </div>
                    </div>
                    <div x-show="tab == 'tab2'" class="p-16">
                        <p class="text-2xl">
                            En DDG utilizamos AnyDesk, TeamViewer y UltraViewer para instalaciones remotas; solo
                            conéctese a Internet y nos encargaremos del resto de forma rápida, segura y sin
                            complicaciones.
                        </p>
                    </div>


                </div>
            </div>
        </section>


        <!-- TESTIMONIALS -->
        <section class="py-20 px-4 mx-auto max-w-screen-2xl " data-aos="fade-up">
            <div class="text-start mb-10">
                <h2 class="uppercase font-extrabold xl:text-3xl lg:text-2xl md:text-xl text-lg mb-4">
                    Lo que dicen nuestros clientes
                </h2>

            </div>

            <section id="testimonials-carousel" class="splide" aria-label="Testimonios de clientes">
                <div class="splide__track pb-6 pt-3">
                    <ul class="splide__list ">
                        <li class="splide__slide">
                            <div
                                class="bg-white shadow border border-gray-100 p-6 md:p-8 mx-2 h-full flex flex-col justify-between min-h-[320px]">
                                <div class="flex-1">
                                    <div class="flex items-center gap-1 mb-4">
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    </div>

                                    <div class="flex items-center gap-4 mb-4 mt-2">
                                        <img src="/assets/images/testimonial.png" class="size-[53px]" alt="">
                                        <p class="font-bold text-xl">
                                            Jhon Doe
                                        </p>
                                    </div>

                                    <blockquote class="text-gray-700 text-base md:text-lg leading-relaxed mb-6">
                                        "El software de diagnóstico VOLVO PTT llegó perfectamente instalado y
                                        configurado. La instalación remota fue impecable y el soporte técnico
                                        excepcional. Ahora puedo diagnosticar cualquier problema en camiones Volvo de
                                        manera profesional."
                                    </blockquote>

                                    <div class="flex items-center gap-2 justify-end">
                                        <p>País:</p>
                                        <img src="/assets/icons/svg/peru_flag.svg" alt="" class="w-[20px] h-auto">
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="splide__slide">
                            <div
                                class="bg-white shadow border border-gray-100 p-6 md:p-8 mx-2 h-full flex flex-col justify-between min-h-[320px]">
                                <div class="flex-1">
                                    <div class="flex items-center gap-1 mb-4">
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    </div>

                                    <div class="flex items-center gap-4 mb-4 mt-2">
                                        <img src="/assets/images/testimonial.png" class="size-[53px]" alt="">
                                        <p class="font-bold text-xl">
                                            Carlos Sánchez
                                        </p>
                                    </div>

                                    <blockquote class="text-gray-700 text-base md:text-lg leading-relaxed mb-6">
                                        "El software de diagnóstico VOLVO PTT llegó perfectamente instalado y
                                        configurado. La instalación remota fue impecable y el soporte técnico
                                        excepcional. Ahora puedo diagnosticar cualquier problema en camiones Volvo de
                                        manera profesional."
                                    </blockquote>

                                    <div class="flex items-center gap-2 justify-end">
                                        <p>País:</p>
                                        <img src="/assets/icons/svg/peru_flag.svg" alt="" class="w-[20px] h-auto">
                                    </div>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
            </section>
        </section>
        <!-- FAQ -->
        <section class="py-20 px-4 mx-auto max-w-screen-2xl overflow-hidden" data-aos="fade-up">
            <div class="text-start xl:mb-12 mb-5">
                <h2 class="uppercase font-extrabold xl:text-3xl lg:text-2xl md:text-xl text-lg mb-4">
                    Preguntas frecuentes
                </h2>
            </div>

            <div class="w-full space-y-4" id="faq-container">
                <!-- FAQ Item 1 -->
                <div class="faq-item border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <button
                        class="faq-header w-full px-6 py-5 text-left flex items-center justify-between bg-white hover:bg-gray-50 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-opacity-50">
                        <h3 class="text-lg font-bold text-gray-900 pr-4">
                            ¿Cómo sé si el software es compatible con mi interfaz?
                        </h3>
                        <div class="faq-icon flex-shrink-0 transform transition-transform duration-300 ease-in-out">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <div class="px-6 py-5 bg-gray-50 border-t border-gray-100">
                            <p class="text-gray-700 leading-relaxed">
                                Cada software incluye una lista detallada de interfaces compatibles en su descripción.
                                Además, nuestro equipo técnico puede asesorarte sobre la compatibilidad específica de tu
                                equipo. Contamos con software para las principales marcas como Launch, Autel, OTC y
                                muchas más.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <button
                        class="faq-header w-full px-6 py-5 text-left flex items-center justify-between bg-white hover:bg-gray-50 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-opacity-50">
                        <h3 class="text-lg font-bold text-gray-900 pr-4">
                            ¿Qué incluye la compra del software?
                        </h3>
                        <div class="faq-icon flex-shrink-0 transform transition-transform duration-300 ease-in-out">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div class="faq-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <div class="px-6 py-5 bg-gray-50 border-t border-gray-100">
                            <p class="text-gray-700 leading-relaxed mb-3">
                                Tu compra incluye:
                            </p>
                            <ul class="text-gray-700 space-y-2">
                                <li class="flex items-start">
                                    <span class="text-amber-500 mr-2">•</span>
                                    Software completo con todas las funciones
                                </li>
                                <li class="flex items-start">
                                    <span class="text-amber-500 mr-2">•</span>
                                    Manual de instalación paso a paso
                                </li>
                                <li class="flex items-start">
                                    <span class="text-amber-500 mr-2">•</span>
                                    Soporte técnico especializado
                                </li>
                                <li class="flex items-start">
                                    <span class="text-amber-500 mr-2">•</span>
                                    Actualizaciones por tiempo determinado
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </main>
    <!-- FOOTER -->
    <footer class="btn-primary shadow-[0_-5px_15px_0_rgba(0,0,0,0.13)]">
        <div class="py-20 px-4 mx-auto max-w-screen-2xl grid grid-cols-4 gap-16">
            <div>
                <img src="/assets/icons/Logotipo.svg" alt="" class="mx-auto block flex items-start h-min w-[275px]" />
                <div class="grid grid-cols-5 gap-4 mt-7 border-b border-gray-500 border-solid pb-6 ">
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/fb.svg" alt="">
                    </div>
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/ig.svg" alt="">
                    </div>
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/telegram.svg" alt="">
                    </div>
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/wsp.svg" alt="">
                    </div>
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/email.svg" alt="">
                    </div>
                </div>
                <p class="text-xl max-w-[435px] mt-6 text-center text-balance">
                    Catálogo con más de <span class="font-extrabold">200 Softwares!</span>
                </p>
                <a href="#"
                    class="btn-secondary w-full rounded-lg block mt-5 font-extrabold text-xl text-center py-3 hover:brightness-110 transition-all easy-in-out duration-200">
                    Ver catálogo
                </a>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold">Mapa del sitio </h2>
                <ul class="flex flex-col gap-5 mt-4 text-lg">
                    <li><a href="#home" class="text-gray-900 hover:underline underline-offset-4">Inicio</a>
                    </li>
                    <li><a href="#features" class="text-gray-900 hover:underline underline-offset-4">Sobre nosotros</a>
                    </li>
                    <li><a href="#pricing" class="text-gray-900 hover:underline underline-offset-4">Software</a></li>
                    <li><a href="#pricing" class="text-gray-900 hover:underline underline-offset-4">El software más
                            reciente</a></li>
                    <li><a href="#pricing" class="text-gray-900 hover:underline underline-offset-4">Paquetes</a></li>
                    <li><a href="#pricing" class="text-gray-900 hover:underline underline-offset-4">Las marcas más
                            vendidas</a></li>
                </ul>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold">Ayuda </h2>
                <ul class="flex flex-col gap-5 mt-4 text-lg">
                    <li><a href="#home" class="text-gray-900 hover:underline underline-offset-4">Iniciar sesión</a>
                    </li>
                    <li><a href="#features" class="text-gray-900 hover:underline underline-offset-4">Registrarse</a>
                    </li>
                    <li><a href="#pricing" class="text-gray-900 hover:underline underline-offset-4">Preguntas
                            frecuentes</a></li>
                    <li><a href="#pricing" class="text-gray-900 hover:underline underline-offset-4">Cómo ganar más
                            monedas</a></li>
                    <li><a href="#pricing" class="text-gray-900 hover:underline underline-offset-4">How to buy</a></li>
                </ul>
            </div>
            <div>
                <h2 class="text-2xl font-extrabold">Recursos </h2>
                <ul class="flex flex-col gap-5 mt-4 text-lg">
                    <li><a href="#home" class="text-gray-900 hover:underline underline-offset-4">Términos y
                            condiciones</a>
                    </li>
                    <li><a href="#features" class="text-gray-900 hover:underline underline-offset-4">Política de
                            privacidad</a>
                    </li>
                </ul>
            </div>
        </div>
    </footer>
    <!-- MODALS -->
    <div id="authentication-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[900px]">
        <div class="relative p-4 w-full max-w-6xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white shadow-lg">
                <!-- Modal Body with 2 Columns -->
                <div class="grid grid-cols-1 md:grid-cols-2 min-h-[500px] modal-content-hidden" id="modal-body">
                    <!-- Column 1: Tabs and Forms -->
                    <div class="flex flex-col xl:pt-6 xl:px-10 px-6">
                        <!-- Tabs -->
                        <div class="">
                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center gap-10" id="auth-tab"
                                data-tabs-toggle="#auth-tab-content" role="tablist">
                                <li class="flex-1" role="presentation">
                                    <button
                                        class="w-min p-4 border-b-2 border-amber-500 rounded-t-lg text-amber-600 tab-button-transition"
                                        id="login-tab" data-tabs-target="#login" type="button" role="tab"
                                        aria-controls="login" aria-selected="true">
                                        Iniciar Sesión
                                    </button>
                                </li>
                                <li class="flex-1" role="presentation">
                                    <button
                                        class="w-min p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 tab-button-transition"
                                        id="register-tab" data-tabs-target="#register" type="button" role="tab"
                                        aria-controls="register" aria-selected="false">
                                        Registrarse
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div id="auth-tab-content" class="p-4 md:p-6 flex-1 flex flex-col">
                            <!-- Login Form -->
                            <div class="flex flex-col tab-content-transition" id="login" role="tabpanel"
                                aria-labelledby="login-tab">
                                <div class="xl:!-mt-[165px] xl:mb-[35px]">
                                    <h4 class="text-2xl font-bold text-gray-900 mb-2 text-center">
                                        Hola, <span class="font-extrabold">amigo</span>!
                                    </h4>
                                </div>
                                <form class="space-y-4" id="login-form" action="login.php" method="POST">
                                    <div>
                                        <label for="input-group-1"
                                            class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tu correo
                                            electrónico</label>
                                        <div class="relative mb-6">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                                <img src="assets/icons/svg/correo-electronico-input.svg" alt="" />
                                            </div>
                                            <input name="email" type="text" id="input-group-1"
                                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                                placeholder="name@flowbite.com" />
                                        </div>
                                    </div>
                                    <div class="">
                                        <label for="input-group-1"
                                            class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tu
                                            contraseña</label>
                                        <div class="relative mb-6">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center ps-3.5 -ml-0.5 pointer-events-none">
                                                <img src="assets/icons/svg/password-input.svg" alt="" />
                                            </div>
                                            <input type="password" name="password" id="input-group-2"
                                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                                placeholder="••••••••" />
                                        </div>
                                        <div class="text-end -mt-4">
                                            <p class="text-sm font-medium text-gray-500">
                                                ¿Olvidaste tu contraseña?
                                                <a href="#" class="text-amber-600 hover:underline">Recuperar
                                                    contraseña</a>
                                            </p>
                                        </div>
                                    </div>


                                    <button type="submit"
                                        class="w-full cursor-pointer xl:mt-14 mt-6 text-white btn-secondary shadow-lg focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-6 py-2.5 text-center">
                                        Iniciar Sesión
                                    </button>
                                    <div class="text-center">
                                        <p class="text-gray-700 text-sm">
                                            ¿Aún no tienes una cuenta?
                                            <button type="button"
                                                class="text-amber-600 hover:underline bg-transparent border-none cursor-pointer"
                                                id="switch-to-register">
                                                Regístrate
                                            </button>
                                        </p>
                                    </div>

                                    <div id="login-error"
                                        class="hidden mt-2 text-xs text-red-700 bg-red-100 border border-red-200 rounded px-3 py-1.5 text-center shadow-sm">
                                        <!-- El mensaje de error se insertará aquí dinámicamente -->
                                    </div>

                                </form>
                            </div>

                            <!-- Register Form -->
                            <div class="hidden flex-col justify-center h-[900px] tab-content-transition" id="register"
                                role="tabpanel" aria-labelledby="register-tab">
                                <h4 class="text-2xl font-bold text-gray-900 mb-2 text-center">
                                    Hola, <span class="font-extrabold">amigo</span>!
                                </h4>
                                <form id="register-form" class="space-y-4" action="#" method="POST">
                                    <!-- Nombre completo -->
                                    <div>
                                        <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tus
                                            nombres y apellidos</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                                <img src="assets/icons/svg/full-name-input.svg" alt="" />
                                            </div>
                                            <input name="nombre_completo" type="text"
                                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                                placeholder="Juan Pérez" required />
                                        </div>
                                    </div>

                                    <!-- País + Teléfono -->
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block mb-2 text-sm xl:text-base font-medium text-gray-900">País</label>
                                            <select name="pais"
                                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                required>
                                                <option value="">Elige un país</option>
                                                <option value="Estados Unidos">Estados Unidos</option>
                                                <option value="Perú">Perú</option>
                                                <option value="Francia">Francia</option>
                                                <option value="Alemania">Alemania</option>
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

                                    <!-- Email + Código referido -->
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tu
                                                correo electrónico</label>
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                                    <img src="assets/icons/svg/correo-electronico-input.svg" alt="" />
                                                </div>
                                                <input name="email" type="email"
                                                    class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                                    placeholder="name@correo.com" required />
                                            </div>
                                        </div>
                                        <div>
                                            <label
                                                class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Código
                                                de referido</label>
                                            <input name="codigo_referido" type="text"
                                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                placeholder="Opcional" />
                                        </div>
                                    </div>

                                    <!-- Contraseña + Confirmación -->
                                    <div>
                                        <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tu
                                            contraseña</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                                <img src="assets/icons/svg/password-input.svg" alt="" />
                                            </div>
                                            <input name="password" type="password"
                                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                                placeholder="••••••••" required minlength="6" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Repita
                                            su contraseña</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                                <img src="assets/icons/svg/password-input.svg" alt="" />
                                            </div>
                                            <input name="password_confirm" type="password"
                                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                                placeholder="••••••••" required minlength="6" />
                                        </div>
                                    </div>

                                    <!-- Términos -->
                                    <div class="flex items-start justify-center mb-5 mx-auto">
                                        <input id="terms" name="terms" type="checkbox"
                                            class="w-4 h-4 border border-gray-300 rounded-sm focus:ring-3 focus:ring-blue-300"
                                            required />
                                        <label for="terms" class="ms-2 text-sm font-medium text-gray-900">
                                            Acepto los <a href="#" class="text-[#F7A615] hover:underline">términos y
                                                condiciones</a>
                                        </label>
                                    </div>

                                    <button type="submit"
                                        class="w-full cursor-pointer mt-2 shadow-lg text-white btn-secondary hover:bg-amber-600 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                        Crear Cuenta
                                    </button>
                                </form>

                                <!-- Alertas -->
                                <div id="register-error"
                                    class="hidden mt-2 text-xs text-red-700 bg-red-100 border border-red-200 rounded px-3 py-1.5 text-center shadow-sm">
                                </div>
                                <div id="register-success"
                                    class="hidden mt-2 text-xs text-green-700 bg-green-100 border border-green-200 rounded px-3 py-1.5 text-center shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Static Image -->
                    <div class="hidden md:block">
                        <img src="assets/images/auth.jpg" alt="Authentication"
                            class="w-full h-[750px] object-cover rounded-r-lg" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- DRAWER -->
    <div id="drawer-right-example"
        class="fixed top-0 right-0 z-40 h-screen px-4 py-10 overflow-y-auto transition-transform translate-x-full btn-secondary w-80"
        tabindex="-1" aria-labelledby="drawer-right-label">
        <div
            class="flex flex-col items-center w-full max-w-sm mx-auto p-4 rounded-xl border border-gray-200 bg-white/90 shadow-md backdrop-blur-sm">

            <?php if (isset($_SESSION['usuario_id'])): ?>
                <?php $usuarioMenu = $database->get('usuarios', ['nombre'], ['id_usuario' => $_SESSION['usuario_id']]); ?>

                <!-- Saludo -->
                <div class="text-center mb-4">
                    <p class="text-lg font-semibold text-slate-900">
                        👋 Hola <span class="text-indigo-600"><?php echo htmlspecialchars($usuarioMenu['nombre']); ?></span>
                    </p>
                    <p class="text-sm text-slate-500">Bienvenido(a) de nuevo</p>
                </div>

                <!-- Botón cerrar sesión -->
                <button type="button" id="logoutModalBtn"
                    class="cursor-pointer flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-sm hover:border-red-300 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-1 focus:ring-red-400 focus:ring-offset-1 transition-colors mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                    </svg>
                    <span>Cerrar sesión</span>
                </button>

                <!-- Modal -->
                <div id="logoutModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
                    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
                        <h2 class="text-lg font-semibold text-slate-900 mb-4">¿Cerrar sesión?</h2>
                        <p class="text-sm text-slate-600 mb-6">Se cerrará tu sesión actual y volverás a la página de inicio
                            de sesión.</p>
                        <div class="flex justify-end gap-3">
                            <button id="cancelLogout"
                                class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancelar</button>
                            <a href="logout.php"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">Sí,
                                salir</a>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- Botones login y registro -->
                <div class="flex gap-3 mb-4">
                    <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                        data-active-tab="login"
                        class="bg-black text-white rounded-lg px-4 py-2 text-sm font-medium shadow hover:bg-gray-800 transition-colors"
                        type="button">
                        Iniciar sesión
                    </button>
                    <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                        data-active-tab="register"
                        class="text-gray-700 border border-gray-300 rounded-lg px-4 py-2 text-sm font-medium shadow hover:bg-gray-100 transition-colors"
                        type="button">
                        Registro
                    </button>
                </div>
            <?php endif; ?>

            <!-- Carrito -->
            <div class="w-full">
                <?php if (!empty($_SESSION['carrito'])): ?>
                    <ul
                        class="divide-y divide-gray-200 max-h-64 overflow-y-auto rounded-lg border border-gray-100 bg-gray-50 p-2">
                        <?php $total = 0; ?>
                        <?php foreach ($_SESSION['carrito'] as $item): ?>
                            <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                            <?php $total += $subtotal; ?>
                            <li class="py-2 flex justify-between items-center hover:bg-white rounded-md px-2">
                                <div>
                                    <p class="font-medium text-slate-700"><?php echo $item['nombre']; ?></p>
                                    <p class="text-xs text-slate-500">Cantidad: <?php echo $item['cantidad']; ?></p>
                                </div>
                                <span class="text-indigo-600 font-semibold">$<?php echo number_format($subtotal, 2); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="mt-3 border-t pt-3">
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold text-slate-800">Total:</span>
                            <span class="text-green-600 font-bold text-lg">$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <a href="carrito.php"
                            class="block w-full text-center bg-indigo-500 hover:bg-indigo-600 text-white py-2 rounded-full font-medium transition-colors">
                            Ver carrito
                        </a>
                    </div>
                <?php else: ?>
                    <p
                        class="flex flex-col items-center justify-center text-center text-gray-500 text-sm mb-3 bg-gray-50 border border-dashed border-gray-300 rounded-lg p-4 shadow-sm">
                        <span class="text-2xl mb-1">🛒</span>
                        <span class="font-medium">Tu carrito está vacío</span>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="alertaFavorito"
        class="hidden fixed top-5 right-5 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50 text-sm"
        role="alert">
        <strong class="font-bold">¡Atención!</strong>
        <span class="block" id="alertaTexto"></span>
    </div>

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="scripts/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

</body>

</html>