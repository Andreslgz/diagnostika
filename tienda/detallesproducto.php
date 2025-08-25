<?php
session_start();
require_once __DIR__ . '/../includes/db.php';



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

        <section class="py-20 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <!-- Galeria de imagenes  -->
                    <div class="relative">
                        <!-- Imagen principal -->
                        <div class="relative group cursor-pointer" id="mainImageContainer">
                            <img id="mainImage" src="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-1.png"
                                alt="Imagen principal del producto"
                                class="w-full h-auto rounded-lg shadow-lg transition-transform duration-300 hover:scale-105">
                            <!-- Overlay con icono de zoom -->
                            <div
                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 rounded-lg flex items-center justify-center">
                                <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7">
                                    </path>
                                </svg>
                            </div>
                        </div>

                        <!-- Navegación de miniaturas -->
                        <div class="mt-6 relative">
                            <!-- Botón scroll izquierda -->
                            <button id="scrollLeft"
                                class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>

                            <!-- Contenedor de miniaturas con scroll horizontal -->
                            <div id="thumbnailContainer"
                                class="flex gap-4 overflow-x-auto scrollbar-hide px-8 scroll-smooth">
                                <img src="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-1.png"
                                    class="thumbnail w-[120px] h-[120px] object-cover border-2 border-blue-500 rounded-lg cursor-pointer  flex-shrink-0"
                                    data-full="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-1.png"
                                    alt="Vista 1">
                                <img src="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-2.png"
                                    class="thumbnail w-[120px] h-[120px] object-cover border-2 border-gray-300 rounded-lg cursor-pointer  flex-shrink-0"
                                    data-full="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-2.png"
                                    alt="Vista 2">
                                <img src="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-3.png"
                                    class="thumbnail w-[120px] h-[120px] object-cover border-2 border-gray-300 rounded-lg cursor-pointer  flex-shrink-0"
                                    data-full="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-3.png"
                                    alt="Vista 3">
                                <img src="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-4.png"
                                    class="thumbnail w-[120px] h-[120px] object-cover border-2 border-gray-300 rounded-lg cursor-pointer  flex-shrink-0"
                                    data-full="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-4.png"
                                    alt="Vista 4">
                                <img src="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-5.png"
                                    class="thumbnail w-[120px] h-[120px] object-cover border-2 border-gray-300 rounded-lg cursor-pointer  flex-shrink-0"
                                    data-full="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-5.png"
                                    alt="Vista 5">
                                <img src="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-6.png"
                                    class="thumbnail w-[120px] h-[120px] object-cover border-2 border-gray-300 rounded-lg cursor-pointer  flex-shrink-0"
                                    data-full="/assets/images/producto1ejemplo/BENDIX_ACOM_PRO_2024-6.png"
                                    alt="Vista 6">
                            </div>

                            <!-- Botón scroll derecha -->
                            <button id="scrollRight"
                                class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-bold">
                        CUMMINS INSITE 9.0 ENGINERRING + FILES + SPDT + FILES + LEGACY VERSION
                    </h2>

                    <div class="max-w-xs mt-2">
                        <select id="countries"
                            class="btn-primary border border-gray-100 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <option selected>Others versions</option>
                            <option value="v1">Version 1</option>
                            <option value="v2">Version 2</option>
                            <option value="v3">Version 3</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 my-6">
                        <div>
                            <div class="flex flex-row gap-2 items-center justify-start">
                                <img src="/assets/icons/detalleproducto/brand.svg" alt="">
                                <div class="flex flex-row gap-6">
                                    <p class="text-gray-500 uppercase">Brand:</p>
                                    <p>
                                        CUMMINS
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-row gap-2 items-center justify-start">
                                <img src="/assets/icons/detalleproducto/type.svg" alt="">
                                <div class="flex flex-row gap-6">
                                    <p class="text-gray-500 uppercase">Type:</p>
                                    <p>
                                        DIAGNOSTIC
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-row gap-2 items-center justify-start">
                                <img src="/assets/icons/detalleproducto/system.svg" alt="">
                                <div class="flex flex-row gap-6">
                                    <p class="text-gray-500 uppercase">system:</p>
                                    <p>
                                        ENGINE
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-row gap-2 items-center justify-start">
                                <img src="/assets/icons/detalleproducto/windows.svg" alt="">
                                <div class="flex flex-row gap-6">
                                    <p class="text-gray-500 uppercase">windows:</p>
                                    <p>
                                        10 / 64 bits
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="flex flex-row gap-2 items-center justify-start">
                                <img src="/assets/icons/detalleproducto/size.svg" alt="">
                                <div class="flex flex-row gap-6">
                                    <p class="text-gray-500 uppercase">size:</p>
                                    <p>
                                        3.17 GB
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-row gap-2 items-center justify-start">
                                <img src="/assets/icons/detalleproducto/ram.svg" alt="">
                                <div class="flex flex-row gap-6">
                                    <p class="text-gray-500 uppercase">ram:</p>
                                    <p>
                                        8 - 16 GB
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-row gap-2 items-center justify-start">
                                <img src="/assets/icons/detalleproducto/storage.svg" alt="">
                                <div class="flex flex-row gap-6">
                                    <p class="text-gray-500 uppercase">storage:</p>
                                    <p>
                                        90 GB
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="flex flex-row items-center justify-between w-full">
                        <p class="text-4xl font-bold">
                            USD 50.00
                        </p>
                        <div class="max-w-xs ">
                            <div class="relative flex items-center max-w-[8rem]">
                                <button type="button" id="decrement-button"
                                    data-input-counter-decrement="quantity-input"
                                    class="bg-gray-50  hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100  focus:ring-2 focus:outline-none">
                                    <svg class="w-3 h-3 text-gray-900 " aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M1 1h16" />
                                    </svg>
                                </button>
                                <input type="text" id="quantity-input" data-input-counter data-input-counter-min="1"
                                    data-input-counter-max="50" aria-describedby="helper-text-explanation"
                                    class="bg-gray-50  border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5  "
                                    placeholder="999" value="5" required />
                                <button type="button" id="increment-button"
                                    data-input-counter-increment="quantity-input"
                                    class="bg-gray-100   hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100  focus:ring-2 focus:outline-none">
                                    <svg class="w-3 h-3 text-gray-900 " aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M9 1v16M1 9h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row items-center justify-between mt-4 gap-4">
                        <button
                            class="btn-secondary w-full shadow-lg rounded-lg xl:py-4 py-2 font-bold xl:text-lg text-base">
                            BUY NOW!
                        </button>
                        <button
                            class="btn-primary w-full shadow-lg rounded-lg xl:py-4 py-2 font-bold xl:text-lg text-base">
                            ADD TO CART
                        </button>
                    </div>

                    <div id="accordion-collapse" data-accordion="collapse" class="mt-6"
                        data-active-classes="bg-gray-50 text-gray-900">
                        <h2 id="accordion-collapse-heading-1">
                            <button type="button"
                                class="flex items-center justify-between w-full p-5 font-medium text-gray-500 border border border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 gap-3"
                                data-accordion-target="#accordion-collapse-body-1" aria-expanded="true"
                                aria-controls="accordion-collapse-body-1">
                                <span class="uppercase">Technical description</span>
                                <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M9 5 5 1 1 5" />
                                </svg>
                            </button>
                        </h2>
                        <div id="accordion-collapse-body-1" class="hidden"
                            aria-labelledby="accordion-collapse-heading-1">
                            <div class="p-5 border border-gray-200 ">
                                <p class="mb-2 text-gray-500 ">Diagnostics software designed for diagnosis and
                                    programming Volvo Trucks, Volvo buss, Volvo Construction Equipment, and New Models
                                    of Mack, Renault, and Nissan UD.</p>

                            </div>
                        </div>


                    </div>


                </div>
            </div>
        </section>

        <!-- Lightbox Modal -->
        <div id="lightboxModal"
            class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
            <!-- Botón cerrar -->
            <button id="closeLightbox"
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors duration-200 z-60">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <!-- Navegación anterior -->
            <button id="prevImage"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors duration-200 z-60">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <!-- Imagen del lightbox -->
            <div class="max-w-5xl max-h-full flex items-center justify-center">
                <img id="lightboxImage" src="" alt="" class="max-w-full max-h-full object-contain">
            </div>

            <!-- Navegación siguiente -->
            <button id="nextImage"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-gray-300 transition-colors duration-200 z-60">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Indicador de imagen actual -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 text-white text-lg">
                <span id="currentImageIndex">1</span> / <span id="totalImages">6</span>
            </div>

            <!-- Miniaturas en lightbox -->
            <div class="absolute bottom-16 left-1/2 transform -translate-x-1/2 flex gap-2">
                <div id="lightboxThumbnails" class="flex gap-2"></div>
            </div>
        </div>

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



    <!-- SCRIPTS en este orden -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>

    <!-- Splide.js DEBE ir antes del modal script -->
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet" />

    <script src="<?php echo $url; ?>/scripts/main.js"></script>
    <script src="<?php echo $url; ?>/scripts/previsualizar_modal.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <script>
        // GALERIA DE IMAGENES SCRIPT
        document.addEventListener('DOMContentLoaded', function () {
            // Variables globales
            const mainImage = document.getElementById('mainImage');
            const thumbnails = document.querySelectorAll('.thumbnail');
            const thumbnailContainer = document.getElementById('thumbnailContainer');
            const scrollLeftBtn = document.getElementById('scrollLeft');
            const scrollRightBtn = document.getElementById('scrollRight');
            const mainImageContainer = document.getElementById('mainImageContainer');

            // Lightbox elementos
            const lightboxModal = document.getElementById('lightboxModal');
            const lightboxImage = document.getElementById('lightboxImage');
            const closeLightbox = document.getElementById('closeLightbox');
            const prevImage = document.getElementById('prevImage');
            const nextImage = document.getElementById('nextImage');
            const currentImageIndex = document.getElementById('currentImageIndex');
            const totalImages = document.getElementById('totalImages');
            const lightboxThumbnails = document.getElementById('lightboxThumbnails');

            let currentIndex = 0;
            const images = Array.from(thumbnails).map(thumb => thumb.dataset.full);

            // Inicializar
            totalImages.textContent = images.length;
            createLightboxThumbnails();

            // Función para cambiar imagen principal
            function changeMainImage(src, index) {
                mainImage.src = src;
                currentIndex = index;

                // Actualizar bordes de miniaturas
                thumbnails.forEach((thumb, i) => {
                    if (i === index) {
                        thumb.classList.remove('border-gray-300');
                        thumb.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');
                    } else {
                        thumb.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
                        thumb.classList.add('border-gray-300');
                    }
                });
            }

            // Crear miniaturas para lightbox
            function createLightboxThumbnails() {
                lightboxThumbnails.innerHTML = '';
                images.forEach((src, index) => {
                    const thumb = document.createElement('img');
                    thumb.src = src;
                    thumb.className = 'w-12 h-12 object-cover rounded cursor-pointer border-2 border-transparent hover:border-white transition-all duration-200';
                    thumb.addEventListener('click', () => {
                        changeLightboxImage(index);
                    });
                    lightboxThumbnails.appendChild(thumb);
                });
            }

            // Función para cambiar imagen en lightbox
            function changeLightboxImage(index) {
                currentIndex = index;
                lightboxImage.src = images[index];
                currentImageIndex.textContent = index + 1;

                // Actualizar miniaturas del lightbox
                const lightboxThumbs = lightboxThumbnails.querySelectorAll('img');
                lightboxThumbs.forEach((thumb, i) => {
                    if (i === index) {
                        thumb.classList.add('border-white', 'opacity-100');
                        thumb.classList.remove('border-transparent', 'opacity-70');
                    } else {
                        thumb.classList.remove('border-white', 'opacity-100');
                        thumb.classList.add('border-transparent', 'opacity-70');
                    }
                });
            }

            // Event listeners para miniaturas
            thumbnails.forEach((thumbnail, index) => {
                thumbnail.addEventListener('click', () => {
                    changeMainImage(thumbnail.dataset.full, index);
                });
            });

            // Scroll de miniaturas
            scrollLeftBtn.addEventListener('click', () => {
                thumbnailContainer.scrollBy({
                    left: -200,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', () => {
                thumbnailContainer.scrollBy({
                    left: 200,
                    behavior: 'smooth'
                });
            });

            // Abrir lightbox
            mainImageContainer.addEventListener('click', () => {
                lightboxModal.classList.remove('hidden');
                lightboxImage.src = mainImage.src;
                currentImageIndex.textContent = currentIndex + 1;
                changeLightboxImage(currentIndex);
                document.body.style.overflow = 'hidden';
            });

            // Cerrar lightbox
            function closeLightboxModal() {
                lightboxModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            closeLightbox.addEventListener('click', closeLightboxModal);

            // Cerrar lightbox al hacer clic en el fondo
            lightboxModal.addEventListener('click', (e) => {
                if (e.target === lightboxModal) {
                    closeLightboxModal();
                }
            });

            // Navegación en lightbox
            prevImage.addEventListener('click', () => {
                const newIndex = currentIndex === 0 ? images.length - 1 : currentIndex - 1;
                changeLightboxImage(newIndex);
            });

            nextImage.addEventListener('click', () => {
                const newIndex = currentIndex === images.length - 1 ? 0 : currentIndex + 1;
                changeLightboxImage(newIndex);
            });

            // Navegación con teclado
            document.addEventListener('keydown', (e) => {
                if (!lightboxModal.classList.contains('hidden')) {
                    switch (e.key) {
                        case 'Escape':
                            closeLightboxModal();
                            break;
                        case 'ArrowLeft':
                            prevImage.click();
                            break;
                        case 'ArrowRight':
                            nextImage.click();
                            break;
                    }
                }
            });

            // Mostrar/ocultar botones de scroll según sea necesario
            function updateScrollButtons() {
                const isScrollable = thumbnailContainer.scrollWidth > thumbnailContainer.clientWidth;
                scrollLeftBtn.style.display = isScrollable ? 'block' : 'none';
                scrollRightBtn.style.display = isScrollable ? 'block' : 'none';
            }

            // Actualizar botones al redimensionar
            window.addEventListener('resize', updateScrollButtons);
            updateScrollButtons();

            // Touch/swipe support para móviles
            let startX = 0;
            let endX = 0;

            lightboxModal.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
            });

            lightboxModal.addEventListener('touchend', (e) => {
                endX = e.changedTouches[0].clientX;
                handleSwipe();
            });

            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = startX - endX;

                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0) {
                        // Swipe izquierda - siguiente imagen
                        nextImage.click();
                    } else {
                        // Swipe derecha - imagen anterior
                        prevImage.click();
                    }
                }
            }
        });
    </script>
    <style>
        /* GALERIA DE IMAGENES ESTILOS */
        .scrollbar-hide {
            -ms-overflow-style: none;
            /* Internet Explorer 10+ */
            scrollbar-width: none;
            /* Firefox */
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
            /* Safari and Chrome */
        }



        /* Efectos de lightbox */
        #lightboxModal {
            backdrop-filter: blur(4px);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        #lightboxImage {
            animation: zoomIn 0.3s ease-out;
        }

        @keyframes zoomIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Botones de navegación */
        .nav-button {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .nav-button:hover {
            background: rgba(255, 255, 255, 1);
            transform: scale(1.1);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .thumbnail {
                width: 80px !important;
                height: 80px !important;
            }

            #lightboxModal .absolute.left-4,
            #lightboxModal .absolute.right-4 {
                left: 1rem;
                right: 1rem;
            }

            #lightboxModal svg {
                width: 2rem;
                height: 2rem;
            }
        }

        /* Indicadores de carga */
        .loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>

</body>

</html>