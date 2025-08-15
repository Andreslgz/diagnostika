<?php
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
                    // Traer todas las imágenes activas ordenadas por ID descendente
                    $sliders = $database->select("slider", "sl_img", [
                        "sl_est" => "activo",
                        "ORDER" => ["sl_id" => "DESC"]
                    ]);

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
                    <?php if ($sliders && count($sliders) > 0): ?>
                        <?php foreach ($sliders as $img):
                            $imagen = htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>
                            <li class="splide__slide">
                                <img src="uploads/slider/<?= $imagen ?>" alt="Slide Mobile"
                                    class="w-full h-full object-cover" />
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="splide__slide">
                            <img src="uploads/slider/img-no-disponible.jpg" alt="Sin imagen"
                                class="w-full h-full object-cover" />
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </section>


        <!-- HERO MOBILE -->

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
                    <a href="<?php echo rtrim($baseDir, '/'); ?>/tienda"
                        class="btn-primary rounded px-4 sm:px-6 lg:px-8 py-2 uppercase font-bold text-sm sm:text-base xl:text-lg flex items-center gap-2 cursor-pointer hover:underline underline-offset-4 self-start sm:self-auto">
                        Ver Todo
                        <img src="assets/icons/svg/tabler--chevron-right.svg" alt="" />
                    </a>
                </div>
                <div class="mx-auto max-w-screen-xl">
                    <!-- Products Carousel -->
                    <section id="products-carousel" class="splide" aria-label="Featured Products">
                        <div class="splide__track h-[61vh]">
                            <ul class="splide__list">
                                <?php foreach ($productos as $prod): ?>
                                    <li
                                        class="splide__slide !pb-4 sm:!pb-6 lg:!pb-10 !pr-2 sm:!pr-4 lg:!pr-6 !pl-2 sm:!pl-4 lg:!pl-6">
                                        <div
                                            class="border border-gray-100 border-solid shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg p-3 sm:p-4 lg:p-6 flex flex-col gap-3 sm:gap-3 h-full">
                                            <div class="flex justify-end -mb-1">
                                                <button type="button" class="favorito-btn"
                                                    data-id="<?php echo $prod['id_producto']; ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        fill="<?php echo in_array($prod['id_producto'], $favoritos_usuario) ? 'currentColor' : 'none'; ?>"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-7 h-7 sm:w-6 sm:h-6 transition-all duration-200 <?php echo in_array($prod['id_producto'], $favoritos_usuario) ? 'text-red-600' : 'text-gray-600'; ?>">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6.75 3.75h10.5a.75.75 0 01.75.75v15.375a.375.375 0 01-.6.3L12 16.5l-5.4 3.675a.375.375 0 01-.6-.3V4.5a.75.75 0 01.75-.75z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <img src="<?php echo !empty($prod['imagen']) ? 'uploads/' . $prod['imagen'] : 'https://placehold.co/600x400/png'; ?>"
                                                alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                                                class="w-full h-40 sm:h-40 lg:h-48 object-fit rounded-md" />
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
                <section class="mx-auto max-w-screen-xl  grid xl:grid-cols-6 grid-cols-2 xl:gap-6 gap-8 mt-12">
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
                <!-- Banner 1 -->
                <div class="relative">
                    <div class="flex flex-col md:flex-row">
                        <!-- Contenido -->
                        <div class="btn-secondary w-full md:w-[30%] h-auto xl:rounded-l-lg rounded-t-lg p-4 md:p-4">
                            <img src="assets/icons/banner/banner1.svg" alt=""
                                class="size-[80px] md:size-[125px] mx-auto md:mx-0" />
                            <p class="text-xl md:text-2xl font-extrabold text-banner-1 text-center md:text-left mt-2">
                                En la carretera
                            </p>
                            <p class="text-white text-base md:text-lg font-semibold text-center md:text-left">Paquete
                            </p>
                            <button
                                class="mt-3 md:mt-5 btn-primary rounded-lg px-4 md:px-8 py-2 uppercase font-bold text-sm md:text-base flex items-center gap-2 cursor-pointer hover:underline underline-offset-4 mx-auto md:mx-0">
                                Ver Todo
                                <img src="assets/icons/svg/tabler--chevron-right.svg" alt="" />
                            </button>
                        </div>
                        <!-- Imagen -->
                        <div class="w-full md:w-[70%] h-[200px] md:h-auto image-anime rounded-r-lg">
                            <img src="assets/images/banner1.jpg" alt=""
                                class="w-full h-full object-cover rounded-b-xl md:rounded-b-none md:rounded-r-xl" />
                        </div>
                    </div>
                    <!-- Badge -->
                    <div class="absolute top-2 right-2 md:top-0 md:right-0 flex scale-75 md:scale-100 origin-top-right">
                        <div class="btn-secondary px-3 md:px-4 py-1 font-extrabold text-2xl md:text-4xl rounded-l-lg">
                            +
                        </div>
                        <div
                            class="btn-primary px-3 md:px-5 py-1 flex items-center italic font-bold text-xs md:text-base ">
                            100 Programas incluidos
                        </div>
                    </div>
                </div>

                <!-- Banner 2 -->
                <div class="relative">
                    <div class="flex flex-col md:flex-row">
                        <!-- Contenido -->
                        <div class="bg-banner2 w-full md:w-[30%] h-auto xl:rounded-l-lg rounded-t-lg p-4 md:p-4">
                            <img src="assets/icons/banner/banner1.svg" alt=""
                                class="size-[80px] md:size-[125px] mx-auto md:mx-0" />
                            <p class="text-xl md:text-2xl font-extrabold text-banner-1 text-center md:text-left mt-2">
                                En la carretera
                            </p>
                            <p class="text-white text-base md:text-lg font-semibold text-center md:text-left">Paquete
                            </p>
                            <button
                                class="mt-3 md:mt-5 btn-primary rounded-lg px-4 md:px-8 py-2 uppercase font-bold text-sm md:text-base flex items-center gap-2 cursor-pointer hover:underline underline-offset-4 mx-auto md:mx-0">
                                Ver Todo
                                <img src="assets/icons/svg/tabler--chevron-right.svg" alt="" />
                            </button>
                        </div>
                        <!-- Imagen -->
                        <div class="w-full md:w-[70%] h-[200px] md:h-auto image-anime">
                            <img src="assets/images/banner2.jpg" alt=""
                                class="w-full h-full object-cover rounded-b-xl md:rounded-b-none md:rounded-r-xl" />
                        </div>
                    </div>
                    <!-- Badge -->
                    <div class="absolute top-2 right-2 md:top-0 md:right-0 flex scale-75 md:scale-100 origin-top-right">
                        <div class="btn-secondary px-3 md:px-4 py-1 font-extrabold text-2xl md:text-4xl">
                            +
                        </div>
                        <div
                            class="btn-primary px-3 md:px-5 py-1 flex items-center italic font-bold text-xs md:text-base">
                            100 Programas incluidos
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
                    <img src="assets/images/estadisticas_crop.png" alt=""
                        class="w-full h-auto object-contain mx-auto max-w-md lg:max-w-full">
                    <div class="flex flex-col gap-4 md:gap-6 lg:gap-8">
                        <div class="btn-primary p-3 md:p-4 rounded-xl flex flex-col md:flex-row items-center gap-4">
                            <div
                                class="btn-secondary w-[85px] h-[85px] md:size-[100px] lg:size-[115px] rounded-full flex items-center justify-center flex-shrink-0">
                                <img src="assets/icons/estadisticas/1.svg" alt="" class="w-[60%] md:w-[70%]">
                            </div>
                            <div class="flex-1 flex flex-col text-center md:text-left mt-2 md:mt-0">
                                <h2 class="font-bold text-lg md:text-xl">Soporte técnico especializado</h2>
                                <p class="text-gray-900 text-sm md:text-base">
                                    Le apoyamos antes, durante y después de la instalación con asistencia remota
                                    para
                                    responder a sus preguntas y ayudarle a trabajar con confianza.
                                </p>
                            </div>
                        </div>
                        <div class="btn-primary p-3 md:p-4 rounded-xl flex flex-col md:flex-row items-center gap-4">
                            <div
                                class="btn-secondary w-[85px] h-[85px] md:size-[100px] lg:size-[115px] rounded-full flex items-center justify-center flex-shrink-0">
                                <img src="assets/icons/estadisticas/2.svg" alt="" class="w-[60%] md:w-[70%]">
                            </div>
                            <div class="flex-1 flex flex-col text-center md:text-left mt-2 md:mt-0">
                                <h2 class="font-bold text-lg md:text-xl">Cobertura global y soporte inmediato</h2>
                                <p class="text-gray-900 text-sm md:text-base">
                                    Asistimos a técnicos y mecánicos de todo el mundo con soporte rápido por
                                    WhatsApp y
                                    Telegram. Estés donde estés, te tenemos cubierto.
                                </p>
                            </div>
                        </div>
                        <div class="btn-primary p-3 md:p-4 rounded-xl flex flex-col md:flex-row items-center gap-4">
                            <div
                                class="btn-secondary w-[85px] h-[85px] md:size-[100px] lg:size-[115px] rounded-full flex items-center justify-center flex-shrink-0">
                                <img src="assets/icons/estadisticas/3.svg" alt="" class="w-[60%] md:w-[70%]">
                            </div>
                            <div class="flex-1 flex flex-col text-center md:text-left mt-2 md:mt-0">
                                <h2 class="font-bold text-lg md:text-xl">Instalación de software de diagnóstico
                                    profesional</h2>
                                <p class="text-gray-900 text-sm md:text-base">
                                    Convertimos su portátil en una potente herramienta para el diagnóstico de
                                    camiones y
                                    maquinaria pesada. El software se entrega instalado, activado y listo para usar.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="max-w-6xl mx-auto mt-10 md:mt-20">
                    <div
                        class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-800 overflow-hidden">
                        <div class="py-5 px-4 md:px-8 text-center">
                            <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-extrabold text-gray-800 mb-2">
                                123K<span class="text-blue-600">+</span>
                            </h2>
                            <p class="text-lg sm:text-xl lg:text-2xl text-gray-600 font-bold">
                                Installations
                            </p>
                        </div>

                        <div class="py-5 px-4 md:px-8 text-center">
                            <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-extrabold text-gray-800 mb-2">
                                60<span class="text-blue-600">+</span>
                            </h2>
                            <p class="text-lg sm:text-xl lg:text-2xl text-gray-600 font-bold">
                                Cities
                            </p>
                        </div>

                        <div class="py-5 px-4 md:px-8 text-center">
                            <h2 class="text-4xl sm:text-5xl md:text-6xl lg:text-8xl font-extrabold text-gray-800 mb-2">
                                10K<span class="text-blue-600">+</span>
                            </h2>
                            <p class="text-lg sm:text-xl lg:text-2xl text-gray-600 font-bold">
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
                PROCESO DE COMPRA
            </h2>

            <!-- Grid de imágenes con números -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8 md:gap-3 w-full">
                <div class="relative">
                    <div
                        class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                        <p>1</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/1.jpg" alt="" class="aspect-square object-cover " />
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                        <p>2</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/2.jpg" alt="" class="aspect-square object-cover " />
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                        <p>3</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/3.jpg" alt="" class="aspect-square object-cover " />
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                        <p>4</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/4.jpg" alt="" class="aspect-square object-cover " />
                    </div>
                </div>
                <div class="relative col-span-2 md:col-span-1 mx-auto w-full max-w-[200px] md:max-w-none">
                    <div
                        class="absolute z-50 btn-secondary -top-[15px] md:-top-[25px] left-[10px] md:left-[15px] px-3 md:px-4 py-1 font-extrabold text-xl md:text-3xl aspect-square flex items-center">
                        <p>5</p>
                    </div>
                    <div class="image-anime">
                        <img src="assets/images/procesocompra/5.jpg" alt="" class="aspect-square object-cover " />
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
                        Elige tu software o paquete
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
                        Añade al carrito y completa tu compra
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
                        Envía tu número de pedido por WhatsApp
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
                        Realizar el pago
                    </p>
                </div>

                <!-- Paso 5 - Se centra en mobile -->
                <div
                    class="flex flex-col items-center gap-3 md:gap-7 w-full max-w-[150px] md:max-w-[200px] col-span-2 md:col-span-1 mx-auto">
                    <div
                        class="btn-secondary w-[100px] h-[100px] md:w-[185px] md:h-[185px] rounded-full flex items-center">
                        <img src="assets/icons/procesocompra/5.svg"
                            class="w-[60px] h-[60px] md:w-[125px] md:h-[125px] mx-auto" alt="" />
                    </div>
                    <p class="text-center text-sm md:text-xl font-bold text-balance">
                        Instalación remota
                    </p>
                </div>
            </div>
        </section>

        <!-- METODO DE PAGO Y HERRAMIENTAS -->
        <section class="xl:py-20 py-8 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <div class="container mx-auto mb-4 shadow-xl rounded-b-xl" x-data="{ tab: 'tab1' }">
                <ul class="flex w-full">
                    <li class="flex-1 -mb-px">
                        <a class="block rounded-t-xl w-full text-center xl:py-3 py-2 xl:px-4 px-2 rounded-t border-t !border-l xl:font-extrabold font-bold xl:text-2xl text-base"
                            href="#"
                            :class="{ 'bg-white text-gray-900 font-extrabold border-l border-t-8 border-r border-[#FFBD47]': tab == 'tab1'}"
                            @click.prevent="tab = 'tab1'">Método de pago</a>
                    </li>
                    <li class="flex-1 -mb-px">
                        <a class="block rounded-t-xl w-full text-center xl:py-3 py-2 xl:px-4 px-2 rounded-t border-t !border-r xl:font-extrabold font-bold text-gray-500 xl:text-2xl text-base"
                            href="#"
                            :class="{ 'bg-white text-gray-900 font-extrabold border-t-8 border-l border-[#FFBD47]': tab == 'tab2'}"
                            @click.prevent="tab = 'tab2'">
                            <span class="hidden xl:inline">Herramientas de instalación remota</span>
                            <span class="xl:hidden">Inst. remota</span>
                        </a>
                    </li>
                </ul>
                <div class="content rounded-b-xl bg-white border-l border-r border-b border-[#FFBD47] pt-4 border-t">
                    <div x-show="tab == 'tab1'" class="xl:p-16 p-6">
                        <p class="xl:text-2xl text-lg">
                            En DDG aceptamos PayPal, Western Union y MoneyGram; generamos la orden de pago para que
                            usted la complete; tenga en cuenta que PayPal incluye una tarifa adicional.
                        </p>
                        <div class="flex flex-col sm:flex-row justify-between w-full xl:mt-10 mt-6 gap-4">
                            <img src="/assets/icons/svg/paymet-methos/paypal.svg" alt=""
                                class="h-8 sm:h-auto object-contain">
                            <img src="/assets/icons/svg/paymet-methos/westernunion.svg" alt=""
                                class="h-8 sm:h-auto object-contain">
                            <img src="/assets/icons/svg/paymet-methos/moneygram.svg" alt=""
                                class="h-8 sm:h-auto object-contain">
                        </div>
                    </div>
                    <div x-show="tab == 'tab2'" class="xl:p-16 p-6">
                        <p class="xl:text-2xl text-lg">
                            En DDG utilizamos AnyDesk, TeamViewer y UltraViewer para instalaciones remotas; solo
                            conéctese a Internet y nos encargaremos del resto de forma rápida, segura y sin
                            complicaciones.
                        </p>
                        <div class="flex flex-col sm:flex-row justify-between w-full xl:mt-10 mt-6 gap-4">
                            <img src="/assets/icons/svg/paymet-methos/westernunion.svg" alt=""
                                class="h-8 sm:h-auto object-contain">
                            <img src="/assets/icons/svg/paymet-methos/moneygram.svg" alt=""
                                class="h-8 sm:h-auto object-contain">
                            <img src="/assets/icons/svg/remote-tools/ultraviewer.svg" alt=""
                                class="h-8 sm:h-auto object-contain">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section class="xl:py-20 py-6 px-4 mx-auto max-w-screen-2xl ">
            <div class="text-start mb-10">
                <h2 class="uppercase font-extrabold xl:text-3xl lg:text-2xl md:text-xl text-lg mb-4">
                    Lo que dicen nuestros clientes
                </h2>

            </div>

            <section id="testimonials-carousel" class="splide" aria-label="Testimonios de clientes">
                <div class="splide__track pb-6 pt-3">
                    <ul class="splide__list h-[51vh]">
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

    <!-- Botones flotantes -->
    <!-- <div class="fixed bottom-5 right-5 flex flex-col items-end gap-6">
        <img src="/assets/icons/svg/WhatsAppChat.svg" alt="" class="w-16 h-16 cursor-pointer">
        <img src="/assets/images/chatLogo.png" alt="" class="w-[200px] h-full cursor-pointer" id="chatBotTrigger">
    </div> -->

    <!-- Chatbot Flotante -->
    <div id="chatbotContainer"
        class="hidden fixed bottom-5 right-5 w-80 h-96 bg-white rounded-lg shadow-2xl border border-gray-200 z-50 flex-col overflow-hidden">
        <!-- Header del Chatbot -->
        <div class="bg-gradient-to-r from-amber-400 to-amber-500 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                    <span class="text-amber-500 text-sm font-bold">🤖</span>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-sm">Asistente Virtual</h3>
                    <p class="text-amber-100 text-xs">En línea</p>
                </div>
            </div>
            <button id="closeChatbot" class="text-white hover:text-amber-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Área de mensajes -->
        <div id="chatMessages" class="flex-1 p-4 overflow-y-auto bg-gray-50 space-y-3">
            <!-- Mensaje inicial del bot -->
            <div class="flex items-start gap-2">
                <div class="w-6 h-6 bg-amber-400 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs">🤖</span>
                </div>
                <div class="bg-white rounded-lg rounded-tl-none px-3 py-2 shadow-sm max-w-xs">
                    <p class="text-sm text-gray-800">¡Hola! 👋 Soy tu asistente virtual. ¿En qué puedo ayudarte hoy?</p>
                </div>
            </div>
        </div>

        <!-- Input de mensaje -->
        <div class="border-t bg-white p-3">
            <div class="flex gap-2">
                <input type="text" id="chatInput" placeholder="Escribe tu mensaje..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent">
                <button id="sendMessage"
                    class="bg-amber-400 hover:bg-amber-500 text-white px-3 py-2 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Indicador de escritura -->
        <div id="typingIndicator" class="hidden px-4 py-2 bg-gray-50">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-amber-400 rounded-full flex items-center justify-center">
                    <span class="text-white text-xs">🤖</span>
                </div>
                <div class="flex gap-1">
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-pulse"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                </div>
                <span class="text-xs text-gray-500">Escribiendo...</span>
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

        document.addEventListener("DOMContentLoaded", function () {
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
                chatTrigger.addEventListener("click", function (e) {
                    console.log('🖱️ Chat trigger clicked!');
                    e.preventDefault();
                    e.stopPropagation();
                    openChatbot();
                });

                // Event listener para cerrar
                if (closeButton) {
                    closeButton.addEventListener("click", function (e) {
                        console.log('🖱️ Close button clicked!');
                        e.preventDefault();
                        e.stopPropagation();
                        closeChatbot();
                    });
                }

                // Agregar hover effect
                chatTrigger.addEventListener("mouseenter", function () {
                    chatTrigger.style.transform = "scale(1.05)";
                    chatTrigger.style.transition = "transform 0.2s ease";
                });

                chatTrigger.addEventListener("mouseleave", function () {
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
    <script type="text/javascript">
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
    </script>
    <!--End of Tawk.to Script-->
</body>

</html>