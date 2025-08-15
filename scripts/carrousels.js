// carrousels.js - Módulo para gestionar todos los carruseles con Splide
export function initializeCarousels() {
  console.log("Initializing carousels...");

  // ⬇️ Cambia esta línea:
  // initHeroCarousel();
  // ⬇️ Por esta:
  initResponsiveHero();

  // Products carousel
  initProductsCarousel();

  // Testimonials carousel
  initTestimonialsCarousel();

  // 🔁 Escucha cambios de breakpoint para alternar entre mobile/desktop
  const mqMD = window.matchMedia("(min-width: 768px)");
  const mqXL = window.matchMedia("(min-width: 1280px)");
  if (mqMD.addEventListener) {
    mqMD.addEventListener("change", initResponsiveHero);
    mqXL.addEventListener("change", initResponsiveHero);
  } else {
    // Safari viejo
    mqMD.addListener(initResponsiveHero);
    mqXL.addListener(initResponsiveHero);
  }
}

/** =========================
 *  HERO RESPONSIVE (nuevo)
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

let heroInstance = null;

/**
 * Monta SOLO el Hero visible según tus breakpoints:
 * - Mobile: < 768px  => #image-carousel-mobile (50vh)
 * - Desktop: >= 1280px => #image-carousel (85vh)
 * - Entre 768px y 1279px, según tu HTML, NO hay hero visible (se destruye si existía)
 */
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
    // No hay hero visible en este rango (md–lg) con tu configuración actual
    console.log("Hero oculto en este breakpoint (md–xl sin xl).");
  }
}

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
        gap: "0.1rem",
        autoplay: false,
        pauseOnHover: true,
        arrows: true,
        pagination: false,
        breakpoints: {
          1024: {
            perPage: 3,
            gap: "1.1rem",
          },
          768: {
            perPage: 2,
            gap: "1rem",
          },
          640: {
            perPage: 1,
            gap: "1rem",
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

// También puedes exportar funciones individuales si las necesitas
export { initHeroCarousel, initProductsCarousel, initTestimonialsCarousel };

// Si necesitas reinicializar algún carrusel específico más tarde
export function reinitializeCarousel(carouselType) {
  switch (carouselType) {
    case "hero":
      initResponsiveHero(); // ⬅️ usa el responsive
      break;
    case "products":
      initProductsCarousel();
      break;
    case "testimonials":
      initTestimonialsCarousel();
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
