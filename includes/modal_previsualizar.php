<!-- MODAL PREVISUALIZAR PRODUCTO | ACTUALIZADO -->
<div id="modal_previsualizar" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm">
            <!-- Modal header -->
            <div class="p-4 md:p-5 border-b rounded-t btn-secondary border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 id="modal_product_name" class="xl:text-xl text-sm font-semibold">
                        NOMBRE DEL PRODUCTO
                    </h3>
                    <button type="button"
                        class="text-white bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="modal_previsualizar">
                        <svg class="xl:w-4 w-2.5 xl:h-4 h-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <p id="modal_product_brand" class="text-white xl:text-lg text-sm xl:mt-8 mt-3">
                    MARCA
                </p>
            </div>
            
            <!-- Modal body -->
            <div id="previsualizar_contenido" class="xl:p-8 p-5 grid xl:grid-cols-2 grid-cols-1 xl:gap-10 gap-4 w-full">
                
                <!-- Sección de imágenes (lado izquierdo) -->
                <div class="max-w-2xl mx-auto">
                    <section aria-label="Galería de imágenes">
                        <!-- Imagen principal -->
                        <div class="relative mb-4 overflow-hidden border border-gray-200 bg-white">
                            <div class="aspect-square">
                                <img id="mainImage"
                                    src="https://placehold.co/600x600/png"
                                    alt="Imagen principal"
                                    class="h-full w-full object-cover transition-opacity duration-300"
                                    loading="eager" decoding="async" draggable="false" />
                            </div>
                        </div>

                        <!-- Carrusel de miniaturas -->
                        <div class="relative carousel-thumbnails-container">
                            <!-- Gradientes laterales para indicar overflow -->
                            <div class="pointer-events-none absolute inset-y-0 left-0 w-10 bg-gradient-to-r from-gray-50 to-transparent"></div>
                            <div class="pointer-events-none absolute inset-y-0 right-0 w-10 bg-gradient-to-l from-gray-50 to-transparent"></div>

                            <div class="flex items-center gap-2">
                                <!-- Flecha izquierda -->
                                <button id="prev"
                                    class="carousel-prev shrink-0 rounded-md bg-gray-800 text-white px-3 py-2 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                    aria-label="Desplazar miniaturas a la izquierda"
                                    title="Anterior">&#10094;</button>

                                <!-- Contenedor de miniaturas -->
                                <div id="thumbs"
                                    class="relative flex gap-2 overflow-x-auto scrollbar-hide scroll-smooth snap-x snap-mandatory py-2"
                                    role="listbox" aria-label="Miniaturas">
                                    <!-- Las miniaturas se generarán dinámicamente aquí -->
                                </div>

                                <!-- Flecha derecha -->
                                <button id="next"
                                    class="carousel-next shrink-0 rounded-md bg-gray-800 text-white px-3 py-2 disabled:opacity-40 disabled:cursor-not-allowed hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                    aria-label="Desplazar miniaturas a la derecha"
                                    title="Siguiente">&#10095;</button>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Sección de información (lado derecho) -->
                <div class="w-full flex flex-col justify-between">
                    <div>
                        <!-- Precio y cantidad -->
                        <div class="flex flex-row justify-between items-center w-full">
                            <p id="modal_product_price" class="xl:text-3xl text-lg text-nowrap font-bold">
                                USD 0.00
                            </p>

                            <div class="relative mt-2 flex max-w-32 items-center justify-end">
                                <button type="button" id="decrement-button"
                                    class="quantity-decrease xl:h-10 h-8 rounded-s-lg border border-gray-300 bg-gray-100 xl:p-3 p-2 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                    <svg class="h-3 w-3 text-gray-900" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M1 1h16" />
                                    </svg>
                                </button>
                                <input type="text" id="product-quantity"
                                    data-input-counter data-input-counter-min="1" data-input-counter-max="50"
                                    aria-describedby="helper-text-explanation"
                                    class="block xl:h-10 h-8 w-full border-x-0 border-gray-300 bg-gray-50 py-2.5 text-center text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
                                    placeholder="1" value="1" required />
                                <button type="button" id="increment-button"
                                    class="quantity-increase xl:h-10 h-8 rounded-e-lg border border-gray-300 bg-gray-100 xl:p-3 p-2 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
                                    <svg class="h-3 w-3 text-gray-900" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M9 1v16M1 9h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Descripción del producto -->
                        <textarea rows="8" name="product-description" id="product-description"
                            class="mt-4 block w-full border border-gray-300 rounded-lg p-2"
                            placeholder="Enter product description..." readonly>Descripción del producto</textarea>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col gap-4 xl:mt-0 mt-4">
                        <form method="post" class="w-full">
                            <input type="hidden" id="modal_product_id" name="id_producto" value="">
                            <input type="hidden" id="hidden-quantity" name="cantidad" value="1">
                            <button type="submit" name="agregar_carrito"
                                class="btn-secondary w-full py-3 font-bold text-base xl:text-lg shadow xl:shadow-lg rounded-lg">
                                ADD TO CART
                            </button>
                        </form>
                        <a href="#" id="modal_more_details"
                            class="block text-center btn-primary w-full py-3 font-bold text-base xl:text-lg shadow xl:shadow-lg rounded-lg">
                            MORE DETAILS
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales necesarios -->
<style>
    /* Ocultar scrollbar pero mantener funcionalidad */
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* Animación de carga */
    .loading-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .animate-reverse {
        animation-direction: reverse;
    }

    /* Transiciones suaves para el modal */
    #modal_previsualizar {
        transition: opacity 300ms ease;
    }

    /* Estilos para las miniaturas activas */
    .thumb.active {
        border-color: rgb(251 146 60);
    }

    /* Asegurar que el textarea de descripción no sea redimensionable */
    #product-description {
        resize: none;
    }
</style>