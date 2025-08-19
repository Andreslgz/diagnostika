<?php
session_start();
require_once __DIR__ . '/includes/db.php';

error_reporting(E_ALL);

// Mostrar errores en pantalla
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$productos = $database->select("productos(p)", [
    "[>]caracteristicas_productos(c)" => ["p.id_producto" => "id_producto"]
], [
    "p.id_producto",
    "p.nombre",
    "p.precio",
    "p.imagen",
    "p.descripcion",
    "c.marca"
], [
    "ORDER" => ["p.id_producto" => "DESC"],
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

$sliders = $database->select("slider", "sl_img", [
    "sl_est" => "activo",
    "ORDER" => ["sl_id" => "DESC"]
]);

$sliders_mov = $database->select("slider", "sl_img_mov", [
    "sl_est" => "activo",
    "ORDER" => ["sl_id" => "DESC"]
]);

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
    <?php require_once __DIR__ . '/includes/top_header.php'; ?>
    <!-- HEADER - NAVBAR -->
    <?php require_once __DIR__ . '/includes/header.php'; ?>

    <main>

        <!-- Hero -->
        <section id="image-carousel" class="splide hidden md:block" aria-label="Beautiful Images">
            <div class="splide__track xl:h-[85vh] h-[70vh]">
                <ul class="splide__list">
                    <?php


                    if ($sliders && count($sliders) > 0) {
                        foreach ($sliders as $img) {
                            $imagen = htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                    ?>
                            <li class="splide__slide">
                                <img src="uploads/slider/<?= $imagen ?>" alt="Slide" class="" />
                            </li>
                        <?php
                        }
                    } else {
                        // Fallback si no hay imágenes activas
                        ?>
                        <li class="splide__slide">
                            <img src="uploads/slider/img-no-disponible.jpg" alt="Sin imagen" class="" />
                        </li>
                    <?php
                    }
                    ?>
                </ul>
            </div>
        </section>

        <!-- Hero Mobile -->
        <section id="image-carousel-mobile" class="splide md:hidden" aria-label="Beautiful Images (Mobile)">
            <div class="splide__track h-[50vh]">
                <ul class="splide__list">
                    <?php if (!empty($sliders_mov)): ?>
                        <?php foreach ($sliders_mov as $img): ?>
                            <?php $imagen = htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>
                            <li class="splide__slide">
                                <img src="uploads/slider/<?= $imagen ?>" alt="Slide Mobile" class="w-full h-full object-cover">
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="splide__slide">
                            <img src="uploads/slider/img-no-disponible.jpg" alt="Sin imagen" class="w-full h-full object-cover">
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </section>

        <!-- STATISTICS -->
        <section class="bg-statistics">
            <div class="xl:py-6 py-2 px-4 xl:px-0 mx-auto max-w-screen-2xl gap-10 overflow-hidden">

                <!-- Desktop Grid (md+) -->
                <div class="hidden md:grid grid-cols-4 gap-4" data-aos="fade-up" id="statistics-grid">
                    <div class="xl:p-4 p-1 rounded-lg flex flex-row xl:gap-4 gap-3 items-center">
                        <img src="assets/icons/banner/icon1.svg" alt="" class="xl:w-auto xl:h-auto w-[25%] h-auto" />
                        <div class="">
                            <p class="font-bold xl:text-3xl text-lg">1 Year</p>
                            <p class="xl:text-lg text-base font-medium text-nowrap xl:mt-0 -mt-2">
                                Warranty
                            </p>
                        </div>
                    </div>
                    <div class="xl:p-4 p-1 rounded-lg flex flex-row xl:gap-4 gap-3 items-center">
                        <img src="assets/icons/banner/icon2.svg" alt="" class="xl:w-auto xl:h-auto w-[25%] h-auto" />
                        <div class="">
                            <p class="font-bold xl:text-3xl text-lg">24/7</p>
                            <p class="xl:text-lg text-base font-medium text-nowrap xl:mt-0 -mt-2">
                                Support
                            </p>
                        </div>
                    </div>
                    <div class="xl:p-4 p-1 rounded-lg flex flex-row xl:gap-4 gap-3 items-center">
                        <img src="assets/icons/banner/icon3.svg" alt="" class="xl:w-auto xl:h-auto w-[25%] h-auto" />
                        <div class="">
                            <p class="font-bold xl:text-3xl text-lg">Global</p>
                            <p class="xl:text-lg text-base font-medium text-nowrap xl:mt-0 -mt-2">
                                Coverage
                            </p>
                        </div>
                    </div>
                    <div class="xl:p-4 p-1 rounded-lg flex flex-row xl:gap-4 gap-3 items-center">
                        <img src="assets/icons/banner/icon4.svg" alt="" class="xl:w-auto xl:h-auto w-[25%] h-auto" />
                        <div class="">
                            <p class="font-bold xl:text-3xl text-lg">123K +</p>
                            <p class="xl:text-lg text-base font-medium text-nowrap xl:mt-0 -mt-2">
                                Installations
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mobile Carousel (< md) -->
                <div class="splide block md:hidden relative" id="statistics-carousel" data-aos="fade-up">
                    <div class="splide__track h-min">
                        <ul class="splide__list">
                            <li class="splide__slide">
                                <div class="grid grid-cols-2 gap-3 ">
                                    <div class="p-2 rounded-lg flex flex-row gap-2 items-center min-h-[80px]">
                                        <img src="assets/icons/banner/icon1.svg" alt="" class="w-8 h-8 flex-shrink-0" />
                                        <div class="flex-1">
                                            <p class="font-bold text-lg leading-tight">1 Año</p>
                                            <p class="text-sm font-medium text-nowrap">
                                                de garantia
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-2 rounded-lg flex flex-row gap-2 items-center min-h-[80px]">
                                        <img src="assets/icons/banner/icon2.svg" alt="" class="w-8 h-8 flex-shrink-0" />
                                        <div class="flex-1">
                                            <p class="font-bold text-lg leading-tight">24/7</p>
                                            <p class="text-sm font-medium text-nowrap">
                                                Soporte técnico
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="splide__slide">
                                <div class="grid grid-cols-2 gap-3 py-2">
                                    <div class="p-2 rounded-lg flex flex-row gap-2 items-center min-h-[80px]">
                                        <img src="assets/icons/banner/icon3.svg" alt="" class="w-8 h-8 flex-shrink-0" />
                                        <div class="flex-1">
                                            <p class="font-bold text-lg leading-tight">Cobertura</p>
                                            <p class="text-sm font-medium text-nowrap">
                                                Global
                                            </p>
                                        </div>
                                    </div>
                                    <div class="p-2 rounded-lg flex flex-row gap-2 items-center min-h-[80px]">
                                        <img src="assets/icons/banner/icon4.svg" alt="" class="w-8 h-8 flex-shrink-0" />
                                        <div class="flex-1">
                                            <p class="font-bold text-lg leading-tight">123K +</p>
                                            <p class="text-sm font-medium text-nowrap">
                                                Instalaciones
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>


                </div>

            </div>
        </section>

        <!-- PRODUCTS -->
        <section data-aos="fade-up">
            <div class="xl:py-20 py-10 px-4 mx-auto max-w-screen-2xl overflow-hidden">
                <div
                    class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-10 w-full gap-4 sm:gap-0">
                    <h2 class="uppercase font-extrabold xl:text-3xl lg:text-2xl md:text-xl text-base">
                        Most in-demand software </h2>
                    <a href="<?php echo $url; ?>/tienda"
                        class="btn-primary rounded px-4 sm:px-6 lg:px-8 py-2 uppercase font-bold text-sm sm:text-base xl:text-lg flex items-center gap-2 cursor-pointer hover:underline underline-offset-4 self-start sm:self-auto">
                        SEE ALL
                        <img src="assets/icons/svg/tabler--chevron-right.svg" alt="" />
                    </a>
                </div>
                <div class="mx-auto max-w-screen-xl">
                    <!-- Products Carousel -->

                    <section id="products-carousel" class="splide" aria-label="Featured Products">
                        <div class="splide__track xl:h-[61vh] h-[47vh]">
                            <ul id="productos-lista" class="splide__list">
                                <?php foreach ($productos as $prod): ?>
                                    <li class="splide__slide !pb-4 sm:!pb-6 lg:!pb-10 !pr-2 lg:!pr-6 !pl-2 sm:!pl-4 lg:!pl-6">
                                        <div class="border border-gray-100 border-solid shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg p-2 sm:p-4 lg:p-6 flex flex-col gap-3 sm:gap-3 h-full">

                                            <div class="flex justify-end -mb-1">
                                                <button type="button" class="favorito-btn" data-id="<?= (int)$prod['id_producto']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        fill="<?= in_array($prod['id_producto'], $favoritos_usuario) ? 'currentColor' : 'none'; ?>"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5 sm:w-6 sm:h-6 transition-all duration-200 <?= in_array($prod['id_producto'], $favoritos_usuario) ? 'text-red-600' : 'text-gray-600'; ?>">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6.75 3.75h10.5a.75.75 0 01.75.75v15.375a.375.375 0 01-.6.3L12 16.5l-5.4 3.675a.375.375 0 01-.6-.3V4.5a.75.75 0 01.75-.75z" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <img src="<?= !empty($prod['imagen']) ? 'uploads/' . $prod['imagen'] : 'https://placehold.co/600x400/png'; ?>"
                                                alt="<?= htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                                                class="w-full h-36 sm:h-36 lg:h-48 object-cover rounded-md" />

                                            <p class="inline font-semibold text-base lg:text-xl text-balance leading-tight uppercase">
                                                <?= htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                            </p>

                                            <p class="inline text-base lg:text-2xl uppercase font-bold">
                                                USD <?= number_format((float)$prod['precio'], 2); ?>
                                            </p>

                                            <div class="flex flex-col gap-2 sm:gap-3 mt-auto">
                                                <!-- Botón AJAX: agregar al carrito -->
                                                <button
                                                    type="button"
                                                    class="btn-secondary add-to-cart inline w-full py-1.5 sm:py-2 rounded-lg uppercase font-semibold text-xs sm:text-base"
                                                    data-id="<?= (int)$prod['id_producto']; ?>"
                                                    data-qty="1"
                                                    aria-label="Añadir <?= htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8'); ?> al carrito">
                                                    <span>Añadir al carrito</span>
                                                </button>

                                                <?php
                                                // Trae solo los nombres de archivo de la galería
                                                $galeria = $database->select(
                                                    "galeria_productos",
                                                    "gal_img",
                                                    [
                                                        "id_producto" => (int)$prod["id_producto"],
                                                        "gal_est"     => "activo",
                                                        "ORDER"       => ["gal_id" => "DESC"]
                                                    ]
                                                );

                                                // Prefija cada imagen con /uploads/
                                                $galeria_full = array_map(
                                                    fn($f) => '/uploads/' . ltrim((string)$f, '/'),
                                                    is_array($galeria) ? $galeria : []
                                                );

                                                // Imagen principal del producto
                                                $imagen_principal = !empty($prod['imagen'])
                                                    ? '/uploads/' . ltrim((string)$prod['imagen'], '/')
                                                    : null;

                                                // Insertar primero la imagen principal (si existe)
                                                if ($imagen_principal) {
                                                    array_unshift($galeria_full, $imagen_principal);
                                                }

                                                // Convierte a JSON seguro para atributo HTML
                                                $data_gallery = htmlspecialchars(
                                                    json_encode($galeria_full, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                );
                                                ?>

                                                <button
                                                    type="button"
                                                    class="flex flex-row items-center justify-center gap-2 border border-gray-400 rounded-lg py-1.5 sm:py-2 uppercase font-semibold text-sm sm:text-base preview"
                                                    data-id="<?= (int)$prod['id_producto']; ?>"
                                                    data-name="<?= htmlspecialchars($prod['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-price="<?= number_format((float)$prod['precio'], 2, '.', ''); ?>"
                                                    data-img="<?= !empty($prod['imagen']) ? '/uploads/' . $prod['imagen'] : ''; ?>"
                                                    data-brand="<?= htmlspecialchars($prod['marca'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-desc="<?= htmlspecialchars($prod['descripcion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                    data-gallery='<?= $data_gallery; ?>'>
                                                    <div class="btn-secondary size-[24px] items-center flex rounded-full justify-center">
                                                        <img src="<?php echo $url;?>/assets/icons/tienda/previsualizar.svg" alt="Preview icon">
                                                    </div>
                                                    <p>PREVIEW</p>
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </section>

                </div>

                <!-- MARCAS -->

                <section id="brands-grid"
                    data-endpoint="/ajax_productos.php"
                    class="mx-auto max-w-screen-xl grid xl:grid-cols-6 grid-cols-3 xl:gap-6 gap-4 mt-12">
                    <button type="button" class="brands-bg brand-tile rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg p-1.5" data-brand="CAT">
                        <img src="assets/images/logos/logo1.svg" alt="CAT" />
                    </button>
                    <button type="button" class="brands-bg brand-tile rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg p-1.5" data-brand="JCB">
                        <img src="assets/images/logos/logo2.svg" alt="JCB" />
                    </button>
                    <button type="button" class="brands-bg brand-tile rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg p-1.5" data-brand="CUMMINS">
                        <img src="assets/images/logos/logo3.svg" alt="CUMMINS" />
                    </button>
                    <button type="button" class="brands-bg brand-tile rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg p-1.5" data-brand="PACCAR">
                        <img src="assets/images/logos/logo4.svg" alt="PACCAR" />
                    </button>
                    <button type="button" class="brands-bg brand-tile rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg p-1.5" data-brand="NOREGON">
                        <img src="assets/images/logos/logo5.svg" alt="NOREGON" />
                    </button>
                    <button type="button" class="brands-bg brand-tile rounded-lg flex items-center justify-center aspect-square shadow-md hover:shadow-lg p-1.5" data-brand="NEXIQ">
                        <img src="assets/images/logos/logo6.svg" alt="NEXIQ" />
                    </button>
                </section>

                <!-- El contenedor que recibe los <li> -->
                <ul id="productos-lista" class="splide__list">
                    <!-- tus <li> iniciales -->
                </ul>

            </div>
        </section>

        <!-- BANNERS -->
        <section class="px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 h-[300px] xl:h-auto">
                <!-- Banner 1 -->
                <div class="relative h-full">
                    <div class="flex flex-row  ">
                        <!-- Contenido -->
                        <div
                            class="btn-secondary xl:w-[32%] w-[51%] h-auto rounded-l-lg xl:p-4 p-2.5 flex flex-col justify-end">
                            <img src="assets/icons/banner/banner1.svg" alt=""
                                class="xl:size-[125px] size-[35px] mx-0" />
                            <p class="xl:text-2xl text-sm font-extrabold text-banner-1 text-left ">
                                On Highway
                            </p>
                            <p class="text-white xl:text-lg text-xs font-semibold text-left">Package</p>
                            <button
                                class="xl:mt-5 mt-2 text-nowrap w-full text-center btn-primary rounded-lg xl:px-8 xl:py-2 py-1 uppercase xl:font-bold semibold xl:text-base text-xs flex items-center xl:gap-2 cursor-pointer justify-center hover:underline underline-offset-4 mx-auto">
                                Add to CART
                            </button>
                        </div>
                        <!-- Imagen -->
                        <div class="xl:w-[70%] w-full h-auto image-anime rounded-r-lg">
                            <img src="assets/images/banner1.jpg" alt=""
                                class="w-full h-full object-cover  rounded-r-xl" />
                        </div>
                    </div>
                    <!-- Badge -->
                    <div class="absolute top-0 right-0 flex scale-100 origin-top-right">
                        <div class="btn-secondary xl:px-4 px-2 xl:py-1 py-0.5 font-extrabold xl:text-4xl ">
                            +
                        </div>
                        <div
                            class="btn-primary xl:px-5 px-3 xl:py-1 py-0.5 flex items-center italic font-bold xl:text-base text-xs">
                            100 SOFTWARES INCLUIDE
                        </div>
                    </div>
                </div>
                <div class="relative ">
                    <div class="flex flex-row h-full">
                        <!-- Contenido -->
                        <div
                            class="bg-banner2 xl:w-[32%] w-[51%] h-auto rounded-l-lg xl:p-4 p-2.5 flex flex-col justify-end">
                            <div class="flex gap-4 items-center ">
                                <img src="assets/icons/banner/banner2.svg" alt=""
                                    class="xl:size-[75px] block size-[45px] mx-0 border-r pr-4 border-gray-500" />
                                <img src="assets/icons/banner/banner3.svg" alt=""
                                    class="xl:size-[75px] block size-[30px] mx-0" />
                            </div>
                            <p class="xl:text-2xl text-sm font-extrabold text-banner-1 text-left xl:mt-4">
                                Off Highway
                            </p>
                            <p class="text-white xl:text-lg text-xs font-semibold text-left">Package</p>
                            <button
                                class="xl:mt-5 mt-2 text-nowrap w-full text-center btn-primary rounded-lg xl:px-8 xl:py-2 py-1 uppercase xl:font-bold semibold xl:text-base text-xs flex items-center xl:gap-2 cursor-pointer justify-center hover:underline underline-offset-4 mx-auto">
                                Add to CART
                            </button>
                        </div>
                        <!-- Imagen -->
                        <div class="xl:w-[70%] w-full h-auto image-anime rounded-r-lg">
                            <img src="assets/images/banner2.jpg" alt=""
                                class="w-full h-full object-cover  rounded-r-xl" />
                        </div>
                    </div>
                    <!-- Badge -->
                    <div class="absolute top-0 right-0 flex scale-100 origin-top-right">
                        <div class="btn-secondary xl:px-4 px-2 xl:py-1 py-0.5 font-extrabold xl:text-4xl ">
                            +
                        </div>
                        <div
                            class="btn-primary xl:px-5 px-3 xl:py-1 py-0.5 flex items-center italic font-bold xl:text-base text-xs">
                            100 SOFTWARES INCLUIDE
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- STATITICS 2 -->
        <section class="my-10 md:my-20 bg-statistics-2 to-[#D9D9D9] overflow-hidden">
            <div class="py-10 md:py-20 px-4 mx-auto max-w-screen-2xl overflow-hidden">
                <img src="assets/icons/Logotipo.svg" alt="" class="mx-auto block w-[220px] md:w-[320px] lg:w-[440px]" />
                <div class="grid grid-cols-1 lg:grid-cols-2 mt-8 md:mt-16 gap-6 lg:gap-0">
                    <div class="grid grid-cols-1 xl:hidden gap-8">
                        <div class="mx-auto overflow-hidden">
                            <div class="flex justify-between items-center cursor-pointer " onclick="toggleAccordion()">
                                <div
                                    class="btn-secondary w-[55px] h-[55px] rounded-full flex items-center justify-center flex-shrink-0">
                                    <img src="assets/icons/estadisticas/1.svg" alt="" class="w-[70%]">
                                </div>
                                <div class="flex-1 mx-4">
                                    <h2 class="font-bold text-base">
                                        Specialized Technical Support
                                    </h2>
                                </div>
                                <div>
                                    <svg class="w-6 h-6 text-gray-500 arrow-transition" id="arrow" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="accordion-content" id="accordionContent">
                                <div class="pl-[75px] pr-[20px]">
                                    <div class="text-gray-900 space-y-3 text-xs">
                                        <p>We support you before, during, and after installation with remote
                                            assistance
                                            to
                                            answer your questions and help you work with confidence.</p>


                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mx-auto overflow-hidden">
                            <div class="flex justify-between items-center cursor-pointer " onclick="toggleAccordion()">
                                <div
                                    class="btn-secondary w-[55px] h-[55px] rounded-full flex items-center justify-center flex-shrink-0">
                                    <img src="assets/icons/estadisticas/2.svg" alt="" class="w-[70%]">
                                </div>
                                <div class="flex-1 mx-4">
                                    <h2 class="font-bold text-base">
                                        Global Coverage and immediate support
                                    </h2>
                                </div>
                                <div>
                                    <svg class="w-6 h-6 text-gray-500 arrow-transition" id="arrow" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="accordion-content" id="accordionContent">
                                <div class="pl-[75px] pr-[20px]">
                                    <div class="text-gray-900 space-y-3 text-xs">
                                        <p>We assist technicians and mechanics worldwide with fast support via
                                            WhatsApp
                                            and Telegram. Wherever you are, we’ve got you covered.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mx-auto overflow-hidden">
                            <div class="flex justify-between items-center cursor-pointer " onclick="toggleAccordion()">
                                <div
                                    class="btn-secondary w-[55px] h-[55px] rounded-full flex items-center justify-center flex-shrink-0">
                                    <img src="assets/icons/estadisticas/3.svg" alt="" class="w-[70%]">
                                </div>
                                <div class="flex-1 mx-4">
                                    <h2 class="font-bold text-base">
                                        Professional Diagnostic Software Installation
                                    </h2>
                                </div>
                                <div>
                                    <svg class="w-6 h-6 text-gray-500 arrow-transition" id="arrow" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="accordion-content" id="accordionContent">
                                <div class="pl-[75px] pr-[20px]">
                                    <div class="text-gray-900 space-y-3 text-xs">
                                        <p>We turn your laptop into a powerful tool for diagnosing trucks and heavy
                                            machinery. The software is delivered installed, activated, and ready to
                                            use.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <img src="assets/images/estadisticas_crop.png" alt=""
                        class="w-full h-auto object-contain mx-auto max-w-md lg:max-w-full">
                    <div class="xl:flex hidden flex-col gap-4 md:gap-6 lg:gap-8">
                        <div class="btn-primary p-3 md:p-4 rounded-xl flex flex-col md:flex-row items-center gap-4">
                            <div
                                class="btn-secondary w-[85px] h-[85px] md:size-[100px] lg:size-[115px] rounded-full flex items-center justify-center flex-shrink-0">
                                <img src="assets/icons/estadisticas/1.svg" alt="" class="w-[60%] md:w-[70%]">
                            </div>
                            <div class="flex-1 flex flex-col text-center md:text-left mt-2 md:mt-0">
                                <h2 class="font-bold text-lg md:text-xl">Specialized Technical Support</h2>
                                <p class="text-gray-900 text-sm md:text-base">
                                    We support you before, during, and after installation with remote assistance to
                                    answer your questions and help you work with confidence.
                                </p>
                            </div>
                        </div>
                        <div class="btn-primary p-3 md:p-4 rounded-xl flex flex-col md:flex-row items-center gap-4">
                            <div
                                class="btn-secondary w-[85px] h-[85px] md:size-[100px] lg:size-[115px] rounded-full flex items-center justify-center flex-shrink-0">
                                <img src="assets/icons/estadisticas/2.svg" alt="" class="w-[60%] md:w-[70%]">
                            </div>
                            <div class="flex-1 flex flex-col text-center md:text-left mt-2 md:mt-0">
                                <h2 class="font-bold text-lg md:text-xl">Global Coverage and Immediate Support</h2>
                                <p class="text-gray-900 text-sm md:text-base">
                                    We assist technicians and mechanics worldwide with fast support via WhatsApp and
                                    Telegram. Wherever you are, we’ve got you covered.
                                </p>
                            </div>
                        </div>
                        <div class="btn-primary p-3 md:p-4 rounded-xl flex flex-col md:flex-row items-center gap-4">
                            <div
                                class="btn-secondary w-[85px] h-[85px] md:size-[100px] lg:size-[115px] rounded-full flex items-center justify-center flex-shrink-0">
                                <img src="assets/icons/estadisticas/3.svg" alt="" class="w-[60%] md:w-[70%]">
                            </div>
                            <div class="flex-1 flex flex-col text-center md:text-left mt-2 md:mt-0">
                                <h2 class="font-bold text-lg md:text-xl">Professional Diagnostic Software
                                    Installation
                                </h2>
                                <p class="text-gray-900 text-sm md:text-base">
                                    We turn your laptop into a powerful tool for diagnosing trucks and heavy
                                    machinery.
                                    The software is delivered installed, activated, and ready to use.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="xl:max-w-6xl mx-auto mt-16 xl:mb-0 mb-10 lg:mt-20">
                    <div class="grid grid-cols-3 sm:grid-cols-3 divide-x divide-gray-800 overflow-hidden">
                        <div class="py-0 px-4 md:px-8 text-center">
                            <h2 class="text-xl md:text-6xl lg:text-8xl font-extrabold text-gray-800 xl:mb-2">
                                123K<span class="">+</span>
                            </h2>
                            <p class="text-xs lg:text-2xl text-gray-600 font-bold">
                                Installations
                            </p>
                        </div>

                        <div class="py-0 px-4 md:px-8 text-center">
                            <h2 class="text-xl md:text-6xl lg:text-8xl font-extrabold text-gray-800 xl:mb-2">
                                60<span class="">+</span>
                            </h2>
                            <p class="text-xs lg:text-2xl text-gray-600 font-bold">
                                Cities
                            </p>
                        </div>

                        <div class="py-0 px-4 md:px-8 text-center">
                            <h2 class="text-xl md:text-6xl lg:text-8xl font-extrabold text-gray-800 xl:mb-2">
                                10K<span class="">+</span>
                            </h2>
                            <p class="text-xs lg:text-2xl text-gray-600 font-bold">
                                Clients
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PROCESO DE COMPRA -->
        <section class="xl:py-20 py-5 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <h2 class="uppercase font-extrabold xl:text-3xl text-xl md:text-2xl mb-8 md:mb-14 text-start">
                PURCHASE PROCESS
            </h2>

            <!-- VISTA DESKTOP (md:block hidden) - Grid original -->
            <div class="hidden md:block">
                <!-- Grid de imágenes con números -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8 md:gap-3 w-full">
                    <div class="relative">
                        <div
                            class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                            <p>1</p>
                        </div>
                        <div class="image-anime">
                            <img src="assets/images/procesocompra/1.jpg" alt="" class="aspect-square object-cover" />
                        </div>
                    </div>
                    <div class="relative">
                        <div
                            class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                            <p>2</p>
                        </div>
                        <div class="image-anime">
                            <img src="assets/images/procesocompra/2.jpg" alt="" class="aspect-square object-cover" />
                        </div>
                    </div>
                    <div class="relative">
                        <div
                            class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                            <p>3</p>
                        </div>
                        <div class="image-anime">
                            <img src="assets/images/procesocompra/3.jpg" alt="" class="aspect-square object-cover" />
                        </div>
                    </div>
                    <div class="relative">
                        <div
                            class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                            <p>4</p>
                        </div>
                        <div class="image-anime">
                            <img src="assets/images/procesocompra/4.jpg" alt="" class="aspect-square object-cover" />
                        </div>
                    </div>
                    <div class="relative col-span-2 md:col-span-1 mx-auto w-full max-w-[200px] md:max-w-none">
                        <div
                            class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                            <p>5</p>
                        </div>
                        <div class="image-anime">
                            <img src="assets/images/procesocompra/5.jpg" alt="" class="aspect-square object-cover" />
                        </div>
                    </div>
                </div>

                <!-- Grid de iconos y descripciones -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8 md:gap-3 justify-items-center w-full mt-10">
                    <!-- Paso 1 -->
                    <div class="flex flex-col items-center gap-3 md:gap-7 w-full max-w-[150px] md:max-w-[200px]">
                        <div
                            class="btn-secondary w-[100px] h-[100px] md:w-[185px] md:h-[185px] rounded-full flex items-center">
                            <img src="assets/icons/procesocompra/1.svg"
                                class="w-[60px] h-[60px] md:w-[125px] md:h-[125px] mx-auto" alt="" />
                        </div>
                        <p class="text-center text-sm md:text-xl font-bold text-balance">
                            Choose your software or package
                        </p>
                    </div>

                    <!-- Paso 2 -->
                    <div class="flex flex-col items-center gap-3 md:gap-7 w-full max-w-[150px] md:max-w-[200px]">
                        <div
                            class="btn-secondary w-[100px] h-[100px] md:w-[185px] md:h-[185px] rounded-full flex items-center">
                            <img src="assets/icons/procesocompra/2.svg"
                                class="w-[60px] h-[60px] md:w-[125px] md:h-[125px] mx-auto" alt="" />
                        </div>
                        <p class="text-center text-sm md:text-xl font-bold text-balance">
                            Add to cart and complete your purchase
                        </p>
                    </div>

                    <!-- Paso 3 -->
                    <div class="flex flex-col items-center gap-3 md:gap-7 w-full max-w-[150px] md:max-w-[200px]">
                        <div
                            class="btn-secondary w-[100px] h-[100px] md:w-[185px] md:h-[185px] rounded-full flex items-center">
                            <img src="assets/icons/procesocompra/3.svg"
                                class="w-[60px] h-[60px] md:w-[125px] md:h-[125px] mx-auto" alt="" />
                        </div>
                        <p class="text-center text-sm md:text-xl font-bold text-balance">
                            Add to cart and complete your purchase
                        </p>
                    </div>

                    <!-- Paso 4 -->
                    <div class="flex flex-col items-center gap-3 md:gap-7 w-full max-w-[150px] md:max-w-[200px]">
                        <div
                            class="btn-secondary w-[100px] h-[100px] md:w-[185px] md:h-[185px] rounded-full flex items-center">
                            <img src="assets/icons/procesocompra/4.svg"
                                class="w-[60px] h-[60px] md:w-[125px] md:h-[125px] mx-auto" alt="" />
                        </div>
                        <p class="text-center text-sm md:text-xl font-bold text-balance">
                            Make the payment
                        </p>
                    </div>

                    <!-- Paso 5 -->
                    <div
                        class="flex flex-col items-center gap-3 md:gap-7 w-full max-w-[150px] md:max-w-[200px] col-span-2 md:col-span-1 mx-auto">
                        <div
                            class="btn-secondary w-[100px] h-[100px] md:w-[185px] md:h-[185px] rounded-full flex items-center">
                            <img src="assets/icons/procesocompra/5.svg"
                                class="w-[60px] h-[60px] md:w-[125px] md:h-[125px] mx-auto" alt="" />
                        </div>
                        <p class="text-center text-sm md:text-xl font-bold text-balance">
                            Remote Installation
                        </p>
                    </div>
                </div>
            </div>

            <!-- VISTA MOBILE (block md:hidden) - Carrusel -->
            <div class="block md:hidden !overflow-visible">
                <div id="purchase-process-carousel" class="splide">
                    <div class="splide__track h-min pt-5">
                        <ul class="splide__list ">
                            <!-- Slide 1 -->
                            <li class="splide__slide !overflow-visible">
                                <div class="flex flex-col items-center gap-6 !overflow-visible">
                                    <!-- Imagen con número flotante -->
                                    <div class="relative w-full max-w-[180px]">
                                        <div
                                            class="absolute z-50 btn-secondary -top-[15px] left-[10px] px-3 py-1 font-extrabold text-xl aspect-square flex items-center">
                                            <p>1</p>
                                        </div>
                                        <div class="image-anime">
                                            <img src="assets/images/procesocompra/1.jpg" alt=""
                                                class="aspect-square object-cover w-full" />
                                        </div>
                                    </div>
                                    <!-- Círculo con icono y texto -->
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="btn-secondary w-[100px] h-[100px] rounded-full flex items-center">
                                            <img src="assets/icons/procesocompra/1.svg"
                                                class="w-[60px] h-[60px] mx-auto" alt="" />
                                        </div>
                                        <p class="text-center text-sm font-bold text-balance max-w-[120px]">
                                            Choose your software or package
                                        </p>
                                    </div>
                                </div>
                            </li>

                            <!-- Slide 2 -->
                            <li class="splide__slide">
                                <div class="flex flex-col items-center gap-6">
                                    <!-- Imagen con número flotante -->
                                    <div class="relative w-full max-w-[180px]">
                                        <div
                                            class="absolute z-50 btn-secondary -top-[15px] left-[10px] px-3 py-1 font-extrabold text-xl aspect-square flex items-center">
                                            <p>2</p>
                                        </div>
                                        <div class="image-anime">
                                            <img src="assets/images/procesocompra/2.jpg" alt=""
                                                class="aspect-square object-cover w-full" />
                                        </div>
                                    </div>
                                    <!-- Círculo con icono y texto -->
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="btn-secondary w-[100px] h-[100px] rounded-full flex items-center">
                                            <img src="assets/icons/procesocompra/2.svg"
                                                class="w-[60px] h-[60px] mx-auto" alt="" />
                                        </div>
                                        <p class="text-center text-sm font-bold text-balance max-w-[120px]">
                                            Add to cart and complete your purchase
                                        </p>
                                    </div>
                                </div>
                            </li>

                            <!-- Slide 3 -->
                            <li class="splide__slide">
                                <div class="flex flex-col items-center gap-6">
                                    <!-- Imagen con número flotante -->
                                    <div class="relative w-full max-w-[180px]">
                                        <div
                                            class="absolute z-50 btn-secondary -top-[15px] left-[10px] px-3 py-1 font-extrabold text-xl aspect-square flex items-center">
                                            <p>3</p>
                                        </div>
                                        <div class="image-anime">
                                            <img src="assets/images/procesocompra/3.jpg" alt=""
                                                class="aspect-square object-cover w-full" />
                                        </div>
                                    </div>
                                    <!-- Círculo con icono y texto -->
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="btn-secondary w-[100px] h-[100px] rounded-full flex items-center">
                                            <img src="assets/icons/procesocompra/3.svg"
                                                class="w-[60px] h-[60px] mx-auto" alt="" />
                                        </div>
                                        <p class="text-center text-sm font-bold text-balance max-w-[120px]">
                                            Add to cart and complete your purchase
                                        </p>
                                    </div>
                                </div>
                            </li>

                            <!-- Slide 4 -->
                            <li class="splide__slide">
                                <div class="flex flex-col items-center gap-6">
                                    <!-- Imagen con número flotante -->
                                    <div class="relative w-full max-w-[180px]">
                                        <div
                                            class="absolute z-50 btn-secondary -top-[15px] left-[10px] px-3 py-1 font-extrabold text-xl aspect-square flex items-center">
                                            <p>4</p>
                                        </div>
                                        <div class="image-anime">
                                            <img src="assets/images/procesocompra/4.jpg" alt=""
                                                class="aspect-square object-cover w-full" />
                                        </div>
                                    </div>
                                    <!-- Círculo con icono y texto -->
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="btn-secondary w-[100px] h-[100px] rounded-full flex items-center">
                                            <img src="assets/icons/procesocompra/4.svg"
                                                class="w-[60px] h-[60px] mx-auto" alt="" />
                                        </div>
                                        <p class="text-center text-sm font-bold text-balance max-w-[120px]">
                                            Make the payment
                                        </p>
                                    </div>
                                </div>
                            </li>

                            <!-- Slide 5 -->
                            <li class="splide__slide">
                                <div class="flex flex-col items-center gap-6">
                                    <!-- Imagen con número flotante -->
                                    <div class="relative w-full max-w-[180px]">
                                        <div
                                            class="absolute z-50 btn-secondary -top-[15px] left-[10px] px-3 py-1 font-extrabold text-xl aspect-square flex items-center">
                                            <p>5</p>
                                        </div>
                                        <div class="image-anime">
                                            <img src="assets/images/procesocompra/5.jpg" alt=""
                                                class="aspect-square object-cover w-full" />
                                        </div>
                                    </div>
                                    <!-- Círculo con icono y texto -->
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="btn-secondary w-[100px] h-[100px] rounded-full flex items-center">
                                            <img src="assets/icons/procesocompra/5.svg"
                                                class="w-[60px] h-[60px] mx-auto" alt="" />
                                        </div>
                                        <p class="text-center text-sm font-bold text-balance max-w-[120px]">
                                            Remote Installation
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- FORMAS DE PAGO SLIDER -->
        <section class="xl:py-0 py-0 mx-auto my-10 xl:my-0  overflow-hidden">
            <section id="formas-pago-carousel" class="splide" aria-label="Formas de Pago">
                <div class="splide__track h-min">
                    <ul class="splide__list">
                        <!-- Slide 1 -->
                        <li class="splide__slide">
                            <img src="/assets/images/formasdepago/1.webp" alt="Formas de Pago - Opción 1"
                                class="w-full h-auto object-contain rounded-lg" />
                        </li>
                        <!-- Slide 2 -->
                        <li class="splide__slide">
                            <img src="/assets/images/formasdepago/2.webp" alt="Formas de Pago - Opción 2"
                                class="w-full h-auto object-contain rounded-lg" />
                        </li>
                        <!-- Slide 3 -->
                        <li class="splide__slide">
                            <img src="/assets/images/formasdepago/3.webp" alt="Formas de Pago - Opción 3"
                                class="w-full h-auto object-contain rounded-lg" />
                        </li>
                    </ul>
                </div>
            </section>
        </section>

        <!-- TESTIMONIALS -->
        <section class="xl:py-20 py-6 px-4 mx-auto max-w-screen-2xl ">
            <div class="text-start xl:mb-10 mb-1">
                <h2 class="uppercase font-extrabold xl:text-3xl text-base mb-4">
                    What our customers say about us:
                </h2>
            </div>

            <section id="testimonials-carousel" class="splide" aria-label="Testimonios de clientes">
                <div class="splide__track pb-6 pt-3">
                    <ul class="splide__list xl:h-[51vh] h-[30vh]">
                        <li class="splide__slide">
                            <div
                                class="bg-white shadow border border-gray-100 p-6 md:p-8 mx-2 h-full flex flex-col justify-between xl:min-h-[320px] h-auto">
                                <div class="flex-1">
                                    <div class="flex items-center gap-1 mb-4">
                                        <svg class="xl:w-5 xl:h-5 w-3 h-3 text-yellow-400 fill-current"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="xl:w-5 xl:h-5 w-3 h-3 text-yellow-400 fill-current"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="xl:w-5 xl:h-5 w-3 h-3 text-yellow-400 fill-current"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="xl:w-5 xl:h-5 w-3 h-3 text-yellow-400 fill-current"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                        <svg class="xl:w-5 xl:h-5 w-3 h-3 text-yellow-400 fill-current"
                                            viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    </div>

                                    <div class="flex items-center gap-4 mb-4 mt-2">
                                        <img src="/assets/images/testimonial.png" class="xl:size-[53px] size-[45px]"
                                            alt="">
                                        <p class="font-bold xl:text-xl text-base">
                                            Jhon Doe
                                        </p>
                                    </div>

                                    <blockquote class="text-gray-700 xl:text-lg text-xs leading-relaxed xl:mb-6 mb-2">
                                        "El software de diagnóstico VOLVO PTT llegó perfectamente instalado y
                                        configurado. La instalación remota fue impecable y el soporte técnico
                                        excepcional. Ahora puedo diagnosticar cualquier problema en camiones Volvo
                                        de
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
                <h2 class="uppercase font-extrabold xl:text-3xl text-base mb-4">
                    Frequently Asked Questions:
                </h2>
            </div>

            <div class="w-full space-y-4" id="faq-container">
                <!-- FAQ Item 1 -->
                <div class="faq-item border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <button
                        class="faq-header w-full xl:px-6 px-3 xl:py-5 py-3 text-left flex items-center justify-between bg-white hover:bg-gray-50 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-opacity-50">
                        <h3 class="xl:text-lg text-sm xl:font-bold text-semibold text-gray-900 xl:pr-4">
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
                            <p class="text-gray-700 xl:text-base text-xs leading-relaxed">
                                Cada software incluye una lista detallada de interfaces compatibles en su
                                descripción.
                                Además, nuestro equipo técnico puede asesorarte sobre la compatibilidad específica
                                de tu
                                equipo. Contamos con software para las principales marcas como Launch, Autel, OTC y
                                muchas más.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="faq-item border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <button
                        class="faq-header w-full xl:px-6 px-3 xl:py-5 py-3 text-left flex items-center justify-between bg-white hover:bg-gray-50 transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-opacity-50">
                        <h3 class="xl:text-lg text-sm xl:font-bold text-semibold text-gray-900 xl:pr-4">
                            ¿Qué métodos de pago aceptan?
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
                            <p class="text-gray-700 xl:text-base text-xs leading-relaxed">
                                Aceptamos PayPal, Western Union y MoneyGram. Al realizar tu compra, puedes elegir el
                                método que prefieras y te enviaremos las instrucciones para completar el pago.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <!-- MODALS -->
    <?php require_once __DIR__ . '/includes/modal_login_registro.php'; ?>

    <!-- DRAWER -->
    <?php require_once __DIR__ . '/includes/carrito_home.php'; ?>

    <div id="alertaFavorito"
        class="hidden fixed top-5 right-5 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50 text-sm"
        role="alert">
        <strong class="font-bold">¡Atención!</strong>
        <span class="block" id="alertaTexto"></span>
    </div>

    <div id="product-details-modal" tabindex="-1" aria-hidden="true"
        class="hidden fixed inset-0 z-50 w-full h-full
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
                            <svg class="xl:w-4 w-2.5 xl:h-4 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 14 14">
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
                                    <img id="mainImage"
                                        src="https://placehold.co/600x600/png"
                                        alt="Imagen principal"
                                        class="h-full w-full object-cover transition-opacity duration-300"
                                        loading="eager" decoding="async" draggable="false" />
                                </div>
                            </div>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 w-10 bg-gradient-to-r from-gray-50 to-transparent"></div>
                                <div class="pointer-events-none absolute inset-y-0 right-0 w-10 bg-gradient-to-l from-gray-50 to-transparent"></div>

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
                                <p id="modal-product-price" class="xl:text-3xl text-lg text-nowrap font-bold">USD 0.00</p>

                                <div class="relative mt-2 flex max-w-32 items-center justify-end">
                                    <button type="button" id="decrement-button"
                                        class="xl:h-10 h-8 rounded-s-lg border border-gray-300 bg-gray-100 xl:p-3 p-2 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                        <svg class="h-3 w-3 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                        </svg>
                                    </button>
                                    <input type="text" id="quantity-input-1" data-input-counter data-input-counter-min="1" data-input-counter-max="50"
                                        class="block xl:h-10 h-8 w-full border-x-0 border-gray-300 bg-gray-50 py-2.5 text-center text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
                                        value="1" />
                                    <button type="button" id="increment-button"
                                        class="xl:h-10 h-8 rounded-e-lg border border-gray-300 bg-gray-100 xl:p-3 p-2 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                        <svg class="h-3 w-3 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <p id="modal-product-description" class="mt-4 text-gray-700 text-sm xl:text-base leading-relaxed">
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

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="<?php echo $url; ?>/scripts/main.js"></script>
    <script type="module" src="<?php echo $url; ?>/scripts/carrousels.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- Debug Script para Chatbot -->
    <script>
        console.log('🔍 Debug Script Loaded');

        document.addEventListener("DOMContentLoaded", function() {
            console.log('🔍 DOM Content Loaded');

            // Verificar elementos
            const chatTrigger = document.getElementById("chatBotTrigger");
            const chatContainer = document.getElementById("chatbotContainer");
            const closeButton = document.getElementById("closeChatbot");

            console.log('🔍 Chat Trigger:', chatTrigger);
            console.log('🔍 Chat Container:', chatContainer);
            console.log('🔍 Close Button:', closeButton);

            if (chatTrigger && chatContainer) {
                console.log('✅ All elements found, setting up chatbot');

                // Función para abrir chatbot
                function openChatbot() {
                    console.log('🚀 Opening chatbot');
                    chatContainer.classList.remove("hidden");
                    chatContainer.style.display = "flex";
                }

                // Función para cerrar chatbot
                function closeChatbot() {
                    console.log('🚀 Closing chatbot');
                    chatContainer.classList.add("hidden");
                    chatContainer.style.display = "none";
                }

                // Event listener para abrir
                chatTrigger.addEventListener("click", function(e) {
                    console.log('🖱️ Chat trigger clicked!');
                    e.preventDefault();
                    e.stopPropagation();
                    openChatbot();
                });

                // Event listener para cerrar
                if (closeButton) {
                    closeButton.addEventListener("click", function(e) {
                        console.log('🖱️ Close button clicked!');
                        e.preventDefault();
                        e.stopPropagation();
                        closeChatbot();
                    });
                }

                // Agregar hover effect
                chatTrigger.addEventListener("mouseenter", function() {
                    chatTrigger.style.transform = "scale(1.05)";
                    chatTrigger.style.transition = "transform 0.2s ease";
                });

                chatTrigger.addEventListener("mouseleave", function() {
                    chatTrigger.style.transform = "scale(1)";
                });

                console.log('✅ Chatbot setup complete');

            } else {
                console.error('❌ Required elements not found');
                if (!chatTrigger) console.error('❌ Chat trigger not found');
                if (!chatContainer) console.error('❌ Chat container not found');
            }
        });
    </script>
    <!--Start of Tawk.to Script-->
    <!-- <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function () {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/689b7043b5ccf2192652acfd/1j2fit24i';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script> -->
    <script src="//code.tidio.co/o6wmaxvuxzaa1ojsayha7m3vay3qlr2g.js" async></script>
    <!--End of Tawk.to Script-->
    <style>
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .accordion-content.active {
            max-height: 300px;
        }

        .arrow-transition {
            transition: transform 0.3s ease;
        }

        .arrow-transition.rotated {
            transform: rotate(180deg);
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Buscar todos los acordeones
            const accordionContainers = document.querySelectorAll('.grid.grid-cols-1.xl\\:hidden .mx-auto');

            // Aplicar el CSS necesario
            const style = document.createElement('style');
            style.textContent = `
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }
        
        .accordion-content.active {
            max-height: 200px;
        }
        
        .arrow-transition {
            transition: transform 0.3s ease;
        }
        
        .arrow-transition.rotated {
            transform: rotate(180deg);
        }
    `;
            document.head.appendChild(style);

            // Configurar cada acordeón
            accordionContainers.forEach((container, index) => {
                const header = container.querySelector('.flex.justify-between.items-center');
                const content = container.querySelector('.accordion-content');
                const arrow = container.querySelector('svg');

                // Remover onclick del HTML
                if (header) {
                    header.removeAttribute('onclick');
                }

                // Abrir el primero por defecto
                if (index === 0) {
                    content.classList.add('active');
                    arrow.classList.add('rotated');
                }

                // Agregar event listener
                if (header) {
                    header.addEventListener('click', function() {
                        // Cerrar todos los demás acordeones
                        accordionContainers.forEach((otherContainer, otherIndex) => {
                            if (otherIndex !== index) {
                                const otherContent = otherContainer.querySelector('.accordion-content');
                                const otherArrow = otherContainer.querySelector('svg');
                                otherContent.classList.remove('active');
                                otherArrow.classList.remove('rotated');
                            }
                        });

                        // Toggle el acordeón actual
                        content.classList.toggle('active');
                        arrow.classList.toggle('rotated');
                    });
                }
            });
        });

        // Función global para compatibilidad (en caso de que se llame desde el HTML)
        function toggleAccordion() {
            // Esta función ya no se necesita, pero la dejamos por compatibilidad
            console.log('Using new accordion system');
        }
    </script>
    
    <div id="pop-up-modal" tabindex="-1" aria-hidden="true"
        class="hidden fixed top-0 left-0 right-0 z-50 flex justify-center items-center w-full h-full bg-black bg-opacity-40">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow ">
                <div class="absolute top-2 right-2">
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        id="close-pop-up-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>
                <div class="xl:p-10 p-8 space-y-4 btn-primary">

                    <h2 class="xl:text-3xl text-lg font-extrabold mx-auto text-center">
                        Get <span class="text-[#f7a615]"> $50 off</span> your
                        first installation
                    </h2>
                    <p class="text-base text-center">
                        Sign up and get $50 off your first installation, plus updates, the latest releases, and news.
                    </p>
                    <form action="">
                        <div>
                            <label for="email-pop-up" class="block mb-2 text-sm font-bold uppercase text-gray-900 ">Sign
                                up
                                with
                                your email
                                <span class="text-red-500">*</span></label>
                            <input type="email" id="email-pop-up"
                                class=" border border-gray-400 text-gray-900 text-sm rounded focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                                placeholder="you@example.com" required />
                        </div>
                        <div class="mt-14">
                            <p class="text-gray-400 text-center text-xs">
                                Terms and conditions apply
                            </p>
                            <button class="btn-secondary w-full block rounded-lg py-3 mt-1" type="submit">
                                SIGN UP
                            </button>
                            <a href="#" target="_blank"
                                class="mt-3 underline underline-offset-4 text-gray-800 text-center text-xs block">
                                Terms and conditions
                            </a>
                        </div>
                    </form>

                    <button id="no-show-again" class="mt-3 text-gray-500 hover:text-gray-800 text-xs block mx-auto">
                        No volver a mostrar
                    </button>
                </div>
                <!-- <div class="flex items-center p-4 border-t border-gray-200 rounded-b ">
                <button id="accept-pop-up-modal" type="button"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">Aceptar</button>
                </div> -->
            </div>
        </div>
    </div>

    <script>
        // Mostrar el modal solo si no está la bandera en localStorage
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('pop-up-modal');
            var noShow = localStorage.getItem('noShowAgain');
            if (!noShow && modal) {
                modal.classList.remove('hidden');
            } else if (modal) {
                modal.classList.add('hidden');
            }
            // Botón cerrar (X)
            document.getElementById('close-pop-up-modal')?.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
            // Botón "No volver a mostrar"
            document.getElementById('no-show-again')?.addEventListener('click', function() {
                modal.classList.add('hidden');
                localStorage.setItem('noShowAgain', 'true');
            });
        });
    </script>
    <script>
        const mainImage = document.getElementById('mainImage');
        const thumbsContainer = document.getElementById('thumbs');
        const thumbnails = Array.from(document.querySelectorAll('.thumb'));
        const prevBtn = document.getElementById('prev');
        const nextBtn = document.getElementById('next');
        let current = 0;

        function setActive(index, {
            focusThumb = false
        } = {}) {
            if (index < 0 || index >= thumbnails.length) return;

            // Transición de la imagen principal
            mainImage.style.opacity = '0';
            const nextSrc = thumbnails[index].src;
            const nextAlt = thumbnails[index].alt.replace('Miniatura', 'Imagen');
            setTimeout(() => {
                mainImage.src = nextSrc;
                mainImage.alt = nextAlt;
                mainImage.style.opacity = '1';
            }, 150);

            // Estilo activo en miniaturas
            thumbnails.forEach((img, i) => {
                img.classList.toggle('ring-2', i === index);
                img.classList.toggle('ring-orange-500', i === index);
                img.classList.toggle('ring-offset-2', i === index);
                img.setAttribute('aria-selected', i === index ? 'true' : 'false');
            });

            // Centrar miniatura activa
            thumbnails[index].scrollIntoView({
                behavior: 'smooth',
                inline: 'center',
                block: 'nearest'
            });
            if (focusThumb) thumbnails[index].focus({
                preventScroll: true
            });

            current = index;
        }

        // Estado de flechas según scroll disponible
        function updateArrowState() {
            const {
                scrollLeft,
                clientWidth,
                scrollWidth
            } = thumbsContainer;
            prevBtn.disabled = scrollLeft <= 0;
            nextBtn.disabled = scrollLeft + clientWidth >= scrollWidth - 1;
        }

        // Click en miniaturas
        thumbnails.forEach((img, i) => {
            img.addEventListener('click', () => setActive(i));
            img.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    setActive(i);
                }
            });
        });

        // Botones de desplazamiento del carrusel de miniaturas
        prevBtn.addEventListener('click', () => {
            thumbsContainer.scrollBy({
                left: -Math.max(thumbsContainer.clientWidth * 0.6, 120),
                behavior: 'smooth'
            });
        });
        nextBtn.addEventListener('click', () => {
            thumbsContainer.scrollBy({
                left: Math.max(thumbsContainer.clientWidth * 0.6, 120),
                behavior: 'smooth'
            });
        });

        // Actualizar estado de flechas al hacer scroll o redimensionar
        thumbsContainer.addEventListener('scroll', updateArrowState);
        window.addEventListener('resize', updateArrowState);

        // Navegación por teclado para cambiar imagen principal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight') setActive(Math.min(current + 1, thumbnails.length - 1));
            if (e.key === 'ArrowLeft') setActive(Math.max(current - 1, 0));
            if (e.key === 'Home') setActive(0, {
                focusThumb: true
            });
            if (e.key === 'End') setActive(thumbnails.length - 1, {
                focusThumb: true
            });
        });

        // Inicializar
        setActive(0);
        updateArrowState();
    </script>

    <style>
        /* Ocultar scrollbar */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</body>


</html>