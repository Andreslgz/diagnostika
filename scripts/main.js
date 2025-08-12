// Marquee functionality
document.addEventListener("DOMContentLoaded", function () {
  const marqueeContainer = document.querySelector(".marquee-container");
  const marqueeText = document.querySelector(".marquee-text");

  if (marqueeContainer && marqueeText) {
    // Calculate animation duration based on text length for consistent speed
    const textLength = marqueeText.scrollWidth;
    const containerWidth = marqueeContainer.offsetWidth;
    const duration = (textLength + containerWidth) / 50; // Adjust speed factor as needed

    marqueeText.style.animationDuration = duration + "s";

    // Add smooth pause/resume on hover
    marqueeContainer.addEventListener("mouseenter", function () {
      marqueeText.style.animationPlayState = "paused";
    });

    marqueeContainer.addEventListener("mouseleave", function () {
      marqueeText.style.animationPlayState = "running";
    });

    // Restart animation on window resize
    window.addEventListener("resize", function () {
      const newTextLength = marqueeText.scrollWidth;
      const newContainerWidth = marqueeContainer.offsetWidth;
      const newDuration = (newTextLength + newContainerWidth) / 50;
      marqueeText.style.animationDuration = newDuration + "s";
    });
  }
});

// Splide carousel initialization
document.addEventListener("DOMContentLoaded", function () {
  // Hero carousel
  const heroCarousel = document.getElementById("image-carousel");
  if (heroCarousel) {
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
  }

  // Products carousel
  const productsCarousel = document.getElementById("products-carousel");
  if (productsCarousel) {
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
  }

  // Testimonials carousel
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
});

// Modal tab functionality
document.addEventListener("DOMContentLoaded", function () {
  const modalBody = document.getElementById("modal-body");
  const registerTab = document.getElementById("register-tab");
  const loginTab = document.getElementById("login-tab");
  const registerPanel = document.getElementById("register");
  const loginPanel = document.getElementById("login");

  // Shared functions for tab management
  function resetTab(tab) {
    if (!tab) return;
    tab.className =
      "inline-block w-full p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 tab-button-transition";
    tab.setAttribute("aria-selected", "false");
  }

  function activateTab(tab) {
    if (!tab) return;
    tab.className =
      "inline-block w-full p-4 border-b-2 border-amber-500 rounded-t-lg text-amber-600 tab-button-transition";
    tab.setAttribute("aria-selected", "true");
  }

  function showLoginTab() {
    console.log("Showing login tab");
    resetTab(registerTab);
    activateTab(loginTab);

    if (registerPanel && loginPanel) {
      registerPanel.className =
        "hidden flex-col justify-center h-full tab-content-transition";
      loginPanel.className =
        "flex flex-col justify-center h-full tab-content-transition";
      loginPanel.style.opacity = "1";
    }
  }

  function showRegisterTab() {
    console.log("Showing register tab");
    resetTab(loginTab);
    activateTab(registerTab);

    if (registerPanel && loginPanel) {
      loginPanel.className =
        "hidden flex-col justify-center h-full tab-content-transition";
      registerPanel.className =
        "flex flex-col justify-center h-full tab-content-transition";
      registerPanel.style.opacity = "1";
    }
  }

  // Handle modal buttons with specific tab targeting
  const modalButtons = document.querySelectorAll(
    '[data-modal-target="authentication-modal"]'
  );

  modalButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const targetTab = this.getAttribute("data-active-tab");
      console.log("Button clicked, target tab:", targetTab);

      // Immediately configure the modal content before it's visible
      setTimeout(() => {
        if (
          !registerTab ||
          !loginTab ||
          !registerPanel ||
          !loginPanel ||
          !modalBody
        ) {
          console.error("Modal elements not found");
          return;
        }

        // Configure modal content based on target tab
        if (targetTab === "register") {
          showRegisterTab();
        } else if (targetTab === "login") {
          showLoginTab();
        }

        // Show the modal content with a smooth fade-in
        setTimeout(() => {
          modalBody.classList.remove("modal-content-hidden");
          modalBody.classList.add("modal-content-visible");
        }, 10);
      }, 10); // Very quick timeout to ensure modal is in DOM
    });
  });

  // Add direct tab click handlers
  if (loginTab) {
    loginTab.addEventListener("click", function (e) {
      e.preventDefault();
      showLoginTab();
    });
  }

  if (registerTab) {
    registerTab.addEventListener("click", function (e) {
      e.preventDefault();
      showRegisterTab();
    });
  }

  // Add handlers for the switch buttons in forms
  const switchToRegisterBtn = document.getElementById("switch-to-register");
  const switchToLoginBtn = document.getElementById("switch-to-login");

  if (switchToRegisterBtn) {
    switchToRegisterBtn.addEventListener("click", function (e) {
      e.preventDefault();
      showRegisterTab();
    });
  }

  if (switchToLoginBtn) {
    switchToLoginBtn.addEventListener("click", function (e) {
      e.preventDefault();
      showLoginTab();
    });
  }

  // Reset modal content when modal is closed
  const modal = document.getElementById("authentication-modal");
  if (modal) {
    modal.addEventListener("hidden.bs.modal", function () {
      if (modalBody) {
        modalBody.classList.remove("modal-content-visible");
        modalBody.classList.add("modal-content-hidden");
      }
    });
  }
});

// Logica para el login / via ajax....

const loginFormEl = document.getElementById("login-form");
if (loginFormEl)
  loginFormEl.addEventListener("submit", function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch("login.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.location.href = data.redirect;
        } else {
          const errorDiv = document.getElementById("login-error");
          errorDiv.textContent = data.message;
          errorDiv.classList.remove("hidden");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
      });
  });

//Logica para registrar al usuario

const registerFormEl = document.getElementById("register-form");
if (registerFormEl)
  registerFormEl.addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = e.target;

    // Validación rápida en cliente
    const pwd = form.querySelector('input[name="password"]').value.trim();
    const pwd2 = form
      .querySelector('input[name="password_confirm"]')
      .value.trim();
    if (pwd !== pwd2) {
      showError("Las contraseñas no coinciden.");
      return;
    }

    const formData = new FormData(form);
    try {
      const res = await fetch("register.php", {
        method: "POST",
        body: formData,
      });
      const data = await res.json();

      if (data.success) {
        showSuccess(data.message || "¡Registro exitoso!");
        // autologin: redirige según lo que diga el backend
        if (data.redirect) window.location.href = data.redirect;
        else form.reset();
      } else {
        showError(data.message || "No pudimos crear tu cuenta.");
      }
    } catch (err) {
      console.error(err);
      showError("Ocurrió un error inesperado. Inténtalo de nuevo.");
    }
  });

function showError(msg) {
  const e = document.getElementById("register-error");
  const s = document.getElementById("register-success");
  s.classList.add("hidden");
  e.textContent = msg;
  e.classList.remove("hidden");
}
function showSuccess(msg) {
  const e = document.getElementById("register-error");
  const s = document.getElementById("register-success");
  e.classList.add("hidden");
  s.textContent = msg;
  s.classList.remove("hidden");
}

// Logica modal Salir de la session

const logoutBtnEl = document.getElementById("logoutModalBtn");
if (logoutBtnEl)
  logoutBtnEl.addEventListener("click", () => {
    const modal = document.getElementById("logoutModal");
    if (!modal) return;
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  });

const cancelLogoutEl = document.getElementById("cancelLogout");
if (cancelLogoutEl)
  cancelLogoutEl.addEventListener("click", () => {
    const modal = document.getElementById("logoutModal");
    if (!modal) return;
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  });

//Logica de agregar a favoritos

document.querySelectorAll(".favorito-btn").forEach((btn) => {
  btn.addEventListener("click", function (e) {
    e.preventDefault();
    const id = this.dataset.id;
    const svg = this.querySelector("svg");

    fetch("ajax_favorito.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: "id_producto=" + encodeURIComponent(id),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.auth === false) {
          mostrarAlerta(
            "Debes iniciar sesión para agregar productos a favoritos."
          );
          return;
        }

        if (data.success) {
          if (data.favorito) {
            svg.setAttribute("fill", "red");
            svg.classList.remove("text-black");
            svg.classList.add("text-red-600");
          } else {
            svg.setAttribute("fill", "none");
            svg.classList.remove("text-red-600");
            svg.classList.add("text-black");
          }
        } else {
          mostrarAlerta(data.message || "Ocurrió un error.");
        }
      })
      .catch(() => {
        mostrarAlerta("Error de conexión con el servidor.");
      });
  });
});

//Mostrar alerta de favoritos
function mostrarAlerta(mensaje) {
  const alerta = document.getElementById("alertaFavorito");
  const texto = document.getElementById("alertaTexto");

  texto.textContent = mensaje;
  alerta.classList.remove("hidden");
  alerta.classList.add("flex");

  setTimeout(() => {
    alerta.classList.add("hidden");
    alerta.classList.remove("flex");
  }, 3000);
}

// Auto-hide cart alert
document.addEventListener("DOMContentLoaded", function () {
  const alertCarrito = document.getElementById("alertCarrito");
  if (alertCarrito) {
    // Auto-hide after 4 seconds
    setTimeout(() => {
      alertCarrito.style.opacity = "0";
      alertCarrito.style.transform = "translateX(100%)";
      setTimeout(() => {
        alertCarrito.remove();
      }, 300);
    }, 4000);

    // Add close button functionality if needed
    alertCarrito.addEventListener("click", function () {
      this.style.opacity = "0";
      this.style.transform = "translateX(100%)";
      setTimeout(() => {
        this.remove();
      }, 300);
    });
  }
});

/* ============================================
   FAQ FUNCTIONALITY - Funcionalidad de Preguntas Frecuentes
   ============================================ */

// FAQ Accordion - Sistema de acordeón para preguntas frecuentes
document.addEventListener("DOMContentLoaded", function () {
  // Usar un timeout para asegurar que todo el DOM esté completamente cargado
  setTimeout(() => {
    initializeFAQ();
  }, 100);
});

function initializeFAQ() {
  console.log("Initializing FAQ...");

  const faqContainer = document.getElementById("faq-container");
  if (!faqContainer) {
    console.warn("FAQ container not found");
    return;
  }

  const faqItems = document.querySelectorAll(".faq-item");
  const faqHeaders = document.querySelectorAll(".faq-header");

  console.log(
    `Found ${faqItems.length} FAQ items and ${faqHeaders.length} headers`
  );

  if (faqHeaders.length === 0) {
    console.warn("No FAQ headers found");
    return;
  }

  // Delegación: permitir clic en todo el item FAQ
  faqContainer.addEventListener("click", function (e) {
    const item = e.target.closest(".faq-item");
    if (!item || !faqContainer.contains(item)) return;
    const header = item.querySelector(".faq-header");
    if (!header) return;
    const headers = Array.from(document.querySelectorAll(".faq-header"));
    const index = headers.indexOf(header);
    if (index === -1) return;
    e.preventDefault();
    handleFAQToggle(header, index);
  });

  // Configurar eventos para cada header de FAQ (teclado y accesibilidad)
  faqHeaders.forEach((header, index) => {
    console.log(`Setting up FAQ header ${index}`);

    // Remover listeners existentes si los hay
    header.removeEventListener("click", header._faqClickHandler);
    header.removeEventListener("keydown", header._faqKeyHandler);

    // Crear handlers y almacenarlos en el elemento
    header._faqClickHandler = function (e) {
      e.preventDefault();
      e.stopPropagation();
      console.log(`FAQ ${index} clicked`);
      handleFAQToggle(this, index);
    };

    header._faqKeyHandler = function (e) {
      handleFAQKeyboard(e, this, index);
    };

    // Agregar los event listeners
    // El click lo maneja la delegación anterior para que funcione en todo el item
    header.addEventListener("keydown", header._faqKeyHandler);

    // Mejorar accesibilidad
    header.setAttribute("role", "button");
    header.setAttribute("tabindex", "0");
    header.setAttribute("aria-expanded", "false");
    header.setAttribute("aria-controls", `faq-content-${index}`);

    // Agregar ID al contenido para accesibilidad
    const content = header.nextElementSibling;
    if (content && content.classList.contains("faq-content")) {
      content.setAttribute("id", `faq-content-${index}`);
      content.setAttribute("role", "region");
      content.setAttribute("aria-labelledby", `faq-header-${index}`);
    }

    // Agregar ID al header
    header.setAttribute("id", `faq-header-${index}`);
  });

  console.log(`FAQ initialized successfully with ${faqItems.length} items`);
}

function handleFAQToggle(header, index) {
  console.log(`Handling FAQ toggle for index ${index}`);

  const faqItem = header.closest(".faq-item");
  const content = header.nextElementSibling;
  const icon = header.querySelector(".faq-icon");

  if (!faqItem || !content) {
    console.error(`Missing elements for FAQ ${index}:`, {
      faqItem: !!faqItem,
      content: !!content,
      icon: !!icon,
    });
    return;
  }

  const isActive = faqItem.classList.contains("active");
  console.log(`FAQ ${index} is currently ${isActive ? "active" : "inactive"}`);

  try {
    if (isActive) {
      // Cerrar FAQ actual
      console.log(`Closing FAQ ${index}`);
      closeFAQ(faqItem, header, content, icon);
    } else {
      // Cerrar otros FAQs (comportamiento acordeón)
      console.log(`Opening FAQ ${index}, closing others`);
      closeAllFAQs();

      // Abrir FAQ seleccionado
      openFAQ(faqItem, header, content, icon);
    }

    // Scroll suave al FAQ si es necesario
    if (!isActive) {
      setTimeout(() => {
        scrollToFAQ(faqItem);
      }, 300);
    }
  } catch (error) {
    console.error("Error handling FAQ toggle:", error);
  }
}

function openFAQ(faqItem, header, content, icon) {
  console.log(
    "Opening FAQ:",
    faqItem.querySelector("h3")?.textContent?.substring(0, 30) + "..."
  );

  // Agregar clase activa
  faqItem.classList.add("active");

  // Actualizar aria-expanded
  header.setAttribute("aria-expanded", "true");

  // Calcular altura del contenido
  const contentInner = content.querySelector("div");
  if (!contentInner) {
    console.error("Content inner div not found");
    return;
  }

  // Resetear el max-height para calcular la altura real
  content.style.maxHeight = "none";
  const targetHeight = contentInner.scrollHeight;
  content.style.maxHeight = "0px";

  // Forzar reflow
  content.offsetHeight;

  // Animar apertura
  requestAnimationFrame(() => {
    content.style.maxHeight = targetHeight + "px";
  });

  // Añadir clase de animación
  setTimeout(() => {
    content.classList.add("faq-open");
  }, 10);

  console.log(`FAQ opened with height: ${targetHeight}px`);
}

function closeFAQ(faqItem, header, content, icon) {
  console.log(
    "Closing FAQ:",
    faqItem.querySelector("h3")?.textContent?.substring(0, 30) + "..."
  );

  // Remover clase activa
  faqItem.classList.remove("active");

  // Actualizar aria-expanded
  header.setAttribute("aria-expanded", "false");

  // Animar cierre
  content.style.maxHeight = "0px";

  // Remover clase de animación
  content.classList.remove("faq-open");

  console.log("FAQ closed");
}

function closeAllFAQs() {
  const activeFAQs = document.querySelectorAll(".faq-item.active");

  activeFAQs.forEach((faqItem) => {
    const header = faqItem.querySelector(".faq-header");
    const content = faqItem.querySelector(".faq-content");
    const icon = faqItem.querySelector(".faq-icon");

    closeFAQ(faqItem, header, content, icon);
  });
}

function handleFAQKeyboard(e, header, index) {
  const faqItems = document.querySelectorAll(".faq-header");

  switch (e.key) {
    case "Enter":
    case " ": // Spacebar
      e.preventDefault();
      handleFAQToggle(header, index);
      break;

    case "ArrowDown":
      e.preventDefault();
      const nextIndex = (index + 1) % faqItems.length;
      faqItems[nextIndex].focus();
      break;

    case "ArrowUp":
      e.preventDefault();
      const prevIndex = (index - 1 + faqItems.length) % faqItems.length;
      faqItems[prevIndex].focus();
      break;

    case "Home":
      e.preventDefault();
      faqItems[0].focus();
      break;

    case "End":
      e.preventDefault();
      faqItems[faqItems.length - 1].focus();
      break;

    case "Escape":
      e.preventDefault();
      closeAllFAQs();
      header.blur();
      break;
  }
}

function scrollToFAQ(faqItem) {
  const rect = faqItem.getBoundingClientRect();
  const isVisible = rect.top >= 0 && rect.bottom <= window.innerHeight;

  if (!isVisible) {
    faqItem.scrollIntoView({
      behavior: "smooth",
      block: "center",
      inline: "nearest",
    });
  }
}

// FAQ Utilidades adicionales
function getFAQState() {
  const faqItems = document.querySelectorAll(".faq-item");
  const state = {};

  faqItems.forEach((item, index) => {
    const question = item.querySelector("h3").textContent.trim();
    const isActive = item.classList.contains("active");
    state[index] = {
      question: question.substring(0, 50) + "...",
      isActive: isActive,
    };
  });

  return state;
}

// Función para abrir FAQ específico por índice (útil para enlaces directos)
function openFAQByIndex(index) {
  const faqItems = document.querySelectorAll(".faq-item");
  if (index >= 0 && index < faqItems.length) {
    const header = faqItems[index].querySelector(".faq-header");
    if (header) {
      closeAllFAQs();
      setTimeout(() => {
        handleFAQToggle(header, index);
      }, 100);
    }
  }
}

// Función para abrir FAQ por texto de pregunta (búsqueda parcial)
function openFAQByQuestion(searchText) {
  const faqItems = document.querySelectorAll(".faq-item");

  faqItems.forEach((item, index) => {
    const question = item.querySelector("h3").textContent.toLowerCase();
    if (question.includes(searchText.toLowerCase())) {
      const header = item.querySelector(".faq-header");
      if (header) {
        closeAllFAQs();
        setTimeout(() => {
          handleFAQToggle(header, index);
        }, 100);
        return true;
      }
    }
  });

  return false;
}

// Exponer funciones globalmente para uso desde consola o otros scripts
window.FAQUtils = {
  openByIndex: openFAQByIndex,
  openByQuestion: openFAQByQuestion,
  closeAll: closeAllFAQs,
  getState: getFAQState,
  // Funciones de debugging
  reinitialize: initializeFAQ,
  testFAQ: function (index = 0) {
    console.log("Testing FAQ functionality...");
    const faqHeaders = document.querySelectorAll(".faq-header");
    if (faqHeaders[index]) {
      console.log(`Testing FAQ ${index}`);
      faqHeaders[index].click();
    } else {
      console.error(
        `FAQ ${index} not found. Available FAQs: 0-${faqHeaders.length - 1}`
      );
    }
  },
  debug: function () {
    console.log("=== FAQ DEBUG INFO ===");
    const container = document.getElementById("faq-container");
    const items = document.querySelectorAll(".faq-item");
    const headers = document.querySelectorAll(".faq-header");
    const contents = document.querySelectorAll(".faq-content");

    console.log("Container found:", !!container);
    console.log("Items found:", items.length);
    console.log("Headers found:", headers.length);
    console.log("Contents found:", contents.length);

    headers.forEach((header, index) => {
      const hasClickListener = header._faqClickHandler !== undefined;
      console.log(`Header ${index}: has click listener = ${hasClickListener}`);
    });

    return {
      container: !!container,
      items: items.length,
      headers: headers.length,
      contents: contents.length,
    };
  },
};
