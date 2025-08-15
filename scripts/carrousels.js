// carrousels.js - Módulo para gestionar todos los carruseles con Splide
export function initializeCarousels() {
  console.log("Initializing carousels...");

  // Hero responsive
  initResponsiveHero();

  // Products carousel
  initProductsCarousel();

  // Testimonials carousel
  initTestimonialsCarousel();

  // Statistics carousel (mobile only)
  initResponsiveStatistics();

  // Purchase process carousel (mobile only)
  initPurchaseProcessCarousel();

  // Formas de pago carousel
  initFormasPagoCarousel();

  // 🔁 Escucha cambios de breakpoint para alternar entre mobile/desktop
  const mqMD = window.matchMedia("(min-width: 768px)");
  const mqXL = window.matchMedia("(min-width: 1280px)");
  if (mqMD.addEventListener) {
    mqMD.addEventListener("change", initResponsiveHero);
    mqXL.addEventListener("change", initResponsiveHero);
    mqMD.addEventListener("change", initResponsiveStatistics);
    mqMD.addEventListener("change", initPurchaseProcessCarousel);
  } else {
    // Safari viejo
    mqMD.addListener(initResponsiveHero);
    mqXL.addListener(initResponsiveHero);
    mqMD.addListener(initResponsiveStatistics);
    mqMD.addListener(initPurchaseProcessCarousel);
  }
}

/** =========================
 *  HELPER FUNCTIONS
 *  ========================= */
function mountIfNotMounted(selector, options) {
  const el = document.querySelector(selector);
  if (!el) return null;

  // Si ya estaba montado, lo destruimos para evitar montajes dobles
  if (el.splide) {
    try {
      el.splide.destroy(true);
    } catch (_) {}
  }

  const inst = new Splide(selector, options);
  inst.mount();
  return inst;
}

/** =========================
 *  HERO RESPONSIVE
 *  ========================= */
let heroInstance = null;

function initResponsiveHero() {
  const isMD = window.matchMedia("(min-width: 768px)").matches;
  const isXL = window.matchMedia("(min-width: 1280px)").matches;

  // Limpia cualquier instancia previa
  if (heroInstance) {
    try {
      heroInstance.destroy(true);
    } catch (_) {}
    heroInstance = null;
  }

  if (isXL && document.getElementById("image-carousel")) {
    // Desktop (xl+)
    heroInstance = mountIfNotMounted("#image-carousel", {
      type: "loop",
      autoplay: true,
      interval: 5000,
      pauseOnHover: true,
      arrows: true,
      pagination: true,
      cover: true,
      height: "85vh",
      lazyLoad: "nearby",
    });
    console.log("Hero desktop mounted");
  } else if (!isMD && document.getElementById("image-carousel-mobile")) {
    // Mobile (< md)
    heroInstance = mountIfNotMounted("#image-carousel-mobile", {
      type: "loop",
      autoplay: true,
      interval: 5000,
      pauseOnHover: true,
      arrows: true,
      pagination: true,
      cover: true,
      height: "50vh",
      lazyLoad: "nearby",
    });
    console.log("Hero mobile mounted");
  } else {
    console.log("Hero oculto en este breakpoint (md–xl sin xl).");
  }

  // 🔄 Sincronizar statistics con hero si ambos están activos
  syncStatisticsWithHero();
}

/** =========================
 *  STATISTICS RESPONSIVE
 *  ========================= */
let statisticsInstance = null;

function initResponsiveStatistics() {
  const isMD = window.matchMedia("(min-width: 768px)").matches;

  // Limpia cualquier instancia previa
  if (statisticsInstance) {
    try {
      statisticsInstance.destroy(true);
    } catch (_) {}
    statisticsInstance = null;
  }

  // Solo montar en mobile (< md)
  if (!isMD && document.getElementById("statistics-carousel")) {
    statisticsInstance = mountIfNotMounted("#statistics-carousel", {
      type: "loop",
      autoplay: true,
      interval: 5000, // Mismo interval que el hero para sincronización
      pauseOnHover: true,
      arrows: false, // Flechas activadas
      pagination: true,
      perPage: 1,
      perMove: 1,
      gap: 0,
      padding: 0,
      height: "auto", // Altura automática para ser más compacto
      fixedHeight: false,
    });
    console.log("Statistics carousel mounted (mobile)");

    // 🔄 Sincronizar con hero después de montar
    syncStatisticsWithHero();
  } else {
    console.log("Statistics usando grid estático (desktop)");
  }
}

/** =========================
 *  PURCHASE PROCESS CAROUSEL (MOBILE ONLY)
 *  ========================= */
let purchaseProcessInstance = null;

function initPurchaseProcessCarousel() {
  const isMD = window.matchMedia("(min-width: 768px)").matches;

  // Limpia cualquier instancia previa
  if (purchaseProcessInstance) {
    try {
      purchaseProcessInstance.destroy(true);
    } catch (_) {}
    purchaseProcessInstance = null;
  }

  // Solo montar en mobile (< md)
  if (!isMD && document.getElementById("purchase-process-carousel")) {
    purchaseProcessInstance = mountIfNotMounted("#purchase-process-carousel", {
      type: "loop",
      autoplay: true,
      interval: 4000,
      pauseOnHover: true,
      arrows: true,
      pagination: false,
      perPage: 2, // 2 slides por vista
      perMove: 1,
      gap: "1rem",
      padding: {
        left: "1rem",
        right: "1rem",
      },
      height: "auto",
      fixedHeight: false,
      breakpoints: {
        480: {
          perPage: 2, // En pantallas muy pequeñas, 1 slide por vista
          gap: "0.5rem",
          padding: {
            left: "0.5rem",
            right: "0.5rem",
          },
        },
      },
    });
    console.log("Purchase process carousel mounted (mobile)");
  } else {
    console.log("Purchase process usando grid estático (desktop)");
  }
}

/** =========================
 *  FORMAS DE PAGO CAROUSEL
 *  ========================= */
let formasPagoInstance = null;

function initFormasPagoCarousel() {
  const formasPagoCarousel = document.getElementById("formas-pago-carousel");

  // Limpia cualquier instancia previa
  if (formasPagoInstance) {
    try {
      formasPagoInstance.destroy(true);
    } catch (_) {}
    formasPagoInstance = null;
  }

  if (formasPagoCarousel) {
    try {
      formasPagoInstance = mountIfNotMounted("#formas-pago-carousel", {
        type: "loop",
        perPage: 1, // 1 imagen por vista
        perMove: 1,
        autoplay: true,
        interval: 4000, // Cambia cada 4 segundos
        pauseOnHover: true,
        arrows: true,
        pagination: false,
        gap: 0,
        padding: 0,
        height: "auto",
        fixedHeight: false,
        lazyLoad: "nearby",
        // Configuración responsive (mismo comportamiento en todas las pantallas)
        breakpoints: {
          1280: {
            perPage: 1,
          },
          1024: {
            perPage: 1,
          },
          768: {
            perPage: 1,
          },
          640: {
            perPage: 1,
          },
          480: {
            perPage: 1,
          },
        },
      });

      console.log("Formas de pago carousel initialized successfully");
    } catch (error) {
      console.error("Error initializing formas de pago carousel:", error);
    }
  } else {
    console.error("Formas de pago carousel element not found");
  }
}

/** =========================
 *  SINCRONIZACIÓN HERO-STATISTICS
 *  ========================= */
function syncStatisticsWithHero() {
  // Sincronización eliminada: ahora ambos sliders funcionan de forma independiente
}

/** =========================
 *  OTROS CARRUSELES
 *  ========================= */

/**
 * Inicializa el carrusel principal (Hero) - (se mantiene por compatibilidad, ya no se llama)
 */
function initHeroCarousel() {
  const heroCarousel = document.getElementById("image-carousel");

  if (heroCarousel) {
    try {
      new Splide("#image-carousel", {
        type: "loop",
        autoplay: true,
        interval: 5000,
        pauseOnHover: true,
        arrows: true,
        pagination: true,
        cover: true,
        height: "85vh",
      }).mount();

      console.log("Hero carousel initialized successfully");
    } catch (error) {
      console.error("Error initializing hero carousel:", error);
    }
  }
}

/**
 * Inicializa el carrusel de productos
 */
function initProductsCarousel() {
  const productsCarousel = document.getElementById("products-carousel");

  if (productsCarousel) {
    try {
      new Splide("#products-carousel", {
        type: "loop",
        perPage: 4,
        perMove: 1,
        autoplay: false,
        pauseOnHover: true,
        arrows: true,

        pagination: false,
        breakpoints: {
          1024: {
            perPage: 3,
          },
          768: {
            perPage: 2,
          },
          640: {
            perPage: 2,
          },
        },
      }).mount();

      console.log("Products carousel initialized successfully");
    } catch (error) {
      console.error("Error initializing products carousel:", error);
    }
  }
}

/**
 * Inicializa el carrusel de testimonios
 */
function initTestimonialsCarousel() {
  const testimonialsCarousel = document.getElementById("testimonials-carousel");

  if (testimonialsCarousel) {
    try {
      new Splide("#testimonials-carousel", {
        type: "loop",
        perPage: 4,
        perMove: 1,
        gap: "1.5rem",
        autoplay: true,
        interval: 4000,
        pauseOnHover: true,
        arrows: true,
        pagination: true,
        focus: "center",
        trimSpace: false,
        breakpoints: {
          1280: {
            perPage: 3,
            gap: "1.2rem",
          },
          1024: {
            perPage: 2,
            gap: "1rem",
          },
          768: {
            perPage: 1,
            gap: "1rem",
            focus: "center",
          },
          640: {
            perPage: 1,
            gap: "0.8rem",
            focus: "center",
          },
        },
      }).mount();

      console.log("Testimonials carousel initialized successfully");
    } catch (error) {
      console.error("Error initializing testimonials carousel:", error);
    }
  } else {
    console.error("Testimonials carousel element not found");
  }
}

/** =========================
 *  EXPORTS Y UTILIDADES
 *  ========================= */

// También puedes exportar funciones individuales si las necesitas
export {
  initHeroCarousel,
  initProductsCarousel,
  initTestimonialsCarousel,
  initResponsiveStatistics,
  initPurchaseProcessCarousel,
  initFormasPagoCarousel,
};

// Si necesitas reinicializar algún carrusel específico más tarde
export function reinitializeCarousel(carouselType) {
  switch (carouselType) {
    case "hero":
      initResponsiveHero();
      break;
    case "products":
      initProductsCarousel();
      break;
    case "testimonials":
      initTestimonialsCarousel();
      break;
    case "statistics":
      initResponsiveStatistics();
      break;
    case "purchase-process":
      initPurchaseProcessCarousel();
      break;
    case "formas-pago":
      initFormasPagoCarousel();
      break;
    default:
      console.warn(`Unknown carousel type: ${carouselType}`);
  }
}

// Función de utilidad para destruir un carrusel si es necesario
export function destroyCarousel(selector) {
  const element = document.querySelector(selector);
  if (element && element.splide) {
    element.splide.destroy();
    console.log(`Carousel ${selector} destroyed`);
  }
}

// Función para pausar/reanudar sincronización
export function toggleSync(enable = true) {
  if (enable) {
    syncStatisticsWithHero();
    console.log("✅ Sincronización activada");
  } else {
    // Remover event listeners sería más complejo, pero puedes pausar autoplay
    if (statisticsInstance) {
      statisticsInstance.Components.Autoplay.pause();
    }
    console.log("⏸️ Sincronización pausada");
  }
}

function ensureSplide() {
  if (typeof Splide === "undefined") {
    console.error(
      '❌ Splide no está cargado. Revisa el <script src="...splide.min.js">'
    );
    return false;
  }
  return true;
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", () => {
    if (ensureSplide()) initializeCarousels();
  });
} else {
  if (ensureSplide()) initializeCarousels();
}
