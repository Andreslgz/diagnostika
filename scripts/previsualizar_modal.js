/**
 * MODAL PREVISUALIZAR PRODUCTO
 * Funcionalidad para mostrar información detallada de productos en modal
 */

// ============================================
// CONFIGURACIÓN Y UTILIDADES
// ============================================

const MODAL_CONFIG = {
  modalId: "modal_previsualizar",
  contentId: "previsualizar_contenido",
  animationDuration: 300,
  loadDelay: 500,
  placeholder: "https://placehold.co/600x400/png",
};

// ============================================
// FUNCIONES DE MODAL
// ============================================

/**
 * Abre el modal de previsualización con animaciones
 */
function abrirModalPrevisualizar() {
  const modal = document.getElementById(MODAL_CONFIG.modalId);
  if (!modal) {
    console.error("Modal de previsualización no encontrado");
    return false;
  }

  // Remover la clase 'hidden' y agregar las clases necesarias para mostrar el modal
  modal.classList.remove("hidden");
  modal.classList.add("flex");

  // Agregar fondo oscuro (backdrop)
  modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)";

  // Agregar clase de transición suave
  modal.style.opacity = "0";
  modal.style.transition = `opacity ${MODAL_CONFIG.animationDuration}ms ease`;

  // Hacer visible con animación
  setTimeout(() => {
    modal.style.opacity = "1";
  }, 10);

  return true;
}

/**
 * Cierra el modal de previsualización con animaciones
 */
function cerrarModalPrevisualizar() {
  const modal = document.getElementById(MODAL_CONFIG.modalId);
  if (!modal) return;

  modal.style.opacity = "0";
  setTimeout(() => {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
    modal.style.backgroundColor = "";
  }, MODAL_CONFIG.animationDuration);
}

/**
 * Muestra indicador de carga en el modal
 */
function mostrarCargando() {
  const contenidoModal = document.getElementById(MODAL_CONFIG.contentId);
  if (!contenidoModal) return;

  contenidoModal.innerHTML = `
    <div class="text-center py-8">
      <div class="animate-spin inline-block w-8 h-8 border-[3px] border-current border-t-transparent text-blue-600 rounded-full" role="status" aria-label="loading">
        <span class="sr-only">Cargando...</span>
      </div>
      <p class="mt-4 text-gray-600 font-medium">Cargando información del producto...</p>
    </div>
  `;
}

// ============================================
// FUNCIONES DE DATOS
// ============================================

/**
 * Busca un producto en los datos cargados
 * @param {string|number} productId - ID del producto a buscar
 * @returns {Object|null} - Producto encontrado o null
 */
function buscarProducto(productId) {
  if (!window.__productosCrudos || !Array.isArray(window.__productosCrudos)) {
    console.warn("No hay datos de productos disponibles");
    return null;
  }

  return (
    window.__productosCrudos.find((p) => p.id_producto == productId) || null
  );
}

/**
 * Genera HTML para mostrar la información del producto
 * @param {Object} producto - Datos del producto
 * @returns {string} - HTML generado
 */
function generarHTMLProducto(producto) {
  const imagenUrl = producto.imagen
    ? `${BASE_DIR}/uploads/${producto.imagen}`
    : MODAL_CONFIG.placeholder;

  const precio = Number(producto.precio || 0).toFixed(2);
  const descripcionHTML = producto.descripcion
    ? `<p class="text-gray-600 text-sm leading-relaxed">${producto.descripcion}</p>`
    : '<p class="text-gray-500 text-sm italic">Sin descripción disponible</p>';

  return `
    <div class="space-y-6">
      <!-- Información principal del producto -->
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Imagen del producto -->
        <div class="md:w-1/2">
          <div class="relative group">
            <img src="${imagenUrl}" 
                 alt="${producto.nombre}" 
                 class="w-full h-64 md:h-72 object-cover rounded-lg shadow-md transition-transform duration-300 group-hover:scale-105"
                 onerror="this.src='${MODAL_CONFIG.placeholder}';">
            <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
          </div>
        </div>
        
        <!-- Información del producto -->
        <div class="md:w-1/2 space-y-4">
          <div>
            <h4 class="text-xl md:text-2xl font-bold text-gray-900 mb-2">${producto.nombre}</h4>
            <div class="flex items-center gap-2 mb-3">
              <span class="text-3xl font-bold text-amber-600">USD ${precio}</span>
              <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Precio actual</span>
            </div>
          </div>
          
          <!-- Descripción -->
          <div class="border-t border-gray-200 pt-4">
            <h5 class="text-sm font-semibold text-gray-700 mb-2">Descripción</h5>
            ${descripcionHTML}
          </div>
          
          <!-- Información adicional -->
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="bg-gray-50 p-3 rounded-lg">
              <span class="text-gray-600">ID del producto</span>
              <p class="font-semibold text-gray-900">#${producto.id_producto}</p>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg">
              <span class="text-gray-600">Disponibilidad</span>
              <p class="font-semibold text-green-600">En stock</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Acciones del producto -->
      <div class="border-t border-gray-200 pt-6">
        <div class="flex flex-col sm:flex-row gap-3">
          <form method="post" class="flex-1">
            <input type="hidden" name="id_producto" value="${producto.id_producto}">
            <button type="submit" 
                    name="agregar_carrito"
                    class="w-full btn-secondary py-3 rounded-lg font-semibold text-base transition-all duration-200 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-4 focus:ring-amber-300">
              <span class="flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l1.5-6M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                </svg>
                Agregar al carrito
              </span>
            </button>
          </form>
          
          <button type="button" 
                  onclick="cerrarModalPrevisualizar()"
                  class="sm:w-auto px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-gray-200">
            Cerrar
          </button>
        </div>
      </div>
    </div>
  `;
}

/**
 * Genera HTML para mostrar error cuando no se encuentra el producto
 * @param {string|number} productId - ID del producto que no se encontró
 * @returns {string} - HTML de error
 */
function generarHTMLError(productId) {
  return `
    <div class="text-center py-12">
      <div class="text-gray-400 text-6xl mb-6">⚠️</div>
      <h4 class="text-xl font-semibold text-gray-900 mb-2">Producto no encontrado</h4>
      <p class="text-gray-600 mb-4">No se pudo cargar la información del producto solicitado.</p>
      <div class="bg-gray-100 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-500">
          <span class="font-medium">ID del producto:</span> ${productId}
        </p>
      </div>
      <button type="button" 
              onclick="cerrarModalPrevisualizar()"
              class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
        Cerrar
      </button>
    </div>
  `;
}

/**
 * Carga y muestra los datos del producto en el modal
 * @param {string|number} productId - ID del producto a cargar
 */
function cargarDatosProducto(productId) {
  const contenidoModal = document.getElementById(MODAL_CONFIG.contentId);
  if (!contenidoModal) {
    console.error("Contenedor del modal no encontrado");
    return;
  }

  console.log(`Cargando datos del producto: ${productId}`);

  // Buscar el producto
  const producto = buscarProducto(productId);

  // Generar y mostrar el contenido
  if (producto) {
    console.log("Producto encontrado:", producto.nombre);
    contenidoModal.innerHTML = generarHTMLProducto(producto);
  } else {
    console.warn(`Producto con ID ${productId} no encontrado`);
    contenidoModal.innerHTML = generarHTMLError(productId);
  }
}

// ============================================
// EVENT LISTENERS
// ============================================

/**
 * Maneja el click en botones de previsualizar
 * @param {Event} ev - Evento de click
 */
function manejarClickPrevisualizar(ev) {
  const btn = ev.target.closest(".preview");
  if (!btn) return;

  ev.preventDefault();
  ev.stopPropagation();

  const productId = btn.getAttribute("data-id");
  if (!productId) {
    console.error("ID de producto no encontrado en el botón");
    return;
  }

  console.log(`Iniciando previsualización del producto: ${productId}`);

  // Abrir modal
  if (!abrirModalPrevisualizar()) {
    console.error("No se pudo abrir el modal");
    return;
  }

  // Mostrar indicador de carga
  mostrarCargando();

  // Cargar datos del producto con delay para mostrar el loading
  setTimeout(() => {
    cargarDatosProducto(productId);
  }, MODAL_CONFIG.loadDelay);
}

/**
 * Maneja el cierre del modal mediante diferentes métodos
 * @param {Event} ev - Evento que activa el cierre
 */
function manejarCierreModal(ev) {
  // Cerrar con botón de cerrar
  if (ev.target.closest('[data-modal-hide="modal_previsualizar"]')) {
    ev.preventDefault();
    cerrarModalPrevisualizar();
    return;
  }

  // Cerrar al hacer click fuera del modal (en el backdrop)
  const modal = document.getElementById(MODAL_CONFIG.modalId);
  if (ev.target === modal) {
    cerrarModalPrevisualizar();
  }
}

/**
 * Maneja el cierre del modal con tecla Escape
 * @param {KeyboardEvent} ev - Evento de teclado
 */
function manejarTeclasModal(ev) {
  if (ev.key === "Escape") {
    const modal = document.getElementById(MODAL_CONFIG.modalId);
    if (modal && !modal.classList.contains("hidden")) {
      ev.preventDefault();
      cerrarModalPrevisualizar();
    }
  }
}

// ============================================
// INICIALIZACIÓN
// ============================================

/**
 * Inicializa la funcionalidad del modal de previsualización
 */
function inicializarModalPrevisualizar() {
  console.log("Inicializando modal de previsualización...");

  // Event listeners para clicks (delegación de eventos)
  document.addEventListener("click", manejarClickPrevisualizar);
  document.addEventListener("click", manejarCierreModal);

  // Event listener para teclas
  document.addEventListener("keydown", manejarTeclasModal);

  // Verificar que el modal existe
  const modal = document.getElementById(MODAL_CONFIG.modalId);
  const contenido = document.getElementById(MODAL_CONFIG.contentId);

  if (!modal) {
    console.warn("Modal de previsualización no encontrado en el DOM");
    return false;
  }

  if (!contenido) {
    console.warn("Contenedor de contenido del modal no encontrado");
    return false;
  }

  console.log("Modal de previsualización inicializado correctamente");
  return true;
}

// ============================================
// UTILIDADES GLOBALES
// ============================================

// Exponer funciones necesarias globalmente
window.ModalPrevisualizar = {
  abrir: abrirModalPrevisualizar,
  cerrar: cerrarModalPrevisualizar,
  cargarProducto: cargarDatosProducto,
  inicializar: inicializarModalPrevisualizar,

  // Funciones de utilidad para debugging
  debug: {
    verificarModal: () => {
      const modal = document.getElementById(MODAL_CONFIG.modalId);
      const contenido = document.getElementById(MODAL_CONFIG.contentId);

      return {
        modalExiste: !!modal,
        contenidoExiste: !!contenido,
        modalVisible: modal && !modal.classList.contains("hidden"),
        productosDisponibles: window.__productosCrudos?.length || 0,
      };
    },

    probarConProducto: (productId) => {
      console.log(`Probando modal con producto ID: ${productId}`);
      abrirModalPrevisualizar();
      setTimeout(() => cargarDatosProducto(productId), 100);
    },
  },
};

// Auto-inicialización cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
  // Pequeño delay para asegurar que otros scripts estén cargados
  setTimeout(inicializarModalPrevisualizar, 100);
});

// También inicializar si el script se carga después del DOMContentLoaded
if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", inicializarModalPrevisualizar);
} else {
  setTimeout(inicializarModalPrevisualizar, 100);
}
