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
        <section class="bg-gray-50 py-8 antialiased  md:py-12">
            <div class="mx-auto max-w-screen-2xl px-4 2xl:px-0">
                <div class="mb-4 items-end justify-between sm:flex md:mb-8">
                    <div class="mb-4 sm:mb-0">
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                                <li class="inline-flex items-center">
                                    <a href="#"
                                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 ">
                                        <svg class="me-2.5 h-3 w-3" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                                        </svg>
                                        Home
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-400 rtl:rotate-180" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m9 5 7 7-7 7" />
                                        </svg>
                                        <a href="#"
                                            class="ms-1 text-sm font-medium text-gray-700 hover:text-primary-600   md:ms-2">Products</a>
                                    </div>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-400 rtl:rotate-180" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m9 5 7 7-7 7" />
                                        </svg>
                                        <span class="ms-1 text-sm font-medium text-gray-500  md:ms-2">Electronics</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <h2 class="mt-3 text-xl font-semibold text-gray-900 sm:text-2xl">Electronics
                        </h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button data-drawer-target="drawer-mobile-filter" data-drawer-show="drawer-mobile-filter"
                            aria-controls="drawer-mobile-filter" type="button"
                            class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100  sm:w-auto lg:hidden">
                            <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                    d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z" />
                            </svg>
                            Filters
                            <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                        <button id="sortDropdownButton2" data-dropdown-toggle="dropdownSort2" type="button"
                            class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100  sm:w-auto">
                            <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 4v16M7 4l3 3M7 4 4 7m9-3h6l-6 6h6m-6.5 10 3.5-7 3.5 7M14 18h4" />
                            </svg>
                            Sort
                            <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="dropdownSort2"
                            class="z-50 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow "
                            data-popper-placement="bottom">
                            <ul class="p-2 text-left text-sm font-medium text-gray-500 "
                                aria-labelledby="sortDropdownButton">
                                <li>
                                    <a href="#"
                                        class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900   ">
                                        The most popular </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900   ">
                                        Newest </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900   ">
                                        Increasing price </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900   ">
                                        Decreasing price </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900   ">
                                        No. reviews </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900   ">
                                        Discount % </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="gap-6 lg:flex">
                    <!-- Sidenav -->
                    <aside id="sidebar"
                        class="hidden h-full w-80 shrink-0 border border-gray-200 bg-white p-4 shadow-sm  lg:block lg:rounded-lg">
                        <div id="accordion-flush" data-accordion="collapse"
                            data-active-classes="bg-white  text-gray-900 " data-inactive-classes="text-gray-500 ">
                            <h2 id="accordion-flush-heading-1">
                                <button type="button"
                                    class="mb-4 flex w-full items-center justify-between font-medium text-gray-500 hover:text-gray-900   "
                                    data-accordion-target="#accordion-flush-body-1" aria-expanded="true"
                                    aria-controls="accordion-flush-body-1">
                                    <span>Categories</span>
                                    <svg data-accordion-icon class="h-5 w-5 shrink-0 rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m5 15 7-7 7 7" />
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-flush-body-1" class="mb-4 hidden space-y-4"
                                aria-labelledby="accordion-flush-heading-1">
                                <div>
                                    <label for="search"
                                        class="sr-only mb-2 text-sm font-medium text-gray-900 ">Search</label>
                                    <div class="relative block">
                                        <div
                                            class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3">
                                            <svg class="h-4 w-4 text-gray-500 " aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                            </svg>
                                        </div>
                                        <input type="search" id="search"
                                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2 ps-10 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    "
                                            placeholder="Search for categories" required />
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input id="tv-audio-video" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="tv-audio-video" class="ms-2 text-sm font-medium text-gray-900 "> TV,
                                            Audio-Video </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="desktop-pc" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                            checked />
                                        <label for="desktop-pc" class="ms-2 text-sm font-medium text-gray-900 "> Desktop
                                            PC </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="gaming" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="gaming" class="ms-2 text-sm font-medium text-gray-900 "> Gaming
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="monitors" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="monitors" class="ms-2 text-sm font-medium text-gray-900 "> Monitors
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="laptops" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                            checked />
                                        <label for="laptops" class="ms-2 text-sm font-medium text-gray-900 "> Laptops
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="console" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="console" class="ms-2 text-sm font-medium text-gray-900 "> Console
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="tablets" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                            checked />
                                        <label for="tablets" class="ms-2 text-sm font-medium text-gray-900 "> Tablets
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="foto" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="foto" class="ms-2 text-sm font-medium text-gray-900 "> Foto
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="fashion" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="fashion" class="ms-2 text-sm font-medium text-gray-900 "> Fashion
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="books" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="books" class="ms-2 text-sm font-medium text-gray-900 "> Books
                                        </label>
                                    </div>
                                </div>

                                <a href="#" title=""
                                    class="inline-flex items-center gap-1 text-sm font-medium text-primary-700 hover:underline ">
                                    See more
                                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                                    </svg>
                                </a>
                            </div>
                            <h2 id="accordion-flush-heading-2">
                                <button type="button"
                                    class="mb-4 flex w-full items-center justify-between font-medium text-gray-500 hover:text-gray-900   rtl:text-right "
                                    data-accordion-target="#accordion-flush-body-2" aria-expanded="true"
                                    aria-controls="accordion-flush-body-2">
                                    <span>Rating</span>
                                    <svg data-accordion-icon class="h-5 w-5 shrink-0 rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m5 15 7-7 7 7" />
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-flush-body-2" class="mb-4 hidden space-y-4"
                                aria-labelledby="accordion-flush-heading-2">
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input id="rating-checkbox-1" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                            checked />
                                        <label for="rating-checkbox-1"
                                            class="ms-2 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 ">
                                            <div class="flex items-center gap-0.5">
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                            </div>
                                            (475)
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="rating-checkbox-2" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                            checked />
                                        <label for="rating-checkbox-2"
                                            class="ms-2 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 ">
                                            <div class="flex items-center gap-0.5">
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                            </div>
                                            (12)
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="rating-checkbox-3" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="rating-checkbox-3"
                                            class="ms-2 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 ">
                                            <div class="flex items-center gap-0.5">
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                            </div>
                                            (20)
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="rating-checkbox-4" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="rating-checkbox-4"
                                            class="ms-2 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 ">
                                            <div class="flex items-center gap-0.5">
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                            </div>
                                            (11)
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="rating-checkbox-5" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="rating-checkbox-5"
                                            class="ms-2 inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 ">
                                            <div class="flex items-center gap-0.5">
                                                <svg class="h-5 w-5 text-yellow-400" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                                <svg class="h-5 w-5 text-gray-300 " aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M13.8 4.2a2 2 0 0 0-3.6 0L8.4 8.4l-4.6.3a2 2 0 0 0-1.1 3.5l3.5 3-1 4.4c-.5 1.7 1.4 3 2.9 2.1l3.9-2.3 3.9 2.3c1.5 1 3.4-.4 3-2.1l-1-4.4 3.4-3a2 2 0 0 0-1.1-3.5l-4.6-.3-1.8-4.2Z" />
                                                </svg>
                                            </div>
                                            (6)
                                        </label>
                                    </div>
                                </div>

                                <a href="#" title=""
                                    class="inline-flex items-center gap-1 text-sm font-medium text-primary-700 hover:underline ">
                                    View all
                                    <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
                                    </svg>
                                </a>
                            </div>
                            <h2 id="accordion-flush-heading-3">
                                <button type="button"
                                    class="mb-4 flex w-full items-center justify-between font-medium text-gray-500 hover:text-gray-900   rtl:text-right "
                                    data-accordion-target="#accordion-flush-body-3" aria-expanded="false"
                                    aria-controls="accordion-flush-body-3">
                                    <span>Price</span>
                                    <svg data-accordion-icon class="h-5 w-5 shrink-0 rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m5 15 7-7 7 7" />
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-flush-body-3" class="mb-4 hidden space-y-4"
                                aria-labelledby="accordion-flush-heading-3">
                                <div class="space-y-3">
                                    <a href="#" title=""
                                        class="inline-flex items-center gap-1 text-sm font-medium text-primary-700 hover:underline ">
                                        <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m15 19-7-7 7-7" />
                                        </svg>
                                        Any Price
                                    </a>
                                    <div class="space-y-3">
                                        <div class="flex items-center">
                                            <input id="price-range-1" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="price-range-1" class="ms-2 text-sm font-medium text-gray-900 ">
                                                Under
                                                $10 </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="price-range-2" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                                checked />
                                            <label for="price-range-2" class="ms-2 text-sm font-medium text-gray-900 ">
                                                $10
                                                to $20 </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="price-range-3" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="price-range-3" class="ms-2 text-sm font-medium text-gray-900 ">
                                                $20
                                                to $30 </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="price-range-4" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="price-range-4" class="ms-2 text-sm font-medium text-gray-900 ">
                                                $30
                                                to $40 </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="price-range-5" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                                checked />
                                            <label for="price-range-5" class="ms-2 text-sm font-medium text-gray-900 ">
                                                $40
                                                to $50 </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="price-range-6" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="price-range-6" class="ms-2 text-sm font-medium text-gray-900 ">
                                                $50
                                                to $60 </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="price-range-7" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                                checked />
                                            <label for="price-range-7" class="ms-2 text-sm font-medium text-gray-900 ">
                                                $60
                                                to $70 </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="price-range-8" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="price-range-8" class="ms-2 text-sm font-medium text-gray-900 ">
                                                $70
                                                to $80 </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="price-range-9" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="price-range-9" class="ms-2 text-sm font-medium text-gray-900 ">
                                                $80
                                                to $90 </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="price-range-10" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="price-range-10" class="ms-2 text-sm font-medium text-gray-900 ">
                                                Over
                                                $100 </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="from_amount" class="mb-2 block text-sm font-medium text-gray-900 ">
                                            From
                                        </label>
                                        <input type="text" id="from_amount"
                                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    "
                                            placeholder="300" required />
                                    </div>

                                    <div>
                                        <label for="to_amount" class="mb-2 block text-sm font-medium text-gray-900 "> To
                                        </label>
                                        <input type="text" id="to_amount"
                                            class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    "
                                            placeholder="3500" required />
                                    </div>
                                </div>
                            </div>
                            <h2 id="accordion-flush-heading-4">
                                <button type="button"
                                    class="mb-4 flex w-full items-center justify-between font-medium text-gray-500 hover:text-gray-900   rtl:text-right "
                                    data-accordion-target="#accordion-flush-body-4" aria-expanded="false"
                                    aria-controls="accordion-flush-body-4">
                                    <span>Shipping to</span>
                                    <svg data-accordion-icon class="h-5 w-5 shrink-0 rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m5 15 7-7 7 7" />
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-flush-body-4" class="mb-4 hidden space-y-4"
                                aria-labelledby="accordion-flush-heading-4">
                                <div class="space-y-3">
                                    <label for="asia" class="relative block">
                                        <input type="checkbox" name="asia" id="asia"
                                            class="peer absolute left-2 top-2 appearance-none" />
                                        <div
                                            class="relative cursor-pointer space-y-1 overflow-hidden rounded-lg border border-gray-200 bg-white p-4 text-gray-500 peer-checked:border-primary-700 peer-checked:bg-primary-50 peer-checked:text-primary-700   ">
                                            <p class="text-sm font-medium">Asia</p>
                                            <p class="text-sm font-normal">Delivery for Asia for <span
                                                    class="font-medium">$954</span></p>
                                        </div>
                                    </label>

                                    <label for="africa" class="relative block">
                                        <input type="checkbox" name="africa" id="africa"
                                            class="peer absolute left-2 top-2 appearance-none" />
                                        <div
                                            class="relative cursor-pointer space-y-1 overflow-hidden rounded-lg border border-gray-200 bg-white p-4 text-gray-500 peer-checked:border-primary-700 peer-checked:bg-primary-50 peer-checked:text-primary-700   ">
                                            <p class="text-sm font-medium">Africa</p>
                                            <p class="text-sm font-normal">Delivery for Africa for <span
                                                    class="font-medium">$706,834</span></p>
                                        </div>
                                    </label>

                                    <label for="americas" class="relative block">
                                        <input type="checkbox" name="americas" id="americas"
                                            class="peer absolute left-2 top-2 appearance-none" checked />
                                        <div
                                            class="relative cursor-pointer space-y-1 overflow-hidden rounded-lg border border-gray-200 bg-white p-4 text-gray-500 peer-checked:border-primary-700 peer-checked:bg-primary-50 peer-checked:text-primary-700   ">
                                            <p class="text-sm font-medium">Americas</p>
                                            <p class="text-sm font-normal">Delivery for USA for <span
                                                    class="font-medium">$365,35</span></p>
                                        </div>
                                    </label>

                                    <label for="australia" class="relative block">
                                        <input type="checkbox" name="australia" id="australia"
                                            class="peer absolute left-2 top-2 appearance-none" checked />
                                        <div
                                            class="relative cursor-pointer space-y-1 overflow-hidden rounded-lg border border-gray-200 bg-white p-4 text-gray-500 peer-checked:border-primary-700 peer-checked:bg-primary-50 peer-checked:text-primary-700   ">
                                            <p class="text-sm font-medium">Australia/Oceania</p>
                                            <p class="text-sm font-normal">Delivery for Australia for <span
                                                    class="font-medium">$209,98</span></p>
                                        </div>
                                    </label>

                                    <label for="europe" class="relative block">
                                        <input type="checkbox" name="europe" id="europe"
                                            class="peer absolute left-2 top-2 appearance-none" />
                                        <div
                                            class="relative cursor-pointer space-y-1 overflow-hidden rounded-lg border border-gray-200 bg-white p-4 text-gray-500 peer-checked:border-primary-700 peer-checked:bg-primary-50 peer-checked:text-primary-700   ">
                                            <p class="text-sm font-medium">Europe</p>
                                            <p class="text-sm font-normal">Delivery for Europe for <span
                                                    class="font-medium">$1,365,35</span></p>
                                        </div>
                                    </label>

                                    <label for="antarctica" class="relative block">
                                        <input type="checkbox" name="antarctica" id="antarctica"
                                            class="peer absolute left-2 top-2 appearance-none" />
                                        <div
                                            class="relative cursor-pointer space-y-1 overflow-hidden rounded-lg border border-gray-200 bg-white p-4 text-gray-500 peer-checked:border-primary-700 peer-checked:bg-primary-50 peer-checked:text-primary-700   ">
                                            <p class="text-sm font-medium">Antarctica</p>
                                            <p class="text-sm font-normal">N/A</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <h2 id="accordion-flush-heading-5">
                                <button type="button"
                                    class="mb-4 flex w-full items-center justify-between font-medium text-gray-500 hover:text-gray-900   rtl:text-right "
                                    data-accordion-target="#accordion-flush-body-5" aria-expanded="false"
                                    aria-controls="accordion-flush-body-5">
                                    <span>Color</span>
                                    <svg data-accordion-icon class="h-5 w-5 shrink-0 rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m5 15 7-7 7 7" />
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-flush-body-5" class="mb-4 hidden space-y-4"
                                aria-labelledby="accordion-flush-heading-5">
                                <div>
                                    <label class="sr-only">Color</label>

                                    <div class="mt-2 flex items-center gap-2">
                                        <label
                                            class="relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 ring-gray-900 focus:outline-none has-[:checked]:ring-2">
                                            <input type="radio" name="color-choice" value="Black" class="sr-only"
                                                checked />
                                            <span id="color-choice-0-label" class="sr-only"> Black </span>
                                            <span aria-hidden="true"
                                                class="h-7 w-7 rounded-full border border-gray-900 border-opacity-10 bg-gray-900"></span>
                                        </label>

                                        <label
                                            class="relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 ring-primary-700 focus:outline-none has-[:checked]:ring-2">
                                            <input type="radio" name="color-choice" value="Blue" class="sr-only" />
                                            <span id="color-choice-0-label" class="sr-only"> Blue </span>
                                            <span aria-hidden="true"
                                                class="h-7 w-7 rounded-full border border-primary-700 border-opacity-10 bg-primary-700"></span>
                                        </label>

                                        <label
                                            class="relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 ring-pink-600 focus:outline-none has-[:checked]:ring-2">
                                            <input type="radio" name="color-choice" value="Pink" class="sr-only" />
                                            <span id="color-choice-0-label" class="sr-only"> Pink </span>
                                            <span aria-hidden="true"
                                                class="h-7 w-7 rounded-full border border-pink-600 border-opacity-10 bg-pink-600"></span>
                                        </label>

                                        <label
                                            class="relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 ring-teal-600 focus:outline-none has-[:checked]:ring-2">
                                            <input type="radio" name="color-choice" value="Teal" class="sr-only" />
                                            <span id="color-choice-0-label" class="sr-only"> Teal </span>
                                            <span aria-hidden="true"
                                                class="h-7 w-7 rounded-full border border-teal-600 border-opacity-10 bg-teal-600"></span>
                                        </label>

                                        <label
                                            class="relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 ring-purple-600 focus:outline-none has-[:checked]:ring-2">
                                            <input type="radio" name="color-choice" value="Purple" class="sr-only" />
                                            <span id="color-choice-0-label" class="sr-only"> Purple </span>
                                            <span aria-hidden="true"
                                                class="h-7 w-7 rounded-full border border-purple-600 border-opacity-10 bg-purple-600"></span>
                                        </label>

                                        <label
                                            class="relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 ring-yellow-400 focus:outline-none has-[:checked]:ring-2">
                                            <input type="radio" name="color-choice" value="Yellow" class="sr-only" />
                                            <span id="color-choice-0-label" class="sr-only"> Yellow </span>
                                            <span aria-hidden="true"
                                                class="h-7 w-7 rounded-full border border-yellow-400 border-opacity-10 bg-yellow-400"></span>
                                        </label>

                                        <label
                                            class="relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 ring-green-600 focus:outline-none has-[:checked]:ring-2">
                                            <input type="radio" name="color-choice" value="Green" class="sr-only" />
                                            <span id="color-choice-0-label" class="sr-only"> Green </span>
                                            <span aria-hidden="true"
                                                class="h-7 w-7 rounded-full border border-green-600 border-opacity-10 bg-green-600"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <h2 id="accordion-flush-heading-6">
                                <button type="button"
                                    class="mb-4 flex w-full items-center justify-between font-medium text-gray-500 hover:text-gray-900   rtl:text-right "
                                    data-accordion-target="#accordion-flush-body-6" aria-expanded="false"
                                    aria-controls="accordion-flush-body-6">
                                    <span>Delivery Method</span>
                                    <svg data-accordion-icon class="h-5 w-5 shrink-0 rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m5 15 7-7 7 7" />
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-flush-body-6" class="mb-4 hidden space-y-4"
                                aria-labelledby="accordion-flush-heading-6">
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input id="flowbox" type="radio" name="delivery" value=""
                                            class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="flowbox" class="ms-2 text-sm font-medium text-gray-900 "> Flowbox
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="pick-from-store" type="radio" name="delivery" value=""
                                            class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="pick-from-store" class="ms-2 text-sm font-medium text-gray-900 ">
                                            Pick up
                                            from the store </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="courier" type="radio" name="delivery" value=""
                                            class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="courier" class="ms-2 text-sm font-medium text-gray-900 "> Fast
                                            courier </label>
                                    </div>
                                </div>
                            </div>
                            <h2 id="accordion-flush-heading-7">
                                <button type="button"
                                    class="mb-4 flex w-full items-center justify-between font-medium text-gray-500 hover:text-gray-900   rtl:text-right "
                                    data-accordion-target="#accordion-flush-body-7" aria-expanded="false"
                                    aria-controls="accordion-flush-body-7">
                                    <span>Condition</span>
                                    <svg data-accordion-icon class="h-5 w-5 shrink-0 rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m5 15 7-7 7 7" />
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-flush-body-7" class="mb-4 hidden space-y-4"
                                aria-labelledby="accordion-flush-heading-7">
                                <div class="flex items-center">
                                    <input id="new" type="radio" name="condition" value=""
                                        class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                    <label for="new" class="ms-2 text-sm font-medium text-gray-900 ">
                                        New Product</label>
                                </div>

                                <div class="flex items-center">
                                    <input id="used" type="radio" name="condition" value=""
                                        class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                    <label for="used" class="ms-2 text-sm font-medium text-gray-900 ">
                                        Used Prooduct</label>
                                </div>

                                <div class="flex items-center">
                                    <input id="resealed" type="radio" name="condition" value=""
                                        class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                    <label for="resealed" class="ms-2 text-sm font-medium text-gray-900 "> Resealed
                                    </label>
                                </div>
                            </div>
                            <h2 id="accordion-flush-heading-8">
                                <button type="button"
                                    class="flex w-full items-center justify-between font-medium text-gray-500 hover:text-gray-900   rtl:text-right "
                                    data-accordion-target="#accordion-flush-body-8" aria-expanded="false"
                                    aria-controls="accordion-flush-body-8">
                                    <span>Weight</span>
                                    <svg data-accordion-icon class="h-5 w-5 shrink-0 rotate-180" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m5 15 7-7 7 7" />
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-flush-body-8" class="my-4 hidden space-y-4"
                                aria-labelledby="accordion-flush-heading-8">
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input id="weight-1" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="weight-1" class="ms-2 text-sm font-medium text-gray-900 "> Under 1
                                            kg </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="weight-2" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                            checked />
                                        <label for="weight-2" class="ms-2 text-sm font-medium text-gray-900 "> 1 kg to 5
                                            kg </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="weight-3" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="weight-3" class="ms-2 text-sm font-medium text-gray-900 "> 5 kg to
                                            10 kg </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="weight-4" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="weight-4" class="ms-2 text-sm font-medium text-gray-900 "> 10 kg to
                                            20 kg </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="weight-5" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="weight-5" class="ms-2 text-sm font-medium text-gray-900 "> Over 20
                                            kg </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                    <!-- Right content -->
                    <div class="w-full">
                        <!-- Product Cards -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-lg border border-gray-200 bg-white shadow-sm ">
                                <div class="relative">
                                    <div class="h-64 p-6">
                                        <a href="#">
                                            <img class="h-auto max-h-full w-full dark:hidden"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/apple-watch-light.svg"
                                                alt="watch image" />
                                            <img class="hidden h-auto max-h-full w-full dark:block"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/apple-watch-dark.svg"
                                                alt="watch image" />
                                        </a>
                                    </div>

                                    <div class="absolute right-0 top-0 p-1.5">
                                        <button type="button" data-tooltip-target="tooltip-add-to-favorites-9"
                                            class="rounded p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900  dark:hover:bg-gray-700 ">
                                            <span class="sr-only"> Add to Favorites </span>
                                            <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z" />
                                            </svg>
                                        </button>
                                        <div id="tooltip-add-to-favorites-9" role="tooltip"
                                            class="tooltip invisible absolute z-10 inline-block w-[132px] rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-600"
                                            data-popper-placement="top">
                                            Add to favorites
                                            <div class="tooltip-arrow" data-popper-arrow=""></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6 p-6">
                                    <div class="flex items-center justify-between space-x-4">
                                        <a href="#"
                                            class="text-lg font-semibold leading-tight text-gray-900 hover:underline ">Apple
                                            Watch SE - Gen 3</a>

                                        <p class="text-2xl font-extrabold leading-tight text-gray-900 ">
                                            $299</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <div class="mb-2 flex items-center gap-2">
                                                <label for="sizes"
                                                    class="flex items-center gap-1 text-sm font-medium text-gray-900 ">
                                                    Display size
                                                    <button data-tooltip-target="display-size-desc-1"
                                                        data-tooltip-trigger="hover"
                                                        class="text-gray-400 hover:text-gray-900 dark:text-gray-500 ">
                                                        <svg class="h-4 w-4" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd"
                                                                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    <div id="display-size-desc-1" role="tooltip"
                                                        class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 ">
                                                        Choose display size
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <select id="sizes"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option selected>Choose a size</option>
                                                <option value="20">20mm</option>
                                                <option value="38">38mm</option>
                                                <option value="40">40mm</option>
                                                <option value="45">45mm</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="product-color-1"
                                                class="mb-2 block text-sm font-medium text-gray-900 ">
                                                Color </label>
                                            <select id="product-color-1"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option>Choose a color</option>
                                                <option value="WH">Gray</option>
                                                <option value="BK">Black</option>
                                                <option value="GR">Gold</option>
                                                <option value="BL">Blue</option>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="button"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium  text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                        </svg>
                                        Add to cart
                                    </button>

                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-500 " aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                        </svg>
                                        <p class="text-sm font-normal text-gray-500 ">Estimated
                                            Delivery <span class="font-medium text-gray-900 ">Jun 23 -
                                                Jun 24</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white shadow-sm ">
                                <div class="relative">
                                    <div class="h-64 p-6">
                                        <a href="#">
                                            <img class="h-auto max-h-full w-full dark:hidden"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ps5-light.svg"
                                                alt="playstation image" />
                                            <img class="hidden h-auto max-h-full w-full dark:block"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ps5-dark.svg"
                                                alt="playstation image" />
                                        </a>
                                    </div>

                                    <div class="absolute right-0 top-0 p-1.5">
                                        <button type="button" data-tooltip-target="tooltip-add-to-favorites-10"
                                            class="rounded p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900  dark:hover:bg-gray-700 ">
                                            <span class="sr-only"> Add to Favorites </span>
                                            <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z" />
                                            </svg>
                                        </button>
                                        <div id="tooltip-add-to-favorites-10" role="tooltip"
                                            class="tooltip invisible absolute z-10 inline-block w-[132px] rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-600"
                                            data-popper-placement="top">
                                            Add to favorites
                                            <div class="tooltip-arrow" data-popper-arrow=""></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6 p-6">
                                    <div class="flex items-center justify-between space-x-4">
                                        <a href="#"
                                            class="text-lg font-semibold leading-tight text-gray-900 hover:underline ">PlayStation®5
                                            Console – 1TB</a>

                                        <p class="text-2xl font-extrabold leading-tight text-gray-900 ">
                                            $599</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <div class="mb-2 flex items-center gap-2">
                                                <label for="storage-size"
                                                    class="flex items-center gap-1 text-sm font-medium text-gray-900 ">
                                                    Storage
                                                    <button data-tooltip-target="storage-size-desc-1"
                                                        data-tooltip-trigger="hover"
                                                        class="text-gray-400 hover:text-gray-900 dark:text-gray-500 ">
                                                        <svg class="h-4 w-4" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd"
                                                                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    <div id="storage-size-desc-1" role="tooltip"
                                                        class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 ">
                                                        Choose storage size
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <select id="storage-size"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option selected>512GB</option>
                                                <option value="1">1TB</option>
                                                <option value="2">2TB</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="product-color-2"
                                                class="mb-2 block text-sm font-medium text-gray-900 ">
                                                Color </label>
                                            <select id="product-color-2"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option selected>Gray</option>
                                                <option value="WH">White</option>
                                                <option value="Bk">Black</option>
                                                <option value="RD">Red</option>
                                                <option value="BL">Blue</option>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="button"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium  text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                        </svg>
                                        Add to cart
                                    </button>

                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-500 " aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                        </svg>
                                        <p class="text-sm font-normal text-gray-500 ">Estimated
                                            Delivery <span class="font-medium text-gray-900 ">Tommorow</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white shadow-sm ">
                                <div class="relative">
                                    <div class="h-64 p-6">
                                        <a href="#">
                                            <img class="h-auto max-h-full w-full dark:hidden"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ipad-light.svg"
                                                alt="ipad image" />
                                            <img class="hidden h-auto max-h-full w-full dark:block"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/ipad-dark.svg"
                                                alt="ipad image" />
                                        </a>
                                    </div>

                                    <div class="absolute right-0 top-0 p-1.5">
                                        <button type="button" data-tooltip-target="tooltip-add-to-favorites-11"
                                            class="rounded p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900  dark:hover:bg-gray-700 ">
                                            <span class="sr-only"> Add to Favorites </span>
                                            <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z" />
                                            </svg>
                                        </button>
                                        <div id="tooltip-add-to-favorites-11" role="tooltip"
                                            class="tooltip invisible absolute z-10 inline-block w-[132px] rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-600"
                                            data-popper-placement="top">
                                            Add to favorites
                                            <div class="tooltip-arrow" data-popper-arrow=""></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6 p-6">
                                    <div class="flex items-center justify-between space-x-4">
                                        <a href="#"
                                            class="text-lg font-semibold leading-tight text-gray-900 hover:underline ">iPad
                                            Pro 13-Inch (M4)</a>

                                        <p class="text-2xl font-extrabold leading-tight text-gray-900 ">
                                            $1,299</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <div class="mb-2 flex items-center gap-2">
                                                <label for="storage-size-2"
                                                    class="flex items-center gap-1 text-sm font-medium text-gray-900 ">
                                                    Storage
                                                    <button data-tooltip-target="storage-size-desc-2"
                                                        data-tooltip-trigger="hover"
                                                        class="text-gray-400 hover:text-gray-900 dark:text-gray-500 ">
                                                        <svg class="h-4 w-4" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd"
                                                                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    <div id="storage-size-desc-2" role="tooltip"
                                                        class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 ">
                                                        Choose storage size
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <select id="storage-size-2"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option selected>512GB</option>
                                                <option value="1">1TB</option>
                                                <option value="2">2TB</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="product-color-3"
                                                class="mb-2 block text-sm font-medium text-gray-900 ">
                                                Color </label>
                                            <select id="product-color-3"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option value="GR">Gray</option>
                                                <option value="GD" selected>Gold</option>
                                                <option value="Bk">Space Black</option>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="button"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium  text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                        </svg>
                                        Add to cart
                                    </button>

                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-500 " aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                        </svg>
                                        <p class="text-sm font-normal text-gray-500 ">Estimated
                                            Delivery <span class="font-medium text-gray-900 ">Jul 2
                                                2024</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white shadow-sm ">
                                <div class="relative">
                                    <div class="h-64 p-6">
                                        <a href="#">
                                            <img class="h-auto max-h-full w-full dark:hidden"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front.svg"
                                                alt="imac image" />
                                            <img class="hidden h-auto max-h-full w-full dark:block"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/imac-front-dark.svg"
                                                alt="imac image" />
                                        </a>
                                    </div>

                                    <div class="absolute right-0 top-0 p-1.5">
                                        <button type="button" data-tooltip-target="tooltip-add-to-favorites-12"
                                            class="rounded p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900  dark:hover:bg-gray-700 ">
                                            <span class="sr-only"> Add to Favorites </span>
                                            <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z" />
                                            </svg>
                                        </button>
                                        <div id="tooltip-add-to-favorites-12" role="tooltip"
                                            class="tooltip invisible absolute z-10 inline-block w-[132px] rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-600"
                                            data-popper-placement="top">
                                            Add to favorites
                                            <div class="tooltip-arrow" data-popper-arrow=""></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6 p-6">
                                    <div class="flex items-center justify-between space-x-4">
                                        <a href="#"
                                            class="text-lg font-semibold leading-tight text-gray-900 hover:underline ">Apple
                                            iMac 27", M3 Max</a>

                                        <p class="text-2xl font-extrabold leading-tight text-gray-900 ">
                                            $2,899</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <div class="mb-2 flex items-center gap-2">
                                                <label for="storage-size-3"
                                                    class="flex items-center gap-1 text-sm font-medium text-gray-900 ">
                                                    Storage
                                                    <button data-tooltip-target="storage-size-desc-3"
                                                        data-tooltip-trigger="hover"
                                                        class="text-gray-400 hover:text-gray-900 dark:text-gray-500 ">
                                                        <svg class="h-4 w-4" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd"
                                                                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    <div id="storage-size-desc-3" role="tooltip"
                                                        class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 ">
                                                        Choose storage size
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <select id="storage-size-3"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option selected>512GB</option>
                                                <option value="256">256GB</option>
                                                <option value="1">1TB</option>
                                                <option value="2">2TB</option>
                                                <option value="4">4TB</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="product-color-4"
                                                class="mb-2 block text-sm font-medium text-gray-900 ">
                                                Color </label>
                                            <select id="product-color-4"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option>Choose a color</option>
                                                <option value="GR">Gray</option>
                                                <option value="GD">Gold</option>
                                                <option value="Bk">Space Black</option>
                                                <option value="PU">Purple</option>
                                                <option value="GR">Green</option>
                                                <option value="PK">Pink</option>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="button"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium  text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                        </svg>
                                        Add to cart
                                    </button>

                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-500 " aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                        </svg>
                                        <p class="text-sm font-normal text-gray-500 ">Estimated
                                            Delivery <span class="font-medium text-gray-900 ">Aug 14 -
                                                Aug 15</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white shadow-sm ">
                                <div class="relative">
                                    <div class="h-64 p-6">
                                        <a href="#">
                                            <img class="h-auto max-h-full w-full dark:hidden"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/macbook-pro-light.svg"
                                                alt="macbook pro image" />
                                            <img class="hidden h-auto max-h-full w-full dark:block"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/macbook-pro-dark.svg"
                                                alt="macbook pro image" />
                                        </a>
                                    </div>

                                    <div class="absolute right-0 top-0 p-1.5">
                                        <button type="button" data-tooltip-target="tooltip-add-to-favorites-13"
                                            class="rounded p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900  dark:hover:bg-gray-700 ">
                                            <span class="sr-only"> Add to Favorites </span>
                                            <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z" />
                                            </svg>
                                        </button>
                                        <div id="tooltip-add-to-favorites-13" role="tooltip"
                                            class="tooltip invisible absolute z-10 inline-block w-[132px] rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-600"
                                            data-popper-placement="top">
                                            Add to favorites
                                            <div class="tooltip-arrow" data-popper-arrow=""></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6 p-6">
                                    <div class="flex items-center justify-between space-x-4">
                                        <a href="#"
                                            class="text-lg font-semibold leading-tight text-gray-900 hover:underline ">Apple
                                            MacBook PRO M2 Max</a>

                                        <p class="text-2xl font-extrabold leading-tight text-gray-900 ">
                                            $4,999</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <div class="mb-2 flex items-center gap-2">
                                                <label for="storage-size-4"
                                                    class="flex items-center gap-1 text-sm font-medium text-gray-900 ">
                                                    Storage
                                                    <button data-tooltip-target="storage-size-desc-4"
                                                        data-tooltip-trigger="hover"
                                                        class="text-gray-400 hover:text-gray-900 dark:text-gray-500 ">
                                                        <svg class="h-4 w-4" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd"
                                                                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    <div id="storage-size-desc-4" role="tooltip"
                                                        class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 ">
                                                        Choose storage size
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <select id="storage-size-4"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option selected>512GB</option>
                                                <option value="256">256GB</option>
                                                <option value="1">1TB</option>
                                                <option value="2">2TB</option>
                                                <option value="4">4TB</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="product-color-5"
                                                class="mb-2 block text-sm font-medium text-gray-900 ">
                                                Color </label>
                                            <select id="product-color-5"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option>Choose a color</option>
                                                <option value="GR">Gray</option>
                                                <option value="GD">Gold</option>
                                                <option value="Bk">Space Black</option>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="button"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium  text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                        </svg>
                                        Add to cart
                                    </button>

                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-500 " aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                        </svg>
                                        <p class="text-sm font-normal text-gray-500 ">Estimated
                                            Delivery <span class="font-medium text-gray-900 ">Jul 2
                                                2024</span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-lg border border-gray-200 bg-white shadow-sm ">
                                <div class="relative">
                                    <div class="h-64 p-6">
                                        <a href="#">
                                            <img class="h-auto max-h-full w-full dark:hidden"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/iphone-light.svg"
                                                alt="iphone image" />
                                            <img class="hidden h-auto max-h-full w-full dark:block"
                                                src="https://flowbite.s3.amazonaws.com/blocks/e-commerce/iphone-dark.svg"
                                                alt="iphone image" />
                                        </a>
                                    </div>

                                    <div class="absolute right-0 top-0 p-1.5">
                                        <button type="button" data-tooltip-target="tooltip-add-to-favorites-14"
                                            class="rounded p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900  dark:hover:bg-gray-700 ">
                                            <span class="sr-only"> Add to Favorites </span>
                                            <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6C6.5 1 1 8 5.8 13l6.2 7 6.2-7C23 8 17.5 1 12 6Z" />
                                            </svg>
                                        </button>
                                        <div id="tooltip-add-to-favorites-14" role="tooltip"
                                            class="tooltip invisible absolute z-10 inline-block w-[132px] rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-600"
                                            data-popper-placement="top">
                                            Add to favorites
                                            <div class="tooltip-arrow" data-popper-arrow=""></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-6 p-6">
                                    <div class="flex items-center justify-between space-x-4">
                                        <a href="#"
                                            class="text-lg font-semibold leading-tight text-gray-900 hover:underline ">Apple
                                            iPhone 15 Pro Max</a>

                                        <p class="text-2xl font-extrabold leading-tight text-gray-900 ">
                                            $1,999</p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <div class="mb-2 flex items-center gap-2">
                                                <label for="storage-size-5"
                                                    class="flex items-center gap-1 text-sm font-medium text-gray-900 ">
                                                    Storage
                                                    <button data-tooltip-target="storage-size-desc-5"
                                                        data-tooltip-trigger="hover"
                                                        class="text-gray-400 hover:text-gray-900 dark:text-gray-500 ">
                                                        <svg class="h-4 w-4" aria-hidden="true"
                                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path fill-rule="evenodd"
                                                                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm9.408-5.5a1 1 0 1 0 0 2h.01a1 1 0 1 0 0-2h-.01ZM10 10a1 1 0 1 0 0 2h1v3h-1a1 1 0 1 0 0 2h4a1 1 0 1 0 0-2h-1v-4a1 1 0 0 0-1-1h-2Z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    <div id="storage-size-desc-5" role="tooltip"
                                                        class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 ">
                                                        Choose storage size
                                                        <div class="tooltip-arrow" data-popper-arrow></div>
                                                    </div>
                                                </label>
                                            </div>
                                            <select id="storage-size-5"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option selected>512GB</option>
                                                <option value="256">256GB</option>
                                                <option value="1">1TB</option>
                                                <option value="2">2TB</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="product-color-6"
                                                class="mb-2 block text-sm font-medium text-gray-900 ">
                                                Color </label>
                                            <select id="product-color-6"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                                <option>Choose a color</option>
                                                <option value="GR">Gray</option>
                                                <option value="GD">Gold</option>
                                                <option value="Bk">Space Black</option>
                                            </select>
                                        </div>
                                    </div>

                                    <button type="button"
                                        class="inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium  text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                        <svg class="-ms-2 me-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4h1.5L8 16m0 0h8m-8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm8 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm.75-3H7.5M11 7H6.312M17 4v6m-3-3h6" />
                                        </svg>
                                        Add to cart
                                    </button>

                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-500 " aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13 7h6l2 4m-8-4v8m0-8V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v9h2m8 0H9m4 0h2m4 0h2v-4m0 0h-5m3.5 5.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Zm-10 0a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z" />
                                        </svg>
                                        <p class="text-sm font-normal text-gray-500 ">Estimated
                                            Delivery <span class="font-medium text-gray-900 ">Jul 20 -
                                                Jul 24</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Pagination -->
                        <nav class="mt-6 flex items-center justify-center sm:mt-8" aria-label="Page navigation example">
                            <ul class="flex h-8 items-center -space-x-px text-sm">
                                <li>
                                    <a href="#"
                                        class="ms-0 flex h-8 items-center justify-center rounded-s-lg border border-e-0 border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700   dark:hover:bg-gray-700 ">
                                        <span class="sr-only">Previous</span>
                                        <svg class="h-4 w-4 rtl:rotate-180" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m15 19-7-7 7-7" />
                                        </svg>
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex h-8 items-center justify-center border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700   dark:hover:bg-gray-700 ">1</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex h-8 items-center justify-center border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700   dark:hover:bg-gray-700 ">2</a>
                                </li>
                                <li>
                                    <a href="#" aria-current="page"
                                        class="z-10 flex h-8 items-center justify-center border border-primary-300 bg-primary-50 px-3 leading-tight text-primary-600 hover:bg-primary-100 hover:text-primary-700 dark:border-gray-700  dark:hover:bg-gray-700 ">3</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex h-8 items-center justify-center border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700   dark:hover:bg-gray-700 ">...</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex h-8 items-center justify-center border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700   dark:hover:bg-gray-700 ">100</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="flex h-8 items-center justify-center rounded-e-lg border border-gray-300 bg-white px-3 leading-tight text-gray-500 hover:bg-gray-100 hover:text-gray-700   dark:hover:bg-gray-700 ">
                                        <span class="sr-only">Next</span>
                                        <svg class="h-4 w-4 rtl:rotate-180" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m9 5 7 7-7 7" />
                                        </svg>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Mobile Drawer -->
                <form action="#" method="get" id="drawer-mobile-filter"
                    class="fixed left-0 top-0 z-40 h-screen w-full max-w-sm -translate-x-full overflow-y-auto bg-white p-4 transition-transform dark:bg-gray-800"
                    tabindex="-1" aria-labelledby="drawer-label">
                    <h5 id="drawer-label-2"
                        class="mb-4 inline-flex items-center text-base font-semibold uppercase text-gray-500 ">
                        Filters mobile</h5>
                    <button type="button" data-drawer-dismiss="drawer-mobile-filter"
                        aria-controls="drawer-mobile-filter"
                        class="absolute right-2.5 top-2.5 inline-flex items-center rounded-lg bg-transparent p-1.5 text-sm text-gray-400 hover:bg-gray-100 hover:text-gray-900  ">
                        <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18 17.94 6M18 18 6.06 6" />
                        </svg>
                        <span class="sr-only">Close menu</span>
                    </button>

                    <div class="flex flex-1 flex-col justify-between">
                        <div class="space-y-4">
                            <!-- Product Brand -->
                            <div>
                                <label for="product-brand-2" class="mb-2 block text-sm font-medium text-gray-900 ">
                                    Product Brand
                                </label>
                                <select id="product-brand-2"
                                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    ">
                                    <option selected value="apple">Apple</option>
                                    <option value="lg">LG</option>
                                    <option value="samsung">Samsung</option>
                                    <option value="logitech">Logitech</option>
                                    <option value="lenovo">Lenovo</option>
                                    <option value="samsung">Philips</option>
                                    <option value="logitech">Microsoft</option>
                                    <option value="lenovo">Sony</option>
                                </select>
                            </div>

                            <!-- Price -->
                            <div>
                                <h6 class="mb-2 block text-sm font-medium text-gray-900 ">Price Range
                                </h6>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <input id="min-price" type="range" min="0" max="7000" value="300" step="1"
                                            class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200 " />
                                    </div>

                                    <div>
                                        <input id="max-price" type="range" min="0" max="7000" value="3500" step="1"
                                            class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-gray-200 " />
                                    </div>

                                    <div class="col-span-2 flex items-center justify-between space-x-4">
                                        <div class="w-full">
                                            <label for="min-price-input"
                                                class="mb-2 block text-sm font-medium text-gray-900 ">From</label>
                                            <input type="number" id="min-price-input" value="300" min="0" max="7000"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500     "
                                                placeholder="" required />
                                        </div>

                                        <div class="w-full">
                                            <label for="max-price-input"
                                                class="mb-2 block text-sm font-medium text-gray-900 ">To</label>
                                            <input type="number" id="max-price-input" value="3500" min="0" max="7000"
                                                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500    "
                                                placeholder="" required />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Delivery method -->
                                <div class="w-full space-y-3">
                                    <h6 class="mb-2 text-sm font-medium text-black ">Delivery method</h6>
                                    <div class="flex items-center">
                                        <input id="flowbox-2" type="radio" name="delivery" value=""
                                            class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="flowbox-2" class="ms-2 text-sm font-medium text-gray-900 "> Flowbox
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="pick-from-store-2" type="radio" name="delivery" value=""
                                            class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="pick-from-store-2" class="ms-2 text-sm font-medium text-gray-900 ">
                                            Pick from
                                            the store </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="courier-2" type="radio" name="delivery" value=""
                                            class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="courier-2" class="ms-2 text-sm font-medium text-gray-900 "> Fast
                                            courier </label>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <h6 class="mb-2 text-sm font-medium text-black ">Rating</h6>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input id="five-stars" type="radio" value="" name="rating"
                                                class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="five-stars" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>First star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Second star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Third star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fourth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fifth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="four-stars" type="radio" value="" name="rating"
                                                class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="four-stars" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>First star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Second star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Third star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fourth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fifth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="three-stars" type="radio" value="" name="rating" checked
                                                class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="three-stars" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>First star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Second star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Third star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fourth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fifth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="two-stars" type="radio" value="" name="rating"
                                                class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="two-stars" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>First star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Second star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Third star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fourth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fifth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="one-star" type="radio" value="" name="rating"
                                                class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                            <label for="one-star" class="ml-2 flex items-center">
                                                <svg aria-hidden="true" class="h-5 w-5 text-yellow-400"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>First star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Second star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Third star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fourth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                                <svg aria-hidden="true" class="h-5 w-5 text-gray-300 dark:text-gray-500"
                                                    fill="currentColor" viewBox="0 0 20 20"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <title>Fifth star</title>
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Condition -->
                            <div>
                                <h6 class="mb-2 text-sm font-medium text-black ">Condition</h6>

                                <ul
                                    class="flex w-full items-center rounded-lg border border-gray-200 bg-white text-sm font-medium text-gray-900   ">
                                    <li class="w-full border-r border-gray-200 ">
                                        <div class="flex items-center pl-3">
                                            <input id="condition-all" type="radio" value="" name="list-radio" checked
                                                class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-500 dark:bg-gray-600 dark:ring-offset-gray-700 dark:focus:ring-primary-600" />
                                            <label for="condition-all"
                                                class="ml-2 w-full py-3 text-sm font-medium text-gray-900 ">
                                                All </label>
                                        </div>
                                    </li>
                                    <li class="w-full border-r border-gray-200 ">
                                        <div class="flex items-center pl-3">
                                            <input id="condition-new" type="radio" value="" name="list-radio"
                                                class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-500 dark:bg-gray-600 dark:ring-offset-gray-700 dark:focus:ring-primary-600" />
                                            <label for="condition-new"
                                                class="ml-2 w-full py-3 text-sm font-medium text-gray-900 ">
                                                New </label>
                                        </div>
                                    </li>
                                    <li class="w-full">
                                        <div class="flex items-center pl-3">
                                            <input id="condition-used" type="radio" value="" name="list-radio"
                                                class="h-4 w-4 border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500 dark:border-gray-500 dark:bg-gray-600 dark:ring-offset-gray-700 dark:focus:ring-primary-600" />
                                            <label for="condition-used"
                                                class="ml-2 w-full py-3 text-sm font-medium text-gray-900 ">
                                                Used </label>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <!-- Color & Rating -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="w-full">
                                    <h6 class="mb-2 text-sm font-medium text-black ">Colour</h6>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input id="blue" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />

                                            <label for="blue"
                                                class="ml-2 flex items-center text-sm font-medium text-gray-900 ">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-primary-600"></div>
                                                Blue
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="gray" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />

                                            <label for="gray"
                                                class="ml-2 flex items-center text-sm font-medium text-gray-900 ">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-gray-400"></div>
                                                Gray
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="green" type="checkbox" value="" checked
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />

                                            <label for="green"
                                                class="ml-2 flex items-center text-sm font-medium text-gray-900 ">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-green-400"></div>
                                                Green
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="pink" type="checkbox" value=""
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />

                                            <label for="pink"
                                                class="ml-2 flex items-center text-sm font-medium text-gray-900 ">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-pink-400"></div>
                                                Pink
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input id="red" type="checkbox" value="" checked
                                                class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />

                                            <label for="red"
                                                class="ml-2 flex items-center text-sm font-medium text-gray-900 ">
                                                <div class="mr-2 h-3.5 w-3.5 rounded-full bg-red-500"></div>
                                                Red
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full space-y-3">
                                    <h6 class="mb-2 text-sm font-medium text-black ">Weight</h6>
                                    <div class="flex items-center">
                                        <input id="weight-6" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="weight-6" class="ms-2 text-sm font-medium text-gray-900 "> Under 1
                                            kg </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="weight-7" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   "
                                            checked />
                                        <label for="weight-7" class="ms-2 text-sm font-medium text-gray-900 "> 1 kg to 5
                                            kg </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="weight-8" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="weight-8" class="ms-2 text-sm font-medium text-gray-900 "> 5 kg to
                                            10 kg </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="weight-9" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="weight-9" class="ms-2 text-sm font-medium text-gray-900 "> 10 kg to
                                            20 kg </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="weight-10" type="checkbox" value=""
                                            class="h-4 w-4 rounded border-gray-300 bg-gray-100 text-primary-600 focus:ring-2 focus:ring-primary-500   " />
                                        <label for="weight-10" class="ms-2 text-sm font-medium text-gray-900 "> Over 20
                                            kg </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery -->
                            <div>
                                <h6 class="mb-2 text-sm font-medium text-black ">Shipping to</h6>

                                <ul class="grid gap-4 sm:grid-cols-2">
                                    <li>
                                        <input type="radio" id="delivery-usa" name="delivery" value="delivery-usa"
                                            class="peer hidden" checked />
                                        <label for="delivery-usa"
                                            class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-5 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-primary-600 peer-checked:text-primary-600   dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-primary-500">
                                            <div class="block">
                                                <div class="w-full text-base font-semibold">USA</div>
                                                <div class="w-full text-sm">Delivery only for USA</div>
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <input type="radio" id="delivery-europe" name="delivery" value="delivery-europe"
                                            class="peer hidden" />
                                        <label for="delivery-europe"
                                            class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-5 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-primary-600 peer-checked:text-primary-600   dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-primary-500">
                                            <div class="block">
                                                <div class="w-full text-base font-semibold">Europe</div>
                                                <div class="w-full text-sm">Delivery only for USA</div>
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <input type="radio" id="delivery-asia" name="delivery" value="delivery-asia"
                                            class="peer hidden" checked />
                                        <label for="delivery-asia"
                                            class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-5 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-primary-600 peer-checked:text-primary-600   dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-primary-500">
                                            <div class="block">
                                                <div class="w-full text-base font-semibold">Asia</div>
                                                <div class="w-full text-sm">Delivery only for Asia</div>
                                            </div>
                                        </label>
                                    </li>
                                    <li>
                                        <input type="radio" id="delivery-australia" name="delivery"
                                            value="delivery-australia" class="peer hidden" />
                                        <label for="delivery-australia"
                                            class="inline-flex w-full cursor-pointer items-center justify-between rounded-lg border border-gray-200 bg-white p-5 text-gray-500 hover:bg-gray-100 hover:text-gray-600 peer-checked:border-primary-600 peer-checked:text-primary-600   dark:hover:bg-gray-700 dark:hover:text-gray-300 dark:peer-checked:text-primary-500">
                                            <div class="block">
                                                <div class="w-full text-base font-semibold">Australia</div>
                                                <div class="w-full text-sm">Delivery only for Australia</div>
                                            </div>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="bottom-0 left-0 mt-6 flex w-full justify-center space-x-4 pb-4">
                            <button type="submit"
                                class="w-full rounded-lg bg-primary-700 px-5 py-2 text-center text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-700 dark:hover:bg-primary-800 dark:focus:ring-primary-800">Apply
                                filters</button>
                            <button type="reset"
                                class="w-full rounded-lg border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-200 ">Clear
                                all</button>
                        </div>
                    </div>
                </form>
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