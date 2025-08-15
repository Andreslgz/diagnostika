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
 * Muestra indicador de carga en el modal con la nueva UI
 */
function mostrarCargando() {
  const contenidoModal = document.getElementById(MODAL_CONFIG.contentId);
  if (!contenidoModal) return;

  // Limpiar header del modal
  const modalName = document.getElementById("modal_product_name");
  const modalBrand = document.getElementById("modal_product_brand");
  if (modalName) modalName.textContent = "Cargando producto...";
  if (modalBrand) modalBrand.textContent = "Obteniendo información";

  contenidoModal.innerHTML = `
    <div class="flex items-center justify-center h-96">
      <div class="text-center">
        <div class="relative">
          <div class="loading-spinner inline-block w-12 h-12 border-[3px] border-current border-t-transparent text-amber-500 rounded-full"></div>
          <div class="absolute inset-0 loading-spinner inline-block w-12 h-12 border-[3px] border-current border-b-transparent text-orange-400 rounded-full animate-reverse"></div>
        </div>
        <p class="mt-6 text-gray-600 font-medium text-lg">Cargando información del producto...</p>
        <p class="mt-2 text-gray-500 text-sm">Esto puede tomar unos segundos</p>
      </div>
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
 * Genera HTML para el carrusel de imágenes
 * @param {Array} imagenes - Array de URLs de imágenes
 * @param {string} nombreProducto - Nombre del producto para el alt
 * @returns {string} - HTML del carrusel
 */
function generarHTMLCarrusel(imagenes, nombreProducto) {
  if (!imagenes || imagenes.length === 0) {
    imagenes = [MODAL_CONFIG.placeholder];
  }

  const imagenesHTML = imagenes
    .map(
      (img, index) => `
    <div class="carousel-item ${
      index === 0 ? "active" : ""
    }" data-index="${index}">
      <img src="${img}" 
           alt="${nombreProducto} - Imagen ${index + 1}" 
           class="w-full h-96 object-cover rounded-xl"
           onerror="this.src='${MODAL_CONFIG.placeholder}';">
    </div>
  `
    )
    .join("");

  const indicadoresHTML =
    imagenes.length > 1
      ? imagenes
          .map(
            (_, index) => `
    <button class="carousel-indicator w-3 h-3 rounded-full ${
      index === 0 ? "bg-amber-500" : "bg-gray-300"
    } hover:bg-amber-400 transition-all duration-200" 
            data-index="${index}"></button>
  `
          )
          .join("")
      : "";

  return `
    <div class="relative group">
      <!-- Carrusel de imágenes -->
      <div class="carousel-container relative overflow-hidden rounded-xl">
        ${imagenesHTML}
      </div>
      
      ${
        imagenes.length > 1
          ? `
        <!-- Controles del carrusel -->
        <button class="carousel-prev absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </button>
        <button class="carousel-next absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </button>
        
        <!-- Indicadores -->
        <div class="flex justify-center gap-2 mt-4">
          ${indicadoresHTML}
        </div>
      `
          : ""
      }
    </div>
  `;
}

/**
 * Genera HTML para mostrar la información del producto con la nueva UI
 * @param {Object} producto - Datos del producto
 * @returns {string} - HTML generado
 */
function generarHTMLProducto(producto) {
  const imagenes = producto.imagen
    ? [`${BASE_DIR}/uploads/${producto.imagen}`]
    : [MODAL_CONFIG.placeholder];

  const precio = Number(producto.precio || 0).toFixed(2);
  const descripcionHTML = producto.descripcion || "Sin descripción disponible";

  // Actualizar el header del modal
  const modalName = document.getElementById("modal_product_name");
  const modalBrand = document.getElementById("modal_product_brand");
  if (modalName) modalName.textContent = producto.nombre;
  if (modalBrand) modalBrand.textContent = producto.marca || "Software";

  return `
    <div class="flex flex-col lg:flex-row">
      <!-- Sección de imágenes (lado izquierdo) -->
      <div class="lg:w-1/2 p-6 bg-gray-50">
        ${generarHTMLCarrusel(imagenes, producto.nombre)}
      </div>
      
      <!-- Sección de información (lado derecho) -->
      <div class="lg:w-1/2 p-6 flex flex-col">
        <!-- Precio prominente -->
        <div class="mb-6">
          <div class="flex items-baseline gap-3 mb-2">
            <span class="text-4xl font-bold text-gray-900">USD ${precio}</span>
            <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">Precio actual</span>
          </div>
          <p class="text-sm text-gray-600">Incluye licencia completa</p>
        </div>

        <!-- Controles de cantidad -->
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Cantidad</label>
          <div class="flex items-center border border-gray-300 rounded-lg w-32">
            <button type="button" class="quantity-decrease px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
              </svg>
            </button>
            <input type="number" id="product-quantity" value="1" min="1" max="10" 
                   class="flex-1 text-center border-0 focus:ring-0 py-2 text-gray-900 font-medium">
            <button type="button" class="quantity-increase px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Descripción técnica expandible -->
        <div class="mb-6">
          <button type="button" class="tech-description-toggle w-full flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
            <span class="font-medium text-gray-900">DESCRIPCIÓN TÉCNICA</span>
            <svg class="tech-description-arrow w-5 h-5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <div class="tech-description-content hidden mt-3 p-4 bg-white border border-gray-200 rounded-lg">
            <p class="text-gray-700 text-sm leading-relaxed">${descripcionHTML}</p>
            <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="font-medium text-gray-900">ID:</span>
                <span class="text-gray-600"> #${producto.id_producto}</span>
              </div>
              <div>
                <span class="font-medium text-gray-900">Estado:</span>
                <span class="text-green-600"> En stock</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Botones de acción -->
        <div class="mt-auto space-y-3">
          <form method="post" class="w-full">
            <input type="hidden" name="id_producto" value="${
              producto.id_producto
            }">
            <input type="hidden" id="hidden-quantity" name="cantidad" value="1">
            <button type="submit" 
                    name="agregar_carrito"
                    class="w-full btn-secondary hover:from-amber-500 hover:to-orange-600 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200 hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-4 focus:ring-amber-300">
              <span class="flex items-center justify-center gap-3">
            
                ADD TO CART
              </span>
            </button>
          </form>
          
          <button type="button" 
                  class="w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-gray-300">
            MORE DETAILS
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

    // Inicializar funcionalidades interactivas después de cargar el contenido
    inicializarInteractividad();
  } else {
    console.warn(`Producto con ID ${productId} no encontrado`);
    contenidoModal.innerHTML = generarHTMLError(productId);
  }
}

// ============================================
// FUNCIONALIDADES INTERACTIVAS
// ============================================

/**
 * Inicializa el carrusel de imágenes
 */
function inicializarCarrusel() {
  let currentSlide = 0;
  const items = document.querySelectorAll(".carousel-item");
  const indicators = document.querySelectorAll(".carousel-indicator");
  const prevBtn = document.querySelector(".carousel-prev");
  const nextBtn = document.querySelector(".carousel-next");

  if (items.length <= 1) return; // No hay carrusel si solo hay una imagen

  function showSlide(index) {
    // Ocultar todas las imágenes
    items.forEach((item) => item.classList.remove("active"));
    indicators.forEach((indicator) =>
      indicator.classList.remove("bg-amber-500")
    );
    indicators.forEach((indicator) => indicator.classList.add("bg-gray-300"));

    // Mostrar imagen actual
    if (items[index]) {
      items[index].classList.add("active");
      if (indicators[index]) {
        indicators[index].classList.remove("bg-gray-300");
        indicators[index].classList.add("bg-amber-500");
      }
    }
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % items.length;
    showSlide(currentSlide);
  }

  function prevSlide() {
    currentSlide = (currentSlide - 1 + items.length) % items.length;
    showSlide(currentSlide);
  }

  // Event listeners
  if (nextBtn) nextBtn.addEventListener("click", nextSlide);
  if (prevBtn) prevBtn.addEventListener("click", prevSlide);

  // Indicadores
  indicators.forEach((indicator, index) => {
    indicator.addEventListener("click", () => {
      currentSlide = index;
      showSlide(currentSlide);
    });
  });

  // Auto-avance del carrusel (opcional)
  let autoSlide = setInterval(nextSlide, 5000);

  // Pausar auto-avance al hacer hover
  const carousel = document.querySelector(".carousel-container");
  if (carousel) {
    carousel.addEventListener("mouseenter", () => clearInterval(autoSlide));
    carousel.addEventListener("mouseleave", () => {
      autoSlide = setInterval(nextSlide, 5000);
    });
  }
}

/**
 * Inicializa los controles de cantidad
 */
function inicializarControlesCantidad() {
  const quantityInput = document.getElementById("product-quantity");
  const hiddenQuantity = document.getElementById("hidden-quantity");
  const decreaseBtn = document.querySelector(".quantity-decrease");
  const increaseBtn = document.querySelector(".quantity-increase");

  if (!quantityInput || !hiddenQuantity) return;

  function updateQuantity(value) {
    const newValue = Math.max(1, Math.min(10, parseInt(value) || 1));
    quantityInput.value = newValue;
    hiddenQuantity.value = newValue;
  }

  // Botón disminuir
  if (decreaseBtn) {
    decreaseBtn.addEventListener("click", () => {
      updateQuantity(parseInt(quantityInput.value) - 1);
    });
  }

  // Botón aumentar
  if (increaseBtn) {
    increaseBtn.addEventListener("click", () => {
      updateQuantity(parseInt(quantityInput.value) + 1);
    });
  }

  // Input directo
  quantityInput.addEventListener("input", (e) => {
    updateQuantity(e.target.value);
  });

  // Prevenir valores inválidos
  quantityInput.addEventListener("blur", (e) => {
    updateQuantity(e.target.value);
  });
}

/**
 * Inicializa la descripción técnica expandible
 */
function inicializarDescripcionExpandible() {
  const toggle = document.querySelector(".tech-description-toggle");
  const content = document.querySelector(".tech-description-content");
  const arrow = document.querySelector(".tech-description-arrow");

  if (!toggle || !content || !arrow) return;

  toggle.addEventListener("click", () => {
    const isHidden = content.classList.contains("hidden");

    if (isHidden) {
      content.classList.remove("hidden");
      arrow.classList.add("rotate-180");

      // Animación suave
      content.style.maxHeight = "0px";
      content.style.overflow = "hidden";
      setTimeout(() => {
        content.style.maxHeight = content.scrollHeight + "px";
        content.style.transition = "max-height 0.3s ease-out";
      }, 10);
    } else {
      content.style.maxHeight = "0px";
      arrow.classList.remove("rotate-180");

      setTimeout(() => {
        content.classList.add("hidden");
        content.style.maxHeight = "";
        content.style.transition = "";
        content.style.overflow = "";
      }, 300);
    }
  });
}

/**
 * Inicializa todas las funcionalidades interactivas del modal
 */
function inicializarInteractividad() {
  // Pequeño delay para asegurar que el DOM esté renderizado
  setTimeout(() => {
    inicializarCarrusel();
    inicializarControlesCantidad();
    inicializarDescripcionExpandible();
  }, 100);
}

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
