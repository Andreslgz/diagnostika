<?php
// public/index.php (añadir lógica para mostrar el carrito)
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
    <link rel="stylesheet" href="/../styles/main.css" />
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
                    <img src="/../assets/icons/Logotipo.svg" class="mr-3 h-6 sm:h-9" alt="Flowbite Logo" />
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
                            <img src="/../assets/icons/Cart.svg" alt="" class="xl:w-[39px] w-[25px]" />
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
                        <li class="lg:h-full lg:flex lg:items-center xl:py-3">
                            <a href="#"
                                class="block text-gray-600 py-2 pr-4 pl-3 border-b border-gray-100 lg:hover:bg-white lg:border-0 lg:hover:text-blue-700 lg:px-6 lg:py-5 lg:h-full lg:flex lg:items-center">Inicio</a>
                        </li>
                        <li class="lg:h-full lg:flex lg:items-center btn-secondary xl:py-3">
                            <a href="#"
                                class="block py-2 pr-4 pl-3 border-b border-gray-100 font-semibold text-white lg:px-6 lg:py-5 lg:h-full lg:flex lg:items-center lg:border-0">Tienda</a>
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
        <!-- Breadcrumbs -->
        <section class="xl:pt-16 py-4 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="../tienda/index.php"
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-orange-600 ">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                            </svg>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                            <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-orange-600 md:ms-2 ">Mi
                                cuenta</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">Información personal</span>
                        </div>
                    </li>
                </ol>
            </nav>

        </section>
        <section class="xl:pb-16 py-0 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <div>
                <h1 class="text-2xl font-extrabold">
                    Mi cuenta
                </h1>
                <div class="grid grid-cols-12 mt-4">
                    <div class="col-span-2 border border-solid border-gray-300 rounded">
                        <div class=" p-3 btn-primary">
                            Información personal
                        </div>
                        <a href="./misoftware.php"
                            class=" p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer">
                            Mis software
                        </a>
                        <a href="./estadoinstalaciones.php"
                            class=" p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer">
                            Estado de instalación
                        </a>
                        <a href="./miscupones.php"
                            class=" p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer">
                            Mis cupones
                        </a>
                        <a href="./miscreditos.php"
                            class=" p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer">
                            Mis créditos
                        </a>
                        <a href="./productosguardados.php"
                            class=" p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer">
                            Productos guardados
                        </a>
                        <div class=" p-3  hover:bg-gray-200 hover:cursor-pointer">
                            Cerrar sesión
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- FOOTER -->
    <footer class="btn-primary shadow-[0_-5px_15px_0_rgba(0,0,0,0.13)] overflow-hidden">
        <div
            class="py-10 md:py-20 px-4 mx-auto max-w-screen-2xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-16">
            <div class="text-center sm:text-left">
                <img src="/assets/icons/Logotipo.svg" alt=""
                    class="mx-auto sm:mx-0 block flex items-start h-min w-[200px] md:w-[275px]" />
                <div
                    class="grid grid-cols-5 gap-3 md:gap-4 mt-5 md:mt-7 border-b border-gray-500 border-solid pb-6 max-w-[250px] mx-auto sm:mx-0">
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[35px] md:size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/fb.svg" alt="">
                    </div>
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[35px] md:size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/ig.svg" alt="">
                    </div>
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[35px] md:size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/telegram.svg" alt="">
                    </div>
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[35px] md:size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/wsp.svg" alt="">
                    </div>
                    <div
                        class="bg-gradient-to-b from-[#DEDEDE] to-[#A7A7A6] p-1.5 size-[35px] md:size-[40px] rounded-full flex items-center justify-center">
                        <img src="/assets/icons/svg/social/email.svg" alt="">
                    </div>
                </div>
                <p class="text-lg md:text-xl max-w-[435px] mt-4 md:mt-6 text-center sm:text-left mx-auto sm:mx-0">
                    Catálogo con más de <span class="font-extrabold">200 Softwares!</span>
                </p>
                <a href="#"
                    class="btn-secondary w-full rounded-lg block mt-4 md:mt-5 font-extrabold text-lg md:text-xl text-center py-2 md:py-3 hover:brightness-110 transition-all easy-in-out duration-200">
                    Ver catálogo
                </a>
            </div>
            <div class="mt-8 sm:mt-0">
                <h2 class="text-xl md:text-2xl font-extrabold text-center sm:text-left">Mapa del sitio</h2>
                <ul class="flex flex-col gap-3 md:gap-5 mt-4 text-base md:text-lg text-center sm:text-left">
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
            <div class="mt-8 lg:mt-0">
                <h2 class="text-xl md:text-2xl font-extrabold text-center sm:text-left">Ayuda</h2>
                <ul class="flex flex-col gap-3 md:gap-5 mt-4 text-base md:text-lg text-center sm:text-left">
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
            <div class="mt-8 lg:mt-0">
                <h2 class="text-xl md:text-2xl font-extrabold text-center sm:text-left">Recursos</h2>
                <ul class="flex flex-col gap-3 md:gap-5 mt-4 text-base md:text-lg text-center sm:text-left">
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
        class="fixed top-0 right-0 z-40 h-screen px-4 py-10 overflow-y-auto transition-transform translate-x-full btn-secondary xl:w-[500px] w-[calc(100vw-50px)]"
        tabindex="-1" aria-labelledby="drawer-right-label">
        <div class="flex flex-col items-center w-full  ">

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
                        <p class="text-sm text-slate-600 mb-6">Se cerrará tu sesión actual y volverás a la página de
                            inicio
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
                        class="text-gray-700 border bg-white border-gray-300 rounded-lg px-4 py-2 text-sm font-medium shadow hover:bg-gray-100 transition-colors"
                        type="button">
                        Registro
                    </button>
                </div>
            <?php endif; ?>

            <!-- Carrito -->
            <div class="w-full">
                <?php if (!empty($_SESSION['carrito'])): ?>
                    <ul class="space-y-3 max-h-[750px] overflow-y-auto rounded-lg ">
                        <?php $total = 0; ?>
                        <?php foreach ($_SESSION['carrito'] as $index => $item): ?>
                            <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                            <?php $total += $subtotal; ?>
                            <li class="relative bg-white rounded-xl  xl:p-4 p-2.5 h-full flex items-center xl:gap-3 gap-2">
                                <!-- Imagen del producto con borde -->
                                <div class="flex-shrink-0">
                                    <div class="xl:w-[85px] xl:h-[85px] w-[65px] h-[65px]">
                                        <img src="/assets/images/carritoProducto.png"
                                            alt="<?php echo htmlspecialchars($item['nombre']); ?>"
                                            class="w-full h-full object-cover">
                                    </div>
                                </div>

                                <!-- Contenido derecho -->
                                <div class="flex-grow flex flex-col justify-between h-full py-1 xl:gap-6 gap-4">
                                    <!-- Parte superior: Nombre y botón cerrar -->
                                    <div class="flex items-start justify-between">
                                        <h3 class="font-semibold text-gray-800 xl:text-base text-sm uppercase tracking-wide">
                                            <?php echo htmlspecialchars($item['nombre']); ?>
                                        </h3>
                                        <button onclick="removeItem(<?php echo $index; ?>)"
                                            class="text-gray-600 hover:text-gray-800 transition-colors ml-2">
                                            <svg class="xl:w-6 xl:h-6 w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Parte inferior: Precio y controles -->
                                    <div class="flex items-center justify-between">
                                        <!-- Precio -->
                                        <span class="xl:text-xl text-sm font-semibold text-gray-800">
                                            USD. <?php echo number_format($subtotal, 2); ?>
                                        </span>

                                        <!-- Controles de cantidad -->
                                        <div class="flex items-center border border-gray-300 rounded-md">
                                            <button onclick="updateQuantity(<?php echo $index; ?>, -1)"
                                                class="xl:w-6 xl:h-6 w-4 h-4 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors xl:text-xl font-medium">
                                                −
                                            </button>
                                            <span
                                                class="xl:w-12 w-8 text-center text-gray-800 font-medium xl:text-lg text-base border-x border-gray-300">
                                                <?php echo $item['cantidad']; ?>
                                            </span>
                                            <button onclick="updateQuantity(<?php echo $index; ?>, 1)"
                                                class="xl:w-6 xl:h-6 w-4 h-4 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors xl:text-xl font-medium">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="border-t pt-4 mt-4">
                        <div class="flex justify-between items-center mb-3">
                            <span class="font-bold xl:text-xl">Total:</span>
                            <span class="text-white font-bold xl:text-xl">$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <a href="carrito.php"
                            class="block w-full text-center btn-primary rounded-lg xl:py-3 py-2 xl:text-lg font-semibold shadow hover:brightness-110 transition-all duration-200 ease-in-out">
                            Ir al Carrito
                        </a>
                        <button
                            class="mt-5 block w-full text-white text-center btn-transparent border border-solid border-white rounded-lg xl:py-3 py-2 xl:text-lg font-semibold shadow hover:brightness-110 transition-all duration-200 ease-in-out"
                            type="button" data-drawer-hide="drawer-right-example" aria-controls="drawer-right-example">
                            Seguir viendo
                        </button>
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
    <script src="../scripts/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        .bg-cards {
            background: linear-gradient(0deg, #A7A7A6 0%, #DEDEDE 100%);
        }

        .bg-cards:hover {
            background: linear-gradient(0deg, #8A8A89 0%, #C0C0C0 100%);
        }
    </style>
</body>

</html>