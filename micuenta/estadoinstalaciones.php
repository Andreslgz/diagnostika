<?php
// public/index.php (añadir lógica para mostrar el carrito)
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../auth.php';

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
    <!-- Breadcrumbs -->
    <section class="xl:pt-16 py-4 px-4 mx-auto max-w-screen-2xl overflow-hidden">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
          <li class="inline-flex items-center">
            <a href="../tienda/index.php"
              class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-orange-600 ">
              <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 20 20">
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
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 9 4-4-4-4" />
              </svg>
              <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-orange-600 md:ms-2 ">Mi
                cuenta</a>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m1 9 4-4-4-4" />
              </svg>
              <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">Mis softwares</span>
            </div>
          </li>
        </ol>
      </nav>

    </section>
    <section class="xl:pb-16 py-4 md:py-6 px-4 mx-auto max-w-screen-2xl overflow-hidden xl:mb-24 mb-16">
      <div>
        <h1 class="text-xl md:text-2xl font-extrabold mb-4">
          Mi cuenta
        </h1>

        <!-- Botón del menú móvil (visible solo en móviles) -->
        <button id="mobileMenuToggle"
          class="lg:hidden w-full mb-4 p-3 btn-secondary text-white rounded-lg flex items-center justify-between">
          <span>Menú de navegación</span>
          <svg class="w-5 h-5 transform transition-transform" id="menuIcon" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
            </path>
          </svg>
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-12 mt-4 gap-4 lg:gap-10 xl:gap-20">
          <!-- Menú lateral - Responsive -->
          <div class="col-span-1 lg:col-span-4 xl:col-span-3">
            <div id="sideMenu"
              class="border border-solid border-gray-300 rounded menu-transition overflow-hidden max-h-0 lg:max-h-none opacity-0 lg:opacity-100">
              <a href="./informacionpersonal.php"
                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                Personal Info
              </a>
              <a href="./misoftware.php"
                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                My Software
              </a>
              <div class="p-3 btn-primary bg-blue-600 ">
                Installation Status
              </div>
              <a href="./miscupones.php"
                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                My Coupons
              </a>
              <a href="./miscreditos.php"
                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                My Credits
              </a>
              <a href="./productosguardados.php"
                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                Saved Items
              </a>
              <div class="p-3 hover:bg-gray-200 hover:cursor-pointer transition-colors text-red-600 font-medium">
                Log Out
              </div>
            </div>
          </div>

          <!-- Contenido principal - Responsive -->
          <div class="col-span-1 lg:col-span-8 xl:col-span-9">
            <div>
              <h2 class="font-bold text-lg md:text-xl mb-2 sm:mb-4">
                Instalation Status
              </h2>
              <!-- ITEM -->
              <div>
                <header>
                  <div class="w-full flex flex-row justify-between">
                    <p class="xl:text-lg text-sm font-bold">ORDER #12345</p>
                    <p class="xl:text-lg text-sm text-gray-500 font-semibold">JUNY 05, 2025 12:00 PM</p>
                  </div>
                  <div class="flex flex-row items-center gap-2 mt-2 text-gray-500 xl:text-base text-xs">
                    <p>Estimated time to complete the order:</p>
                    <p class="font-semibold">
                      5 hours 45 minutes
                    </p>
                  </div>
                </header>
                <!-- ESTADO 1 - GRIS -->
                <div class="overflow-x-auto p-4 md:p-4">
                  <div class="flex flex-row items-center min-w-max gap-4">
                    <!-- Primer estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/gris/Request_Received_Gris.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-gray-400 w-4 h-4 rounded-full bg-white"></div>
                      <div class="text-center font-bold text-xs md:text-sm">
                        <p>Request Received</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>

                    <!-- Línea conectora 1 -->
                    <div class="flex items-center justify-center px-2 md:px-4 min-w-[60px] flex-shrink-0">
                      <div class="w-12 md:w-16 lg:w-20 h-0.5 bg-gray-300"></div>
                    </div>

                    <!-- Segundo estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/gris/Installation_Started_Gris.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-gray-400 w-4 h-4 rounded-full bg-white"></div>
                      <div class="text-center font-bold text-xs md:text-sm">
                        <p>Installation Started</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>

                    <!-- Línea conectora 2 -->
                    <div class="flex items-center justify-center px-2 md:px-4 min-w-[60px] flex-shrink-0">
                      <div class="w-12 md:w-16 lg:w-20 h-0.5 bg-gray-300"></div>
                    </div>

                    <!-- Tercer estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/gris/Payment_Confirmed_Gris.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-gray-400 w-4 h-4 rounded-full bg-white"></div>
                      <div class="text-center font-bold text-xs md:text-sm">
                        <p>Payment Confirmed</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>

                    <!-- Línea conectora 3 -->
                    <div class="flex items-center justify-center px-2 md:px-4 min-w-[60px] flex-shrink-0">
                      <div class="w-12 md:w-16 lg:w-20 h-0.5 bg-gray-300"></div>
                    </div>

                    <!-- Cuarto estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/gris/Installation_Completed_Gris.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-gray-400 w-4 h-4 rounded-full bg-white"></div>
                      <div class="text-center font-bold text-xs md:text-sm">
                        <p>Installation Completed</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- ESTADO 2 - NARANJA -->
                <div class="overflow-x-auto p-4 md:p-4">
                  <div class="flex flex-row items-center min-w-max gap-4">
                    <!-- Primer estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/orange/Request_Received_Naranja.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-orange-300 bg-orange-300 w-4 h-4 rounded-full ">
                      </div>
                      <div class="text-center font-bold text-xs md:text-sm">
                        <p>Request Received</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>

                    <!-- Línea conectora 1 -->
                    <div class="flex items-center justify-center px-2 md:px-4 min-w-[60px] flex-shrink-0">
                      <div class="w-12 md:w-16 lg:w-20 h-0.5 bg-orange-300"></div>
                    </div>

                    <!-- Segundo estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/orange/Installation_Started_Naranja.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-orange-300 bg-orange-300 w-4 h-4 rounded-full ">
                      </div>
                      <div class="text-center font-bold text-xs md:text-sm">
                        <p>Installation Started</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>

                    <!-- Línea conectora 2 -->
                    <div class="flex items-center justify-center px-2 md:px-4 min-w-[60px] flex-shrink-0">
                      <div class="w-12 md:w-16 lg:w-20 h-0.5 bg-orange-300"></div>
                    </div>

                    <!-- Tercer estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/orange/Payment_Confirmed_Naranja.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-orange-300 bg-orange-300 w-4 h-4 rounded-full ">
                      </div>
                      <div class="text-center font-bold text-xs md:text-sm">
                        <p>Payment Confirmed</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>

                    <!-- Línea conectora 3 -->
                    <div class="flex items-center justify-center px-2 md:px-4 min-w-[60px] flex-shrink-0">
                      <div class="w-12 md:w-16 lg:w-20 h-0.5 bg-orange-300"></div>
                    </div>

                    <!-- Cuarto estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/orange/Installation_Completed_Naranja.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-orange-300 bg-orange-300 w-4 h-4 rounded-full ">
                      </div>
                      <div class="text-center font-bold text-xs md:text-sm">
                        <p>Installation Completed</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- ESTADO 3 - ROJO -->

                <div class="overflow-x-auto p-4 md:p-4">
                  <div class="flex flex-row items-center min-w-max gap-4">
                    <!-- Primer estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/red/Request_Received_Red.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-red-500 bg-red-500 w-4 h-4 rounded-full ">
                      </div>
                      <div class="text-center font-bold text-xs md:text-sm text-red-500">
                        <p>Request Received</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>

                    <!-- Línea conectora 1 -->
                    <div class="flex items-center justify-center px-2 md:px-4 min-w-[60px] flex-shrink-0">
                      <div class="w-12 md:w-16 lg:w-20 h-0.5 bg-red-300"></div>
                    </div>

                    <!-- Segundo estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/red/Installation_Started_Red.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-red-500 bg-red-500 w-4 h-4 rounded-full ">
                      </div>
                      <div class="text-center font-bold text-xs md:text-sm text-red-500">
                        <p>Installation Started</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>

                    <!-- Línea conectora 2 -->
                    <div class="flex items-center justify-center px-2 md:px-4 min-w-[60px] flex-shrink-0">
                      <div class="w-12 md:w-16 lg:w-20 h-0.5 bg-red-300"></div>
                    </div>

                    <!-- Tercer estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/red/Payment_Confirmed_Red.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-red-500 bg-red-500 w-4 h-4 rounded-full ">
                      </div>
                      <div class="text-center font-bold text-xs md:text-sm text-red-500">
                        <p>Payment Confirmed</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>

                    <!-- Línea conectora 3 -->
                    <div class="flex items-center justify-center px-2 md:px-4 min-w-[60px] flex-shrink-0">
                      <div class="w-12 md:w-16 lg:w-20 h-0.5 bg-red-300"></div>
                    </div>

                    <!-- Cuarto estado -->
                    <div class="flex items-center flex-col gap-2 min-w-[120px] flex-shrink-0">
                      <img src="/assets/icons/estados_instalacion/red/Installation_Completed_Red.svg"
                        class="w-16 h-16 md:w-20 lg:w-24 xl:size-[95px]" alt="">
                      <div class="border border-solid border-red-500 bg-red-500 w-4 h-4 rounded-full ">
                      </div>
                      <div class="text-center font-bold text-xs md:text-sm text-red-500">
                        <p>Installation Completed</p>
                        <p>22/07/2025</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Resumen de productos -->
                <div class="p-4 border border-solid border-gray-300 shadow-sm rounded-lg mt-6">
                  <!-- Header con título y controles de navegación -->
                  <div class="flex flex-row items-center justify-between mb-3">
                    <p class="font-bold text-lg">ITEMS</p>

                    <!-- Controles de navegación del slider -->
                    <div class="flex items-center gap-2">
                      <button id="itemsSliderPrev"
                        class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center transition-all duration-200 border border-gray-300 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                          </path>
                        </svg>
                      </button>
                      <button id="itemsSliderNext"
                        class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center transition-all duration-200 border border-gray-300 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Slider de items -->
                  <div class="splide" id="itemsSlider">
                    <div class="splide__track h-min">
                      <ul class="splide__list h-min">
                        <li class="splide__slide">
                          <div
                            class="text-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                            <div class="w-20 h-20 mx-auto mb-2 bg-gray-50 rounded-lg flex items-center justify-center">
                              <img src="/assets/images/carritoProducto.png" class="w-16 h-16 object-contain" alt="">
                            </div>
                            <p class="font-semibold text-sm text-gray-800 leading-tight">Cummins insite 9.1 pro</p>
                          </div>
                        </li>
                        <li class="splide__slide">
                          <div
                            class="text-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                            <div class="w-20 h-20 mx-auto mb-2 bg-gray-50 rounded-lg flex items-center justify-center">
                              <img src="/assets/images/carritoProducto.png" class="w-16 h-16 object-contain" alt="">
                            </div>
                            <p class="font-semibold text-sm text-gray-800 leading-tight">Cummins insite 9.1 pro</p>
                          </div>
                        </li>
                        <li class="splide__slide">
                          <div
                            class="text-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                            <div class="w-20 h-20 mx-auto mb-2 bg-gray-50 rounded-lg flex items-center justify-center">
                              <img src="/assets/images/carritoProducto.png" class="w-16 h-16 object-contain" alt="">
                            </div>
                            <p class="font-semibold text-sm text-gray-800 leading-tight">Cummins insite 9.1 pro</p>
                          </div>
                        </li>
                        <li class="splide__slide">
                          <div
                            class="text-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                            <div class="w-20 h-20 mx-auto mb-2 bg-gray-50 rounded-lg flex items-center justify-center">
                              <img src="/assets/images/carritoProducto.png" class="w-16 h-16 object-contain" alt="">
                            </div>
                            <p class="font-semibold text-sm text-gray-800 leading-tight">Cummins insite 9.1 pro</p>
                          </div>
                        </li>
                        <li class="splide__slide">
                          <div
                            class="text-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                            <div class="w-20 h-20 mx-auto mb-2 bg-gray-50 rounded-lg flex items-center justify-center">
                              <img src="/assets/images/carritoProducto.png" class="w-16 h-16 object-contain" alt="">
                            </div>
                            <p class="font-semibold text-sm text-gray-800 leading-tight">Cummins insite 9.1 pro</p>
                          </div>
                        </li>
                        <li class="splide__slide">
                          <div
                            class="text-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                            <div class="w-20 h-20 mx-auto mb-2 bg-gray-50 rounded-lg flex items-center justify-center">
                              <img src="/assets/images/carritoProducto.png" class="w-16 h-16 object-contain" alt="">
                            </div>
                            <p class="font-semibold text-sm text-gray-800 leading-tight">Cummins insite 9.1 pro</p>
                          </div>
                        </li>
                        <li class="splide__slide">
                          <div
                            class="text-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                            <div class="w-20 h-20 mx-auto mb-2 bg-gray-50 rounded-lg flex items-center justify-center">
                              <img src="/assets/images/carritoProducto.png" class="w-16 h-16 object-contain" alt="">
                            </div>
                            <p class="font-semibold text-sm text-gray-800 leading-tight">Cummins insite 9.1 pro</p>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>

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

    /* Estilos optimizados para el slider de items */
    #itemsSlider .splide__slide {
      display: flex;
      align-items: stretch;
      justify-content: center;
      padding: 0.25rem;
    }

    #itemsSlider .splide__slide>div {
      width: 100%;
      min-height: 120px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }




    /* Botones de navegación optimizados */
    #itemsSliderPrev,
    #itemsSliderNext {
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #itemsSliderPrev:hover:not(:disabled),
    #itemsSliderNext:hover:not(:disabled) {
      background: linear-gradient(0deg, #f7a615 0%, #ffbd47 100%);
      color: white;
      border-color: #f7a615;
      transform: scale(1.1);
      box-shadow: 0 4px 12px rgba(247, 166, 21, 0.4);
    }

    #itemsSliderPrev:active:not(:disabled),
    #itemsSliderNext:active:not(:disabled) {
      background: linear-gradient(0deg, #e6940b 0%, #f7a615 100%);
      transform: scale(1.05);
      box-shadow: 0 2px 8px rgba(247, 166, 21, 0.5);
    }

    #itemsSliderPrev:disabled,
    #itemsSliderNext:disabled {
      opacity: 0.4;
      cursor: not-allowed;
      background-color: #f3f4f6 !important;
      color: #9ca3af !important;
    }

    /* Contenedor del slider más compacto */
    #itemsSlider .splide__track {
      padding: 0.25rem 0;
    }

    /* Responsive mejorado */
    @media (max-width: 1024px) {
      #itemsSlider .splide__slide>div {
        min-height: 110px;
        padding: 0.75rem;
      }
    }

    @media (max-width: 768px) {
      #itemsSlider .splide__slide>div {
        min-height: 100px;
        padding: 0.5rem;
      }

      #itemsSliderPrev,
      #itemsSliderNext {
        width: 1.75rem;
        height: 1.75rem;
      }

      #itemsSliderPrev svg,
      #itemsSliderNext svg {
        width: 0.875rem;
        height: 0.875rem;
      }
    }

    @media (max-width: 640px) {
      #itemsSlider .splide__slide>div {
        min-height: 90px;
      }
    }

    /* Animación suave para el contenedor */
    .splide {
      visibility: visible;
      opacity: 1;
      transition: opacity 0.3s ease;
    }

    /* Mejora visual para el fondo de las imágenes */
    #itemsSlider .splide__slide>div>div {
      background: linear-gradient(145deg, #f8fafc, #f1f5f9);
      border: 1px solid #e2e8f0;
    }
  </style>

  <script>
    // Toggle del menú móvil
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sideMenu = document.getElementById('sideMenu');
    const menuIcon = document.getElementById('menuIcon');
    let isMenuOpen = false;

    if (mobileMenuToggle) {
      mobileMenuToggle.addEventListener('click', function () {
        isMenuOpen = !isMenuOpen;

        if (isMenuOpen) {
          sideMenu.style.maxHeight = sideMenu.scrollHeight + 'px';
          sideMenu.style.opacity = '1';
          menuIcon.style.transform = 'rotate(180deg)';
        } else {
          sideMenu.style.maxHeight = '0';
          sideMenu.style.opacity = '0';
          menuIcon.style.transform = 'rotate(0deg)';
        }
      });
    }

    // Asegurar que el menú esté visible en pantallas grandes
    window.addEventListener('resize', function () {
      if (window.innerWidth >= 1024) { // lg breakpoint
        sideMenu.style.maxHeight = 'none';
        sideMenu.style.opacity = '1';
      } else if (!isMenuOpen) {
        sideMenu.style.maxHeight = '0';
        sideMenu.style.opacity = '0';
      }
    });

    // Inicializar slider de items
    document.addEventListener('DOMContentLoaded', function () {
      // Configuración del slider de items
      const itemsSlider = new Splide('#itemsSlider', {
        type: 'slide',
        perPage: 4, // Mostrar 4 items por defecto
        perMove: 1,
        gap: '1rem',
        pagination: false,
        arrows: false, // Usamos botones customizados
        breakpoints: {
          1280: { // xl
            perPage: 4,
            gap: '1rem',
          },
          1024: { // lg
            perPage: 3,
            gap: '0.75rem',
          },
          768: { // md
            perPage: 2,
            gap: '0.5rem',
          },
          640: { // sm
            perPage: 1,
            gap: '0.25rem',
          },
        },
        autoplay: false,
        interval: 5000,
        pauseOnHover: true,
        resetProgress: false,
        speed: 600,
        easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
      });

      // Montar el slider
      itemsSlider.mount();

      // Conectar botones personalizados
      const prevBtn = document.getElementById('itemsSliderPrev');
      const nextBtn = document.getElementById('itemsSliderNext');

      if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
          itemsSlider.go('<');
        });

        nextBtn.addEventListener('click', () => {
          itemsSlider.go('>');
        });

        // Actualizar estado de los botones según la posición
        itemsSlider.on('moved', function (newIndex, prevIndex, destIndex) {
          // Habilitar/deshabilitar botones según la posición
          const isAtStart = itemsSlider.index === 0;
          const isAtEnd = itemsSlider.index >= itemsSlider.length - itemsSlider.options.perPage;

          prevBtn.disabled = isAtStart;
          nextBtn.disabled = isAtEnd;

          // Agregar clases visuales para botones deshabilitados
          if (isAtStart) {
            prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
            prevBtn.classList.remove('hover:bg-orange-gradient', 'hover:text-white');
          } else {
            prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            prevBtn.classList.add('hover:bg-orange-gradient', 'hover:text-white');
          }

          if (isAtEnd) {
            nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
            nextBtn.classList.remove('hover:bg-orange-gradient', 'hover:text-white');
          } else {
            nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            nextBtn.classList.add('hover:bg-orange-gradient', 'hover:text-white');
          }
        });

        // Estado inicial de los botones
        prevBtn.disabled = true;
        prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
        prevBtn.classList.remove('hover:bg-orange-gradient', 'hover:text-white');

        // Verificar si hay suficientes slides para mostrar el botón siguiente
        const totalSlides = itemsSlider.length;
        const visibleSlides = itemsSlider.options.perPage;
        if (totalSlides <= visibleSlides) {
          nextBtn.disabled = true;
          nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
          nextBtn.classList.remove('hover:bg-orange-gradient', 'hover:text-white');
        }
      }
    });

  </script>


</body>

</html>