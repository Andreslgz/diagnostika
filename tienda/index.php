<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

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
                        <!-- 
                        <div class="border-2 border-solid border-gray-400 p-4 rounded-xl mb-5">
                            <header class="flex items-center justify-between">
                                <p>
                                    Industria
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
                        <div class="border-2 border-solid border-gray-400 p-4 rounded-xl mb-5">
                            <header class="flex items-center justify-between">
                                <p>
                                    Interfaz
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
                        -->

                        <!-- <button
                            class="w-full btn-secondary py-2.5 mt-3 rounded-lg font-bold text-lg  transition-all duration-200 easy-in-out">
                            Aplicar filtros
                        </button> -->
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
                                    <span class="sm:hidden">Ordenar</span>
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
                                    <ul class="p-2 text-left text-sm font-medium text-gray-500 "
                                        aria-labelledby="sortDropdownButton">
                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                                The most popular </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                                Newest </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                                Increasing price </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                                Decreasing price </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                                No. reviews </a>
                                        </li>
                                        <li>
                                            <a href="#"
                                                class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900">
                                                Discount % </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- Product Cards -->

                        <div id="productosGrid" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4"></div>

                        <nav id="paginacion" class="flex items-center justify-center py-4 mt-12"
                            aria-label="Paginación"></nav>
                        <!-- Indicador de página actual (mejor para móvil) -->
                        <div class="text-center mt-3 text-sm text-gray-600">
                            Página <span class="font-semibold text-gray-900">1</span> de <span
                                class="font-semibold">68</span>
                        </div>
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

    <div id="static-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Static modal
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="static-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        With less than a month to go before the European Union enacts new consumer privacy laws for its
                        citizens, companies around the world are updating their terms of service agreements to comply.
                    </p>
                    <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                        The European Union’s General Data Protection Regulation (G.D.P.R.) goes into effect on May 25
                        and is
                        meant to ensure a common set of data rights in the European Union. It requires organizations to
                        notify users as soon as possible of high-risk data breaches that could personally affect them.
                    </p>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                    <button data-modal-hide="static-modal" type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I
                        accept</button>
                    <button data-modal-hide="static-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Decline</button>
                </div>
            </div>
        </div>
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

    <style>
        .bg-cards {
            background: linear-gradient(0deg, #A7A7A6 0%, #DEDEDE 100%);
        }

        .bg-cards:hover {
            background: linear-gradient(0deg, #8A8A89 0%, #C0C0C0 100%);
        }

        /* ============================================
   ESTILOS PARA MODAL CON SPLIDE.JS
   ============================================ */

        /* Estilos personalizados para Splide en el modal */
        .modal-carousel-arrows {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }

        .modal-carousel-arrow {
            background: rgba(0, 0, 0, 0.5) !important;
            border: none !important;
            border-radius: 50% !important;
            width: 44px !important;
            height: 44px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s ease !important;
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: auto;
        }

        .modal-carousel-arrow:hover {
            background: rgba(0, 0, 0, 0.7) !important;
            transform: translateY(-50%) scale(1.1) !important;
        }

        .modal-carousel-arrow:disabled {
            opacity: 0.3 !important;
        }

        .modal-carousel-arrow svg {
            fill: white !important;
            width: 20px !important;
            height: 20px !important;
        }

        .modal-carousel-prev {
            left: 12px !important;
        }

        .modal-carousel-next {
            right: 12px !important;
        }

        /* Mostrar controles al hacer hover en el contenedor */
        .product-carousel-container:hover .modal-carousel-arrow {
            opacity: 1;
        }

        /* Estilos para el carrusel principal */
        #product-carousel .splide__track {
            border-radius: 0.75rem;
            /* rounded-xl */
            overflow: hidden;
        }

        #product-carousel .splide__slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Estilos para thumbnails */
        #product-thumbnails .splide__slide {
            opacity: 0.6;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }

        #product-thumbnails .splide__slide.is-active {
            opacity: 1;
        }

        #product-thumbnails .splide__slide:hover {
            opacity: 0.8;
        }

        #product-thumbnails .splide__slide img {
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        #product-thumbnails .splide__slide.is-active img {
            border: 2px solid #f59e0b;
            /* amber-500 */
            box-shadow: 0 0 0 1px rgba(245, 158, 11, 0.2);
        }

        /* Controles de cantidad */
        .quantity-decrease,
        .quantity-increase {
            transition: all 0.2s ease;
            border: none;
            background: none;
        }

        .quantity-decrease:hover,
        .quantity-increase:hover {
            background-color: #f3f4f6;
        }

        #product-quantity {
            border: none;
            outline: none;
            text-align: center;
            font-weight: 500;
            background: transparent;
        }

        #product-quantity:focus {
            background-color: #f9fafb;
        }

        /* Descripción técnica expandible */
        .tech-description-toggle {
            transition: all 0.2s ease;
        }

        .tech-description-arrow {
            transition: transform 0.2s ease;
        }

        .tech-description-content {
            transition: max-height 0.3s ease-out;
            overflow: hidden;
        }

        /* Animaciones del modal */
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        #modal_previsualizar .relative.bg-white {
            animation: modalSlideIn 0.3s ease-out;
        }

        /* Loading animation mejorada */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .modal-carousel-arrow {
                width: 36px !important;
                height: 36px !important;
            }

            .modal-carousel-arrow svg {
                width: 16px !important;
                height: 16px !important;
            }

            #modal_previsualizar .relative.p-4 {
                max-width: 95vw;
            }
        }

        @media (max-width: 768px) {
            .modal-carousel-arrow {
                width: 32px !important;
                height: 32px !important;
            }

            .modal-carousel-arrow svg {
                width: 14px !important;
                height: 14px !important;
            }

            #product-thumbnails {
                margin-top: 0.75rem !important;
            }
        }

        /* Mejoras de accesibilidad */
        .modal-carousel-arrow:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        .tech-description-toggle:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Efectos visuales adicionales */
        .product-image-overlay {
            background: linear-gradient(to bottom,
                    transparent 0%,
                    transparent 60%,
                    rgba(0, 0, 0, 0.1) 100%);
            transition: opacity 0.3s ease;
        }

        /* Personalización adicional de Splide */
        .splide__pagination {
            bottom: 0.5rem !important;
        }

        .splide__pagination__page {
            background: rgba(255, 255, 255, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.8) !important;
        }

        .splide__pagination__page.is-active {
            background: #f59e0b !important;
            /* amber-500 */
            border-color: #f59e0b !important;
        }

        /* Estados del carrusel */
        .splide.is-initialized {
            visibility: visible;
        }

        .splide:not(.is-initialized) {
            visibility: hidden;
        }

        /* Smooth transitions para slides */
        .splide__slide {
            transition: opacity 0.3s ease;
        }

        /* Indicador de múltiples imágenes */
        .multiple-images-indicator {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            backdrop-filter: blur(4px);
            z-index: 5;
        }
    </style>
</body>

</html>