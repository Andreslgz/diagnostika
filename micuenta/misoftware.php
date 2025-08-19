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
                                Información personal
                            </a>
                            <div class="p-3 btn-primary bg-blue-600 ">
                                Mis software
                            </div>
                            <a href="./estadoinstalaciones.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Estado de instalación
                            </a>
                            <a href="./miscupones.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Mis cupones
                            </a>
                            <a href="./miscreditos.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Mis créditos
                            </a>
                            <a href="./productosguardados.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Productos guardados
                            </a>
                            <div
                                class="p-3 hover:bg-gray-200 hover:cursor-pointer transition-colors text-red-600 font-medium">
                                Cerrar sesión
                            </div>
                        </div>
                    </div>

                    <!-- Contenido principal - Responsive -->
                    <div class="col-span-1 lg:col-span-8 xl:col-span-9">
                        <div>
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <h2 class="font-bold text-lg md:text-xl mb-2 sm:mb-4">
                                    Órdenes de paquetes y software
                                </h2>

                            </div>
                            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <p class="font-medium mb-3 text-gray-700">Buscar por fecha</p>

                                <!-- Opciones de filtrado -->
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <button
                                        class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500 text-sm md:text-base"
                                        data-filter="dia">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Día
                                    </button>
                                    <button
                                        class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500 text-sm md:text-base active"
                                        data-filter="mes">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Mes
                                    </button>
                                    <button
                                        class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500 text-sm md:text-base"
                                        data-filter="año">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        Año
                                    </button>
                                    <button
                                        class="filter-btn px-4 py-2 rounded-lg border border-gray-300 hover:border-blue-500 text-sm md:text-base"
                                        data-filter="personalizado">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                            </path>
                                        </svg>
                                        Personalizado
                                    </button>
                                </div>

                                <!-- Input de fecha dinámico -->
                                <div id="dateInputContainer" class="flex flex-col sm:flex-row gap-3">
                                    <select id="monthSelect" class="border border-gray-300 rounded-lg p-2 flex-1">
                                        <option value="01">Enero</option>
                                        <option value="02">Febrero</option>
                                        <option value="03">Marzo</option>
                                        <option value="04">Abril</option>
                                        <option value="05">Mayo</option>
                                        <option value="06" selected>Junio</option>
                                        <option value="07">Julio</option>
                                        <option value="08">Agosto</option>
                                        <option value="09">Septiembre</option>
                                        <option value="10">Octubre</option>
                                        <option value="11">Noviembre</option>
                                        <option value="12">Diciembre</option>
                                    </select>
                                    <select id="yearSelect"
                                        class="border border-gray-300 rounded-lg p-2 w-full sm:w-32">
                                        <option value="2023">2023</option>
                                        <option value="2024">2024</option>
                                        <option value="2025" selected>2025</option>
                                    </select>
                                    <button
                                        class="btn-secondary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        Buscar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="ordenes-container" class="border border-solid border-gray-400 rounded-lg p-4 max-h-[750px] overflow-y-auto flex flex-col gap-3"></div>
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

        // Sistema de filtros
        const filterButtons = document.querySelectorAll('.filter-btn');
        const dateInputContainer = document.getElementById('dateInputContainer');

        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Remover clase active de todos los botones
                filterButtons.forEach(btn => btn.classList.remove('active'));
                // Agregar clase active al botón clickeado
                this.classList.add('active');

                const filterType = this.dataset.filter;
                updateDateInputs(filterType);
            });
        });

        function updateDateInputs(filterType) {
            let html = '';

            switch (filterType) {
                case 'dia':
                    html = `
                        <input type="date" class="border border-gray-300 rounded-lg p-2 flex-1" value="2025-06-01">
                        <button class="btn-secondary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Buscar
                        </button>
                    `;
                    break;

                case 'mes':
                    html = `
                        <select class="border border-gray-300 rounded-lg p-2 flex-1">
                            <option value="01">Enero</option>
                            <option value="02">Febrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06" selected>Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select>
                        <select class="border border-gray-300 rounded-lg p-2 w-full sm:w-32">
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                        </select>
                        <button class="btn-secondary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Buscar
                        </button>
                    `;
                    break;

                case 'año':
                    html = `
                        <select class="border border-gray-300 rounded-lg p-2 flex-1">
                            <option value="2020">2020</option>
                            <option value="2021">2021</option>
                            <option value="2022">2022</option>
                            <option value="2023">2023</option>
                            <option value="2024">2024</option>
                            <option value="2025" selected>2025</option>
                            <option value="2026">2026</option>
                        </select>
                        <button class="btn-secondary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Buscar
                        </button>
                    `;
                    break;

                case 'personalizado':
                    html = `
                        <div class="flex flex-col sm:flex-row gap-2 flex-1">
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600">Desde:</label>
                                <input type="date" class="border border-gray-300 rounded-lg p-2" value="2025-01-01">
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600">Hasta:</label>
                                <input type="date" class="border border-gray-300 rounded-lg p-2" value="2025-06-30">
                            </div>
                        </div>
                        <button class="btn-secondary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Buscar
                        </button>
                    `;
                    break;
            }

            dateInputContainer.innerHTML = html;

            // Animación de entrada
            dateInputContainer.style.opacity = '0';
            setTimeout(() => {
                dateInputContainer.style.transition = 'opacity 0.3s ease';
                dateInputContainer.style.opacity = '1';
            }, 10);
        }
    </script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // ========= CONFIG =========
  const BASE = (typeof window.BASE_DIR !== 'undefined' ? window.BASE_DIR : (typeof BASE_DIR !== 'undefined' ? BASE_DIR : '')) || '';
  const ORDERS_ENDPOINT = BASE.replace(/\/$/, '') + '/micuenta/ajax_ordenes.php';

  // ========= DOM =========
  const filterButtons       = Array.from(document.querySelectorAll('.filter-btn'));
  const dateInputContainer  = document.getElementById('dateInputContainer');
  const ordersContainer     = document.getElementById('ordenes-container');

  if (!ordersContainer) {
    console.error('No existe #ordenes-container');
    return;
  }
  if (!dateInputContainer) {
    console.error('No existe #dateInputContainer');
    return;
  }

  // ========= Helpers =========
  const esc = (s) => String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  const fmtFecha = (s) => {
    if (!s) return '';
    const d = new Date(String(s).replace(' ', 'T')); // "YYYY-MM-DD HH:mm:ss" → "YYYY-MM-DDTHH:mm:ss"
    return isNaN(d) ? s : d.toLocaleDateString(undefined, { day:'2-digit', month:'2-digit', year:'numeric' });
  };

  // ========= Estado =========
  let currentFilter = (document.querySelector('.filter-btn.active')?.dataset.filter || 'mes').toLowerCase();

  // ========= UI de inputs según filtro =========
  function renderInputs(filter) {
    switch (filter) {
      case 'dia':
        dateInputContainer.innerHTML = `
          <input type="date" id="dayInput" class="border border-gray-300 rounded-lg p-2 flex-1" />
          <button id="btnBuscar" class="btn-secondary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Buscar</button>
        `;
        break;
      case 'mes':
        dateInputContainer.innerHTML = `
          <select id="monthSelect" class="border border-gray-300 rounded-lg p-2 flex-1">
            <option value="01">Enero</option><option value="02">Febrero</option><option value="03">Marzo</option>
            <option value="04">Abril</option><option value="05">Mayo</option><option value="06" selected>Junio</option>
            <option value="07">Julio</option><option value="08">Agosto</option><option value="09">Septiembre</option>
            <option value="10">Octubre</option><option value="11">Noviembre</option><option value="12">Diciembre</option>
          </select>
          <select id="yearSelect" class="border border-gray-300 rounded-lg p-2 w-full sm:w-32">
            <option value="2023">2023</option><option value="2024">2024</option><option value="2025" selected>2025</option>
          </select>
          <button id="btnBuscar" class="btn-secondary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Buscar</button>
        `;
        break;
      case 'año':
      case 'ano': // por si el dataset viene sin tilde
        dateInputContainer.innerHTML = `
          <select id="yearOnly" class="border border-gray-300 rounded-lg p-2 w-full sm:w-40">
            <option value="2023">2023</option><option value="2024">2024</option><option value="2025" selected>2025</option>
          </select>
          <button id="btnBuscar" class="btn-secondary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Buscar</button>
        `;
        break;
      case 'personalizado':
        dateInputContainer.innerHTML = `
          <input type="date" id="fromInput" class="border border-gray-300 rounded-lg p-2 flex-1" />
          <input type="date" id="toInput"   class="border border-gray-300 rounded-lg p-2 flex-1" />
          <button id="btnBuscar" class="btn-secondary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">Buscar</button>
        `;
        break;
    }

    // Hook del botón Buscar
    const btnBuscar = document.getElementById('btnBuscar');
    if (btnBuscar) btnBuscar.addEventListener('click', (ev) => { ev.preventDefault(); fetchAndRender(); });
  }

  // ========= Construcción de params =========
  function buildParams() {
    const params = new URLSearchParams();
    params.set('filter', currentFilter);

    if (currentFilter === 'dia') {
      const day = document.getElementById('dayInput')?.value?.trim();
      if (day) params.set('day', day);
    } else if (currentFilter === 'mes') {
      const m = document.getElementById('monthSelect')?.value?.trim();
      const y = document.getElementById('yearSelect')?.value?.trim();
      if (m) params.set('month', m);
      if (y) params.set('year', y);
    } else if (currentFilter === 'año' || currentFilter === 'ano') {
      const y = document.getElementById('yearOnly')?.value?.trim();
      if (y) params.set('year', y);
    } else if (currentFilter === 'personalizado') {
      const from = document.getElementById('fromInput')?.value?.trim();
      const to   = document.getElementById('toInput')?.value?.trim();
      if (from) params.set('from', from);
      if (to)   params.set('to', to);
    }

    return params;
  }

  // ========= Pintado =========
  function renderOrders(list) {
    if (!Array.isArray(list) || list.length === 0) {
      ordersContainer.innerHTML = `
        <div class="p-6 text-center text-gray-600 border border-dashed border-gray-300 rounded-lg">
          No se encontraron órdenes con el filtro seleccionado.
        </div>`;
      return;
    }

    const html = list.map(row => {
      const imgSrc = row.imagen
        ? (BASE.replace(/\/$/,'') + $url.'/uploads/' + String(row.imagen).replace(/^\/+/, ''))
        : 'https://placehold.co/300x180/png';
      return `
      <div class="border border-solid border-gray-300 rounded-lg overflow-hidden">
        <header class="bg-[#00c016] text-white xl:p-3 p-2 flex items-center gap-2">
          <img src="<?php echo $url;?>/assets/icons/svg/icon-instalacion.svg" alt="" class="w-5 h-5">
          <p class="font-medium">Instalación completada</p>
        </header>
        <div class="xl:p-5 p-3 flex items-center gap-4">
          <img src="${esc(imgSrc)}" alt="${esc(row.nombre)}" class="xl:size-[150px] size-[70px] object-cover rounded">
          <div class="flex flex-col gap-1">
            <p class="uppercase font-bold xl:text-xl text-sm">${esc(row.nombre)}</p>
            <p class="uppercase text-gray-500 font-semibold xl:text-sm text-xs">Fecha: ${esc(fmtFecha(row.fecha))}</p>
            <p class="uppercase text-gray-500 font-semibold xl:text-sm text-xs">Marca: ${esc(row.marca ?? '—')}</p>
            <p class="text-xs text-gray-400">#Orden: ${esc(row.id_orden)}</p>
          </div>
        </div>
      </div>`;
    }).join('');

    ordersContainer.innerHTML = html;
  }

  // ========= Fetch =========
  async function fetchAndRender() {
    const params = buildParams();
    ordersContainer.innerHTML = `<div class="p-6 text-center text-gray-500">Cargando...</div>`;

    try {
      const res = await fetch(ORDERS_ENDPOINT, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: params
      });

      const raw = await res.text();
      console.log('[ORDERS raw]', raw);

      let data = null;
      try { data = JSON.parse(raw); } catch { /* intenta extraer JSON si hay BOM/debug */ 
        const s = raw.indexOf('{'), e = raw.lastIndexOf('}');
        if (s > -1 && e > s) { try { data = JSON.parse(raw.slice(s, e+1)); } catch {} }
      }

      if (!data || !res.ok || data.ok !== true) {
        const msg = data?.message || `HTTP ${res.status}`;
        throw new Error(msg);
      }

      renderOrders(data.data);
    } catch (err) {
      console.error('[ORDERS error]', err);
      ordersContainer.innerHTML = `<div class="p-6 text-center text-red-600">Error al obtener órdenes: ${esc(err.message || 'desconocido')}</div>`;
    }
  }

  // ========= Eventos de filtro =========
  filterButtons.forEach(btn => {
    btn.addEventListener('click', (ev) => {
      ev.preventDefault();
      filterButtons.forEach(b => b.classList.remove('active', 'border-blue-500'));
      btn.classList.add('active', 'border-blue-500');
      currentFilter = (btn.dataset.filter || 'mes').toLowerCase();
      renderInputs(currentFilter);
    });
  });

  // ========= Inicio =========
  renderInputs(currentFilter);
  // Si quieres lanzar una primera consulta automáticamente:
  const firstSearchBtn = document.getElementById('btnBuscar');
  if (firstSearchBtn) firstSearchBtn.click();
});
</script>

<script>
async function fetchJSON(url, init) {
  const res  = await fetch(url, { headers: { "X-Requested-With":"XMLHttpRequest" }, ...init });
  const text = await res.text();

  // Intentar parsear JSON "seguro"
  let data = null;
  try {
    data = JSON.parse(text);
  } catch {
    const s = text.indexOf('{'), e = text.lastIndexOf('}');
    if (s > -1 && e > s) {
      try { data = JSON.parse(text.slice(s, e + 1)); } catch {}
    }
  }

  if (!data) {
    // Muestra lo que vino realmente del server para depurar
    throw new Error("Respuesta no válida del servidor:\n" + text.slice(0, 500));
  }
  if (!res.ok || data.ok === false || data.success === false) {
    const msg = data.message || data.error || `HTTP ${res.status}`;
    throw new Error(msg);
  }
  return data;
}

async function loadOrders(filter) {
  const container = document.querySelector(".border.border-solid.border-gray-400");
  const month = document.getElementById("monthSelect")?.value;
  const year  = document.getElementById("yearSelect")?.value;

  const params = new URLSearchParams({ filter });
  if (filter === "mes") { params.append("month", month); params.append("year", year); }
  // TODO: añade aquí día / año / personalizado si tienes inputs

  try {
    const url  = "ajax_ordenes.php?" + params.toString(); // verifica la ruta
    const data = await fetchJSON(url);

    renderOrders(data.data || []);
  } catch (err) {
    console.error(err);
    container.innerHTML = `<pre class="text-red-600 whitespace-pre-wrap break-words">${(err.message||'Error').toString()}</pre>`;
  }
}

function renderOrders(rows) {
  const container = document.querySelector(".border.border-solid.border-gray-400");
  if (!rows.length) { container.innerHTML = "<p class='text-gray-500'>No se encontraron órdenes.</p>"; return; }
  container.innerHTML = rows.map(r => `
    <div>
      <header class="bg-[#00c016] text-white xl:p-3 p-1.5 flex items-center gap-2 rounded-t-lg">
        <img src="/assets/icons/svg/icon-instalacion.svg" alt="">
        <p>Instalación completada</p>
      </header>
      <div class="xl:p-5 p-3 flex flex-row items-center justify-start xl:gap-6 gap-3">
        <img src="${r.imagen || ''}" class="xl:size-[150px] size-[70px]" />
        <div class="flex flex-col items-start justify-start xl:gap-3 gap-1">
          <p class="uppercase font-bold xl:text-xl text-sm">${r.nombre || ''}</p>
          <p class="uppercase text-gray-400 font-bold xl:text-lg text-sm">Fecha: ${r.fecha || ''}</p>
          <p class="uppercase text-gray-400 font-bold xl:text-lg text-sm">${r.marca || ''}</p>
        </div>
      </div>
    </div>
  `).join("");
}

// Carga por defecto
loadOrders("mes");
</script>

</body>

</html>