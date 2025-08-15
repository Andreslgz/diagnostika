<!-- Modal Previsualizar Producto con Splide.js -->
<div id="modal_previsualizar" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-6xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Modal header con gradiente -->
            <div class="btn-secondary p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 id="modal_product_name" class="text-2xl font-bold text-white">
                            Nombre del Producto
                        </h3>
                        <p id="modal_product_brand" class="text-amber-100 text-sm mt-1">
                            Marca del producto
                        </p>
                    </div>
                    <button type="button"
                        class="text-white hover:bg-white/20 rounded-full text-sm w-10 h-10 inline-flex justify-center items-center transition-all duration-200"
                        data-modal-hide="modal_previsualizar">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Cerrar modal</span>
                    </button>
                </div>
            </div>

            <!-- Modal body -->
            <div class="p-0" id="previsualizar_contenido">
                <!-- Aquí se cargará el contenido dinámicamente -->
                <div class="flex items-center justify-center h-64">
                    <div class="text-center">
                        <div
                            class="animate-spin inline-block w-8 h-8 border-[3px] border-current border-t-transparent text-amber-500 rounded-full">
                        </div>
                        <p class="mt-4 text-gray-600">Cargando información del producto...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>