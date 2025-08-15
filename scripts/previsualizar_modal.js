/**
 * MODAL PREVISUALIZAR PRODUCTO - VERSIÓN ACTUALIZADA
 * Compatible con la nueva estructura HTML del modal
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

  // Remover la clase 'hidden' y agregar las clases necesarias
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

  // Actualizar header del modal
  const modalName = document.getElementById("modal_product_name");
  const modalBrand = document.getElementById("modal_product_brand");
  if (modalName) modalName.textContent = "CARGANDO PRODUCTO...";
  if (modalBrand) modalBrand.textContent = "Obteniendo información";

  contenidoModal.innerHTML = `
    <div class="col-span-2 flex items-center justify-center h-96">
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
 * Genera HTML para el contenido del producto
 */
function generarHTMLProducto(producto) {
  const imagenes = producto.imagen
    ? [`${BASE_DIR}/uploads/${producto.imagen}`]
    : [MODAL_CONFIG.placeholder];

  const precio = Number(producto.precio || 0).toFixed(2);
  const descripcion = producto.descripcion || "Sin descripción disponible";

  // Actualizar el header del modal
  const modalName = document.getElementById("modal_product_name");
  const modalBrand = document.getElementById("modal_product_brand");
  if (modalName) modalName.textContent = producto.nombre.toUpperCase();
  if (modalBrand)
    modalBrand.textContent = (producto.marca || "Software").toUpperCase();

  // Generar miniaturas
  const miniaturas = imagenes
    .map(
      (img, index) => `
    <img src="${img}" 
         alt="Miniatura ${index + 1}"
         class="thumb w-20 h-20 object-cover cursor-pointer border-2 ${
           index === 0 ? "border-orange-400 active" : "border-transparent"
         } hover:border-orange-400 focus:outline-none focus:ring-2 focus:ring-orange-500 snap-start"
         loading="lazy" decoding="async" tabindex="0" 
         data-index="${index}" />
  `
    )
    .join("");

  return `
    <!-- Sección de imágenes (lado izquierdo) -->
    <div class="max-w-2xl mx-auto">
      <section aria-label="Galería de imágenes">
        <!-- Imagen principal -->
        <div class="relative mb-4 overflow-hidden border border-gray-200 bg-white">
          <div class="aspect-square">
            <img id="mainImage"
                src="${imagenes[0]}"
                alt="${producto.nombre}"
                class="h-full w-full object-cover transition-opacity duration-300"
                loading="eager" decoding="async" draggable="false"
                onerror="this.src='${MODAL_CONFIG.placeholder}';" />
          </div>
        </div>

        <!-- Carrusel de miniaturas -->
        ${
          imagenes.length > 1
            ? `
        <div class="relative">
          <div class="pointer-events-none absolute inset-y-0 left-0 w-10 bg-gradient-to-r from-gray-50 to-transparent"></div>
          <div class="pointer-events-none absolute inset-y-0 right-0 w-10 bg-gradient-to-l from-gray-50 to-transparent"></div>

          <div class="flex items-center gap-2">
            <button id="prev"
                class="carousel-prev shrink-0 rounded-md bg-gray-800 text-white px-3 py-2 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                aria-label="Anterior">&#10094;</button>

            <div id="thumbs"
                class="relative flex gap-2 overflow-x-auto scrollbar-hide scroll-smooth snap-x snap-mandatory py-2"
                role="listbox" aria-label="Miniaturas">
              ${miniaturas}
            </div>

            <button id="next"
                class="carousel-next shrink-0 rounded-md bg-gray-800 text-white px-3 py-2 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                aria-label="Siguiente">&#10095;</button>
          </div>
        </div>
        `
            : ""
        }
      </section>
    </div>

    <!-- Sección de información (lado derecho) -->
    <div class="w-full flex flex-col justify-between">
      <div>
        <!-- Precio y cantidad -->
        <div class="flex flex-row justify-between items-center w-full">
          <p class="xl:text-3xl text-lg text-nowrap font-bold">
            USD ${precio}
          </p>

          <div class="relative mt-2 flex max-w-32 items-center justify-end">
            <button type="button"
                class="quantity-decrease xl:h-10 h-8 rounded-s-lg border border-gray-300 bg-gray-100 xl:p-3 p-2 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
              <svg class="h-3 w-3 text-gray-900" fill="none" viewBox="0 0 18 2">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
              </svg>
            </button>
            <input type="text" id="product-quantity"
                class="block xl:h-10 h-8 w-full border-x-0 border-gray-300 bg-gray-50 py-2.5 text-center text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500"
                value="1" min="1" max="50" />
            <button type="button"
                class="quantity-increase xl:h-10 h-8 rounded-e-lg border border-gray-300 bg-gray-100 xl:p-3 p-2 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100">
              <svg class="h-3 w-3 text-gray-900" fill="none" viewBox="0 0 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Descripción del producto -->
        <textarea rows="8" id="product-description"
            class="mt-4 block w-full border border-gray-300 rounded-lg p-2"
            readonly>${descripcion}</textarea>
      </div>

      <!-- Botones de acción -->
      <div class="flex flex-col gap-4 xl:mt-0 mt-4">
        <form method="post" class="w-full">
          <input type="hidden" name="id_producto" value="${
            producto.id_producto
          }">
          <input type="hidden" id="hidden-quantity" name="cantidad" value="1">
          <button type="submit" name="agregar_carrito"
              class="btn-secondary w-full py-3 font-bold text-base xl:text-lg shadow xl:shadow-lg rounded-lg">
            ADD TO CART
          </button>
        </form>
        <a href="#"
            class="block text-center btn-primary w-full py-3 font-bold text-base xl:text-lg shadow xl:shadow-lg rounded-lg">
          MORE DETAILS
        </a>
      </div>
    </div>
  `;
}

/**
 * Genera HTML para mostrar error
 */
function generarHTMLError(productId) {
  return `
    <div class="col-span-2 text-center py-12">
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
 * Inicializa la galería de imágenes
 */
function inicializarGaleria() {
  const mainImage = document.getElementById("mainImage");
  const thumbs = document.querySelectorAll(".thumb");
  const prevBtn = document.getElementById("prev");
  const nextBtn = document.getElementById("next");
  const thumbsContainer = document.getElementById("thumbs");

  if (!mainImage || thumbs.length === 0) return;

  // Click en miniaturas
  thumbs.forEach((thumb, index) => {
    thumb.addEventListener("click", () => {
      mainImage.src = thumb.src;

      // Actualizar clase activa
      thumbs.forEach((t) => {
        t.classList.remove("border-orange-400", "active");
        t.classList.add("border-transparent");
      });
      thumb.classList.remove("border-transparent");
      thumb.classList.add("border-orange-400", "active");
    });
  });

  // Navegación con flechas
  if (prevBtn && nextBtn && thumbsContainer) {
    prevBtn.addEventListener("click", () => {
      thumbsContainer.scrollBy({ left: -88, behavior: "smooth" });
    });

    nextBtn.addEventListener("click", () => {
      thumbsContainer.scrollBy({ left: 88, behavior: "smooth" });
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

  if (!quantityInput) return;

  function updateQuantity(value) {
    const newValue = Math.max(1, Math.min(50, parseInt(value) || 1));
    quantityInput.value = newValue;
    if (hiddenQuantity) hiddenQuantity.value = newValue;
  }

  if (decreaseBtn) {
    decreaseBtn.addEventListener("click", () => {
      updateQuantity(parseInt(quantityInput.value) - 1);
    });
  }

  if (increaseBtn) {
    increaseBtn.addEventListener("click", () => {
      updateQuantity(parseInt(quantityInput.value) + 1);
    });
  }

  quantityInput.addEventListener("input", (e) => {
    updateQuantity(e.target.value);
  });

  quantityInput.addEventListener("blur", (e) => {
    updateQuantity(e.target.value);
  });
}

/**
 * Inicializa todas las funcionalidades interactivas
 */
function inicializarInteractividad() {
  setTimeout(() => {
    inicializarGaleria();
    inicializarControlesCantidad();
  }, 100);
}

/**
 * Maneja el click en botones de previsualizar
 */
function manejarClickPrevisualizar(ev) {
  const btn = ev.target.closest(".preview");
  if (!btn) return;

  ev.preventDefault();
  ev.stopPropagation();

  const productId = btn.getAttribute("data-id");
  if (!productId) {
    console.error("ID de producto no encontrado");
    return;
  }

  console.log(`Iniciando previsualización del producto: ${productId}`);

  if (!abrirModalPrevisualizar()) {
    console.error("No se pudo abrir el modal");
    return;
  }

  mostrarCargando();

  setTimeout(() => {
    cargarDatosProducto(productId);
  }, MODAL_CONFIG.loadDelay);
}

/**
 * Maneja el cierre del modal
 */
function manejarCierreModal(ev) {
  if (ev.target.closest('[data-modal-hide="modal_previsualizar"]')) {
    ev.preventDefault();
    cerrarModalPrevisualizar();
    return;
  }

  const modal = document.getElementById(MODAL_CONFIG.modalId);
  if (ev.target === modal) {
    cerrarModalPrevisualizar();
  }
}

/**
 * Maneja el cierre del modal con tecla Escape
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

function inicializarModalPrevisualizar() {
  console.log("Inicializando modal de previsualización...");

  document.addEventListener("click", manejarClickPrevisualizar);
  document.addEventListener("click", manejarCierreModal);
  document.addEventListener("keydown", manejarTeclasModal);

  const modal = document.getElementById(MODAL_CONFIG.modalId);
  const contenido = document.getElementById(MODAL_CONFIG.contentId);

  if (!modal || !contenido) {
    console.warn("Modal o contenido no encontrado en el DOM");
    return false;
  }

  console.log("Modal de previsualización inicializado correctamente");
  return true;
}

// Exponer funciones globalmente
window.ModalPrevisualizar = {
  abrir: abrirModalPrevisualizar,
  cerrar: cerrarModalPrevisualizar,
  cargarProducto: cargarDatosProducto,
  inicializar: inicializarModalPrevisualizar,
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

// Auto-inicialización
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(inicializarModalPrevisualizar, 100);
});

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", inicializarModalPrevisualizar);
} else {
  setTimeout(inicializarModalPrevisualizar, 100);
}
