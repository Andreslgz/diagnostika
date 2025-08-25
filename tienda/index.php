<?php
session_start();
require_once __DIR__ . '/../includes/db.php';


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
        <!-- banner -->
        <section>
            <img src="/assets/images/bannerTienda.webp" class="xl:flex hidden w-full" alt="">
            <img src="/assets/images/bannerTiendaMobile.webp" class="xl:hidden flex w-full" alt="">
        </section>
        <section class="py-8 antialiased  md:py-12">
            <div class="mx-auto max-w-screen-2xl px-4 2xl:px-0">
                <div class="mb-4 items-end justify-between sm:flex md:mb-8">
                    <!-- <div class="mb-4 sm:mb-0">
                        <h2 class="mt-3 text-xl font-semibold text-gray-900 sm:text-4xl">Nuestros Productos y paquetes
                        </h2>
                    </div> -->
                    <div class="flex items-center space-x-4">
                        <button data-drawer-target="drawer-mobile-filter" data-drawer-show="drawer-mobile-filter"
                            aria-controls="drawer-mobile-filter" type="button"
                            class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100  sm:w-auto lg:hidden">
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
                        <!-- <button id="sortDropdownButton2" data-dropdown-toggle="dropdownSort2" type="button"
                            class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100  sm:w-auto">
                            <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 4v16M7 4l3 3M7 4 4 7m9-3h6l-6 6h6m-6.5 10 3.5-7 3.5 7M14 18h4" />
                            </svg>
                            Sort by
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
                        </div> -->
                    </div>
                </div>
                <div class="gap-6 lg:flex ">

                    <div class="w-[350px] lg:flex hidden flex-col">
                        <h1 class="text-2xl font-bold mb-6">FILTERS</h1>

                        <div id="filters-panel">

                            <div class="border-2 border-solid border-gray-400 p-4 rounded-xl mb-5">
                                <header class="flex items-center justify-between">
                                    <p>
                                        BRAND SOFTWARE
                                    </p>
                                    <button class="" type="button">
                                        <svg class="-me-0.5 ms-2 h-  5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </button>
                                </header>
                                <div class="mt-1">
                                    <div class="flex flex-col gap-0.5">
                                        <?php if (!empty($marcas)): ?>
                                            <?php foreach ($marcas as $m): ?>
                                                <label class="inline-flex items-center justify-between">
                                                    <span class="flex items-center">
                                                        <input type="checkbox" class="form-checkbox" name="marca[]"
                                                            value="<?php echo htmlspecialchars($m['marca']); ?>" />
                                                        <span class="ml-2"><?php echo htmlspecialchars($m['marca']); ?></span>
                                                    </span>
                                                    <span
                                                        class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium bg-gray-100 rounded-full">
                                                        <?php echo $m['cantidad']; ?>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-sm text-gray-500">There are no brands registered</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="border-2 border-solid border-gray-400 p-4 rounded-xl mb-5">
                                <header class="flex items-center justify-between">
                                    <p>
                                        YEAR
                                    </p>
                                    <button class="" type="button">
                                        <svg class="-me-0.5 ms-2 h-  5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </button>
                                </header>
                                <div class="mt-1">
                                    <div class="flex flex-col gap-0.5">
                                        <?php if (!empty($anios)): ?>
                                            <?php foreach ($anios as $a): ?>
                                                <label class="inline-flex items-center justify-between">
                                                    <span class="flex items-center">
                                                        <input type="checkbox" class="form-checkbox" name="anio[]"
                                                            value="<?php echo htmlspecialchars($a['anio']); ?>" />
                                                        <span class="ml-2"><?php echo htmlspecialchars($a['anio']); ?></span>
                                                    </span>
                                                    <span
                                                        class="ml-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-medium bg-gray-100 rounded-full">
                                                        <?php echo $a['cantidad']; ?>
                                                    </span>
                                                </label>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p class="text-sm text-gray-500">There are no years registered</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <!-- Right content -->
                    <div class="w-full ">
                        <div
                            class="flex flex-col lg:flex-row lg:items-center lg:justify-between w-full mb-5 gap-4 lg:gap-0">
                            <!-- Filtros aplicados -->
                            <div class="flex gap-2 lg:gap-6 flex-wrap">
                                <div id="active-filters" class="flex gap-2 lg:gap-6 flex-wrap"></div>

                            </div>

                            <!-- Botón de ordenar -->
                            <div class="flex justify-end lg:justify-start">
                                <button id="sortDropdownButton2" data-dropdown-toggle="dropdownSort2" type="button"
                                    class="flex w-full lg:w-auto items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100">
                                    <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 4v16M7 4l3 3M7 4 4 7m9-3h6l-6 6h6m-6.5 10 3.5-7 3.5 7M14 18h4" />
                                    </svg>
                                    <span class="hidden sm:inline">Sort by</span>
                                    <span class="sm:hidden">Sort by</span>
                                    <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                        viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 9-7 7-7-7" />
                                    </svg>
                                </button>
                                <div id="dropdownSort2"
                                    class="z-50 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow "
                                    data-popper-placement="bottom">
                                    <ul id="ordenMenu" class="p-2 text-left text-sm font-medium text-gray-500 "
                                        aria-labelledby="sortDropdownButton">

                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                                                data-order="newest">
                                                Newest
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                                                data-order="price_desc">
                                                Price: High to Low
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                                                data-order="price_asc">
                                                Price: Low to High
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900"
                                                data-order="alpha">
                                                Alphabetically
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Product Cards -->

                        <div id="productosGrid" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4"></div>

                        <nav id="paginacion" class="flex items-center justify-center py-4 mt-12"
                            aria-label="Paginación"></nav>
                        <!-- Indicador de página actual (mejor para móvil) 
                        <div class="text-center mt-3 text-sm text-gray-600">
                            Página <span class="font-semibold text-gray-900">1</span> de <span
                                class="font-semibold">68</span>
                        </div>-->
                    </div>
                </div>

                <!-- Mobile Drawer -->
                <form action="#" method="get" id="drawer-mobile-filter"
                    class="fixed left-0 top-0 z-40 h-screen w-full max-w-sm -translate-x-full overflow-y-auto bg-white p-4 transition-transform "
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
                            <!-- Marca de Software -->
                            <div class="border-2 border-solid border-gray-400 p-4 rounded-xl mb-5">
                                <header class="flex items-center justify-between">
                                    <p>
                                        Marca de Software
                                    </p>
                                    <button class="" type="button">
                                        <svg class="-me-0.5 ms-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </button>
                                </header>
                                <div class="mt-1">
                                    <div class="flex flex-col gap-0.5">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Marca 1</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Marca 2</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Marca 3</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Año -->
                            <div class="border-2 border-solid border-gray-400 p-4 rounded-xl mb-5">
                                <header class="flex items-center justify-between">
                                    <p>
                                        Año
                                    </p>
                                    <button class="" type="button">
                                        <svg class="-me-0.5 ms-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </button>
                                </header>
                                <div class="mt-1">
                                    <div class="flex flex-col gap-0.5">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Año 1</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Año 2</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Año 3</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Industria -->
                            <div class="border-2 border-solid border-gray-400 p-4 rounded-xl mb-5">
                                <header class="flex items-center justify-between">
                                    <p>
                                        Industria
                                    </p>
                                    <button class="" type="button">
                                        <svg class="-me-0.5 ms-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </button>
                                </header>
                                <div class="mt-1">
                                    <div class="flex flex-col gap-0.5">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Industria 1</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Industria 2</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Industria 3</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Interfaz -->
                            <div class="border-2 border-solid border-gray-400 p-4 rounded-xl mb-5">
                                <header class="flex items-center justify-between">
                                    <p>
                                        Interfaz
                                    </p>
                                    <button class="" type="button">
                                        <svg class="-me-0.5 ms-2 h-5 w-5" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                            viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m19 9-7 7-7-7" />
                                        </svg>
                                    </button>
                                </header>
                                <div class="mt-1">
                                    <div class="flex flex-col gap-0.5">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Interfaz 1</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Interfaz 2</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox" />
                                            <span class="ml-2">Interfaz 3</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bottom-0 left-0 mt-6 flex w-full justify-center space-x-4 pb-4">
                            <button type="submit"
                                class="w-full rounded-lg bg-blue-700 px-5 py-2 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300">Apply
                                filters</button>
                            <button type="reset"
                                class="w-full rounded-lg border border-gray-200 bg-white px-5 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-200 ">Clear
                                all</button>
                        </div>
                    </div>
                </form>

            </div>
        </section>

        <div id="product-details-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 w-full h-full
            justify-center items-center
            overflow-y-auto overflow-x-hidden
            bg-black/60 backdrop-blur-sm"> <!-- 👈 fondo oscuro + blur -->
            <div class="relative p-4 w-full max-w-4xl max-h-full">
                <div class="relative bg-white rounded-lg shadow-sm">
                    <!-- Header -->
                    <div class="p-4 md:p-5 border-b rounded-t btn-secondary border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 id="modal-product-name" class="xl:text-xl text-sm font-semibold">Producto</h3>
                            <button type="button"
                                class="text-white bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                data-modal-hide="product-details-modal" aria-label="Cerrar">
                                <svg class="xl:w-4 w-2.5 xl:h-4 h-2.5" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                            </button>
                        </div>
                        <p id="modal-product-brand" class="text-white xl:text-lg text-sm xl:mt-8 mt-3"></p>
                    </div>

                    <!-- Body -->
                    <div class="xl:p-8 p-5 grid xl:grid-cols-2 grid-cols-1 xl:gap-10 gap-4 w-full">
                        <!-- Galería -->
                        <div class="max-w-2xl mx-auto">
                            <section aria-label="Galería de imágenes">
                                <div class="relative mb-4 overflow-hidden border border-gray-200 bg-white">
                                    <div class="aspect-square">
                                        <img id="mainImage" src="https://placehold.co/600x600/png"
                                            alt="Imagen principal"
                                            class="h-full w-full object-cover transition-opacity duration-300"
                                            loading="eager" decoding="async" draggable="false" />
                                    </div>
                                </div>

                                <div class="relative">
                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 w-10 bg-gradient-to-r from-gray-50 to-transparent">
                                    </div>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 w-10 bg-gradient-to-l from-gray-50 to-transparent">
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button id="prev"
                                            class="shrink-0 rounded-md bg-gray-800 text-white px-3 py-2 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                            aria-label="Anterior">&#10094;</button>

                                        <div id="thumbs"
                                            class="relative flex gap-2 overflow-x-auto scrollbar-hide scroll-smooth snap-x snap-mandatory py-2"
                                            role="listbox" aria-label="Miniaturas"></div>

                                        <button id="next"
                                            class="shrink-0 rounded-md bg-gray-800 text-white px-3 py-2 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                            aria-label="Siguiente">&#10095;</button>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Info -->
                        <div class="w-full flex flex-col justify-between">
                            <div>
                                <div class="flex flex-row justify-between items-center w-full">
                                    <p id="modal-product-price" class="xl:text-3xl text-lg text-nowrap font-bold">USD
                                        0.00</p>

                                    <div class="relative mt-2 flex max-w-32 items-center justify-end">
                                        <button type="button" id="decrement-button"
                                            class="xl:h-10 h-8 rounded-s-lg border border-gray-300 bg-gray-100 xl:p-3 p-2 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                            <svg class="h-3 w-3 text-gray-900" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 18 2">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                            </svg>
                                        </button>
                                        <input type="text" id="quantity-input-1" data-input-counter
                                            data-input-counter-min="1" data-input-counter-max="50"
                                            class="block xl:h-10 h-8 w-full border-x-0 border-gray-300 bg-gray-50 py-2.5 text-center text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
                                            value="1" />
                                        <button type="button" id="increment-button"
                                            class="xl:h-10 h-8 rounded-e-lg border border-gray-300 bg-gray-100 xl:p-3 p-2 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                            <svg class="h-3 w-3 text-gray-900" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 18 18">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <p id="modal-product-description"
                                    class="mt-4 text-gray-700 text-sm xl:text-base leading-relaxed">
                                    Producto sin descripción.
                                </p>


                            </div>

                            <div class="flex flex-col gap-4 xl:mt-0 mt-4">
                                <button id="modal-add-to-cart"
                                    class="btn-secondary w-full py-3 font-bold text-base xl:text-lg shadow xl:shadow-lg rounded-lg">
                                    ADD TO CART
                                </button>
                                <a id="modal-more-details" href="#"
                                    class="block text-center btn-primary w-full py-3 font-bold text-base xl:text-lg shadow xl:shadow-lg rounded-lg">
                                    MORE DETAILS
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </main>
    <!-- FOOTER -->
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <!-- MODALS -->

    <?php require_once __DIR__ . '/../includes/modal_login_registro.php'; ?>

    <!-- DRAWER -->
    <?php require_once __DIR__ . '/../includes/carrito_home.php'; ?>



    <div id="alertaFavorito"
        class="hidden fixed top-5 right-5 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50 text-sm"
        role="alert">
        <strong class="font-bold">¡Atención!</strong>
        <span class="block" id="alertaTexto"></span>
    </div>



    <!-- SCRIPTS en este orden -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>


    <!-- Splide.js DEBE ir antes del modal script -->
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet" />

    <script>
        document.addEventListener("click", (ev) => {
            const link = ev.target.closest("#ordenMenu a[data-order]");
            if (!link) return;
            ev.preventDefault();

            const selectedOrder = link.dataset.order || "newest";
            cargarProductos({ page: 1, order: selectedOrder });
        });
    </script>

    <script src="<?php echo $url; ?>/scripts/main.js"></script>
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