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
    <section class="xl:pb-16 py-4 md:py-6 px-4 mx-auto max-w-screen-2xl overflow-hidden">
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

  </script>




</body>

</html>