document.addEventListener("DOMContentLoaded", function () {
  const marqueeContainer = document.querySelector(".marquee-container");
  const marqueeWrapper = document.querySelector(".marquee-wrapper");
  const marqueeTexts = document.querySelectorAll(".marquee-text");

  if (marqueeContainer && marqueeWrapper && marqueeTexts.length > 0) {
    function adjustSpeed() {
      // Calcula el ancho del contenido
      const textWidth = marqueeTexts[0].offsetWidth + 50; 
      const containerWidth = marqueeContainer.offsetWidth;

      const pixelsPerSecond = 100; 

      // Calcula la duración basada en la velocidad deseada
      const duration = textWidth / pixelsPerSecond;

      // Aplica la nueva duración
      marqueeWrapper.style.animationDuration = duration + "s";

      // Si el texto es muy corto, duplica más veces para llenar el espacio
      if (textWidth < containerWidth) {
        const timesNeeded = Math.ceil(containerWidth / textWidth) + 2;
        const currentTexts = marqueeWrapper.innerHTML;
        let newContent = "";

        for (let i = 0; i < timesNeeded; i++) {
          newContent += currentTexts;
        }

        marqueeWrapper.innerHTML = newContent;
      }
    }

    // Ajusta la velocidad inicial
    adjustSpeed();

    // Reajusta en cambio de tamaño de ventana
    let resizeTimer;
    window.addEventListener("resize", function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(adjustSpeed, 250);
    });

    // Pausa/reanuda en hover (ya manejado por CSS, pero podemos añadir efectos adicionales)
    marqueeContainer.addEventListener("mouseenter", function () {
      marqueeWrapper.style.animationPlayState = "paused";
    });

    marqueeContainer.addEventListener("mouseleave", function () {
      marqueeWrapper.style.animationPlayState = "running";
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

    fetch(BASE_DIR + "/login.php", {
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
      const res = await fetch(BASE_DIR + "/register.php", {
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

    fetch(BASE_DIR + "/ajax_favorito.php", {
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
  s;
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

/* ============================================
   CHATBOT FUNCTIONALITY - Funcionalidad del Chatbot
   ============================================ */

// Chatbot - Sistema de chat flotante simulado
document.addEventListener("DOMContentLoaded", function () {
  initializeChatbot();
});

function initializeChatbot() {
  console.log("Initializing Chatbot...");

  const chatTrigger = document.getElementById("chatBotTrigger");
  const chatContainer = document.getElementById("chatbotContainer");
  const closeChatbot = document.getElementById("closeChatbot");
  const sendButton = document.getElementById("sendMessage");
  const chatInput = document.getElementById("chatInput");
  const chatMessages = document.getElementById("chatMessages");
  const typingIndicator = document.getElementById("typingIndicator");

  if (!chatTrigger || !chatContainer) {
    console.warn("Chatbot elements not found");
    return;
  }

  // Abrir chatbot
  chatTrigger.addEventListener("click", function () {
    console.log("Opening chatbot");
    chatContainer.classList.remove("hidden");
    chatContainer.classList.add("animate-chat-slide-in");
    setTimeout(() => {
      chatInput.focus();
    }, 100);
  });

  // Cerrar chatbot
  closeChatbot.addEventListener("click", function () {
    console.log("Closing chatbot");
    chatContainer.classList.remove("animate-chat-slide-in");
    setTimeout(() => {
      chatContainer.classList.add("hidden");
    }, 300);
  });

  // Enviar mensaje
  function sendMessage() {
    const message = chatInput.value.trim();
    if (!message) return;

    // Agregar mensaje del usuario
    addUserMessage(message);
    chatInput.value = "";

    // Simular respuesta del bot
    setTimeout(() => {
      showTypingIndicator();
      setTimeout(() => {
        hideTypingIndicator();
        addBotResponse(message);
      }, 1500);
    }, 500);
  }

  // Event listeners para enviar mensaje
  sendButton.addEventListener("click", sendMessage);

  chatInput.addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      sendMessage();
    }
  });

  // Funciones auxiliares del chatbot
  function addUserMessage(message) {
    const messageDiv = document.createElement("div");
    messageDiv.className = "flex items-start gap-2 justify-end chatbot-message";
    messageDiv.innerHTML = `
      <div class="bg-amber-400 text-white rounded-lg rounded-tr-none px-3 py-2 shadow-sm max-w-xs">
        <p class="text-sm">${escapeHtml(message)}</p>
      </div>
      <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
        <span class="text-gray-600 text-xs">👤</span>
      </div>
    `;
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
  }

  function addBotResponse(userMessage) {
    const response = generateBotResponse(userMessage);
    const messageDiv = document.createElement("div");
    messageDiv.className = "flex items-start gap-2 chatbot-message";
    messageDiv.innerHTML = `
      <div class="w-6 h-6 bg-amber-400 rounded-full flex items-center justify-center flex-shrink-0">
        <span class="text-white text-xs">🤖</span>
      </div>
      <div class="bg-white rounded-lg rounded-tl-none px-3 py-2 shadow-sm max-w-xs">
        <p class="text-sm text-gray-800">${response}</p>
      </div>
    `;
    chatMessages.appendChild(messageDiv);
    scrollToBottom();
  }

  function showTypingIndicator() {
    typingIndicator.classList.remove("hidden");
    scrollToBottom();
  }

  function hideTypingIndicator() {
    typingIndicator.classList.add("hidden");
  }

  function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  // Generador de respuestas del bot (simulado)
  function generateBotResponse(userMessage) {
    const message = userMessage.toLowerCase();

    // Respuestas relacionadas con software y productos
    if (message.includes("software") || message.includes("programa")) {
      return "¡Perfecto! Tenemos más de 200 softwares disponibles en nuestro catálogo. ¿Hay algún tipo específico que te interese? 💻";
    }

    if (
      message.includes("precio") ||
      message.includes("costo") ||
      message.includes("cuanto")
    ) {
      return "Los precios varían según el software. Te recomiendo revisar nuestro catálogo o contactar a nuestro equipo de ventas para cotizaciones personalizadas. 💰";
    }

    if (
      message.includes("descuento") ||
      message.includes("oferta") ||
      message.includes("promocion")
    ) {
      return "¡Excelente pregunta! Tenemos promociones especiales regularmente. Te sugiero suscribirte a nuestro newsletter para estar al día. 🎉";
    }

    if (
      message.includes("instalacion") ||
      message.includes("instalar") ||
      message.includes("como usar")
    ) {
      return "Proporcionamos guías de instalación detalladas y soporte técnico para todos nuestros productos. ¿Necesitas ayuda con algún software en particular? 🛠️";
    }

    if (
      message.includes("contacto") ||
      message.includes("telefono") ||
      message.includes("email")
    ) {
      return "Puedes contactarnos a través de WhatsApp, nuestro formulario de contacto, o visita la sección de contacto en el sitio web. ¡Estamos aquí para ayudarte! 📞";
    }

    if (
      message.includes("horario") ||
      message.includes("atencion") ||
      message.includes("cuando")
    ) {
      return "Nuestro equipo está disponible de lunes a viernes de 9:00 AM a 6:00 PM. Para consultas urgentes, puedes usar WhatsApp. ⏰";
    }

    if (
      message.includes("licencia") ||
      message.includes("legal") ||
      message.includes("original")
    ) {
      return "Todos nuestros softwares son completamente legales y con licencias originales. Garantizamos la autenticidad de cada producto. ✅";
    }

    if (
      message.includes("hola") ||
      message.includes("buenos") ||
      message.includes("saludos")
    ) {
      return "¡Hola! 😊 Es un placer atenderte. ¿En qué puedo ayudarte hoy? Puedo ayudarte con información sobre nuestros productos, precios, o cualquier consulta que tengas.";
    }

    if (
      message.includes("gracias") ||
      message.includes("perfecto") ||
      message.includes("excelente")
    ) {
      return "¡De nada! Me alegra poder ayudarte. Si tienes más preguntas, no dudes en preguntarme. ¡Estoy aquí para eso! 😊";
    }

    if (
      message.includes("adios") ||
      message.includes("chao") ||
      message.includes("hasta luego")
    ) {
      return "¡Hasta luego! Que tengas un excelente día. Recuerda que estoy aquí cuando me necesites. 👋";
    }

    // Respuesta por defecto
    const defaultResponses = [
      "Interesante pregunta. ¿Podrías ser más específico? Estoy aquí para ayudarte con cualquier consulta sobre nuestros productos. 🤔",
      "Entiendo tu consulta. Te recomiendo revisar nuestro catálogo o contactar directamente a nuestro equipo de ventas para más información detallada. 📋",
      "¡Gracias por tu pregunta! Para brindarte la mejor respuesta, ¿podrías contarme más detalles sobre lo que necesitas? 💡",
      "Estoy aquí para ayudarte. Puedo asistirte con información sobre software, precios, instalación y más. ¿Qué te interesa saber? 🚀",
    ];

    return defaultResponses[
      Math.floor(Math.random() * defaultResponses.length)
    ];
  }

  console.log("Chatbot initialized successfully");
}

// Programacion para elmiinar el procuto del carrito / home
const ENDPOINT = BASE_DIR + "/includes/carrito_acciones.php";

// Función auxiliar para leer JSON de forma segura
async function fetchJSON(url, options = {}) {
  const res = await fetch(url, options);
  const text = await res.text();

  try {
    return { okHTTP: res.ok, json: JSON.parse(text), raw: text };
  } catch (e) {
    return { okHTTP: res.ok, json: null, raw: text, parseError: e };
  }
}

// Delegación de eventos: escucha clicks en botones .js-remove-item
document.addEventListener("click", async (ev) => {
  const btn = ev.target.closest(".js-remove-item");
  if (!btn) return; // No es un click en el botón de eliminar

  ev.preventDefault();

  // Obtener nombre del producto para confirmación
  const nombre =
    btn.getAttribute("aria-label")?.replace(/^Eliminar\s+/i, "") ||
    "este producto";
  if (!confirm(`¿Eliminar "${nombre}" del carrito?`)) return;

  // Obtener índice y/o id
  const index = btn.dataset.index;
  const id = btn.dataset.id || null;

  // Mostrar spinner mientras procesa
  const prevHTML = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<svg class="animate-spin xl:w-6 xl:h-6 w-4 h-4" viewBox="0 0 24 24">
    <circle cx="12" cy="12" r="10" stroke="currentColor" fill="none" stroke-width="4" opacity="0.25"></circle>
    <path d="M4 12a8 8 0 0 1 8-8" stroke="currentColor" stroke-width="4" fill="none" stroke-linecap="round"></path>
  </svg>`;

  try {
    // Preparar datos
    const fd = new FormData();
    if (id) {
      fd.append("action", "removeById");
      fd.append("id", id);
    } else {
      fd.append("action", "remove");
      fd.append("index", index);
    }

    // Petición AJAX
    const { okHTTP, json, raw, parseError } = await fetchJSON(ENDPOINT, {
      method: "POST",
      body: fd,
    });

    if (!okHTTP) {
      console.error("Error HTTP", raw);
      alert("Error en la comunicación con el servidor.");
      return;
    }
    if (!json) {
      console.error("Respuesta no es JSON", parseError, raw);
      alert("Respuesta inválida del servidor.");
      return;
    }
    if (!json.ok) {
      alert(json.msg || "No se pudo eliminar el producto.");
      return;
    }

    // Quitar producto del DOM
    const li = btn.closest("[data-item]");
    if (li) li.remove();

    // Actualizar total
    const totalEl = document.getElementById("totalCarrito");
    if (totalEl && json.total_formatted) {
      totalEl.textContent = json.total_formatted;
    }

    // Si carrito vacío, recargar
    if ((json.items ?? 0) === 0) {
      location.reload();
      return;
    }

    // Reindexar data-index de los botones visibles
    document.querySelectorAll("[data-item]").forEach((el, i) => {
      el.setAttribute("data-item", i);
      const delBtn = el.querySelector(".js-remove-item");
      if (delBtn) delBtn.dataset.index = i;
    });
  } catch (err) {
    console.error(err);
    alert("Ocurrió un error al eliminar el producto.");
  } finally {
    btn.disabled = false;
    btn.innerHTML = prevHTML;
  }
});

// Productos de la /tienda

// ===== Config =====

const ENDPOINT_LISTA = BASE_DIR + "/tienda/ajax_productos.php";
const GRID_ID = "productosGrid";
const PAG_ID = "paginacion";
const PAGE_SIZE = 12; // Debe coincidir con el backend

// Utilidades
const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

// Renderiza tarjetas en el cliente con data[]
function renderGridFromData(list = []) {
  const placeholder = "https://placehold.co/600x400/png";
  let html = "";

  if (!list.length) {
    html = `
      <div class="col-span-full">
        <div class="flex flex-col items-center justify-center gap-2 p-8 text-center bg-gray-50 border border-dashed border-gray-300 rounded-lg">
          <span class="text-3xl">🛍️</span>
          <p class="text-gray-600">No se encontraron productos con los filtros seleccionados.</p>
        </div>
      </div>`;
    return html;
  }

  for (const p of list) {
    const id = Number(p.id_producto || 0);
    const nombre = String(p.nombre || "Producto");
    const precio = Number(p.precio || 0).toFixed(2);
    const img = p.imagen ? BASE_DIR + `/uploads/${p.imagen}` : placeholder;

    html += `
    <div class="border border-gray-100 border-solid shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg p-3 sm:p-4 lg:p-6 flex flex-col gap-3 sm:gap-3 h-full">
      <div class="flex justify-end -mb-1">
        <button type="button" class="favorito-btn" data-id="${id}" aria-label="Marcar favorito">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor"
            class="w-7 h-7 sm:w-6 sm:h-6 transition-all duration-200 text-gray-600">
            <path stroke-linecap="round" stroke-linejoin="round"
              d="M6.75 3.75h10.5a.75.75 0 01.75.75v15.375a.375.375 0 01-.6.3L12 16.5l-5.4 3.675a.375.375 0 01-.6-.3V4.5a.75.75 0 01.75-.75z" />
          </svg>
        </button>
      </div>
      <img src="${img}" alt="${nombre.replace(
      /"/g,
      "&quot;"
    )}" loading="lazy" decoding="async"
           class="w-full h-40 sm:h-40 lg:h-48 object-cover rounded-md"
           onerror="this.onerror=null;this.src='${placeholder}';" />
      <p class="inline font-semibold text-sm sm:text-base lg:text-lg text-balance leading-tight uppercase">${nombre}</p>
      <p class="inline text-lg sm:text-xl lg:text-xl uppercase font-bold">USD ${precio}</p>
      <div class="flex flex-col gap-2 sm:gap-3 mt-auto">

      <form method="post">
                                                    <input type="hidden" name="id_producto"
                                                        value="${id}">
                                                    <button type="submit" name="agregar_carrito"
                                                        class="btn-secondary inline w-full py-1.5 sm:py-2 rounded-lg uppercase font-semibold text-sm sm:text-base">
                                                        Agregar al carrito
                                                    </button>
                                                </form>



       <button class="flex flex-row items-center justify-center gap-2 border border-gray-400 rounded-lg py-1.5 sm:py-2 uppercase font-semibold text-sm sm:text-base preview"
                data-id="${id}" 
                aria-label="Previsualizar ${nombre}">
          <div class="btn-secondary size-[24px] items-center flex rounded-full justify-center">
            <img src="/assets/icons/tienda/previsualizar.svg" alt="">
          </div>
          <p>Previsualizar</p>
        </button>
      </div>
    </div>`;
  }
  return html;
}

// Renderiza la paginación en el cliente
function renderPagination(total, currentPage) {
  const totalPages = Math.max(1, Math.ceil((Number(total) || 0) / PAGE_SIZE));
  const current = Math.max(1, Number(currentPage) || 1);

  let html = '<div class="flex items-center gap-2">';

  const disabledPrev =
    current <= 1 ? "disabled:opacity-50 disabled:cursor-not-allowed" : "";
  html += `
    <button data-page="${Math.max(1, current - 1)}"
      class="js-page-prev flex items-center justify-center px-2 sm:px-4 h-10 text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 hover:shadow-sm transition-all duration-200 group ${disabledPrev}" ${
    current <= 1 ? "disabled" : ""
  }>
      <svg class="w-4 h-4 sm:mr-2 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
      </svg>
      <span class="hidden sm:inline font-medium">Anterior</span>
    </button>`;

  html += '<div class="flex items-center bg-gray-50 rounded-xl p-1 gap-1">';

  const show = 3;
  const start = Math.max(1, current - 1);
  const end = Math.min(totalPages, start + show - 1);

  if (start > 1) {
    html += `<button data-page="1" class="flex items-center justify-center min-w-[40px] h-9 px-3 text-gray-700 bg-white rounded-lg hover:bg-gray-100 font-medium transition-all duration-200 border border-transparent hover:border-gray-200">1</button>`;
    if (start > 2) {
      html += `<div class="flex items-center px-2">
        <span class="w-1 h-1 bg-gray-400 rounded-full mx-0.5"></span>
        <span class="w-1 h-1 bg-gray-400 rounded-full mx-0.5"></span>
        <span class="w-1 h-1 bg-gray-400 rounded-full mx-0.5"></span>
      </div>`;
    }
  }

  for (let i = start; i <= end; i++) {
    if (i === current) {
      html += `<button class="relative flex items-center justify-center min-w-[40px] h-9 px-3 text-white btn-secondary rounded-lg font-semibold shadow-sm hover:shadow-md transition-all duration-200">${i}</button>`;
    } else {
      html += `<button data-page="${i}" class="flex items-center justify-center min-w-[40px] h-9 px-3 text-gray-700 bg-white rounded-lg hover:bg-gray-100 font-medium transition-all duration-200 border border-transparent hover:border-gray-200">${i}</button>`;
    }
  }

  if (end < totalPages) {
    if (end < totalPages - 1) {
      html += `<div class="flex items-center px-2">
        <span class="w-1 h-1 bg-gray-400 rounded-full mx-0.5"></span>
        <span class="w-1 h-1 bg-gray-400 rounded-full mx-0.5"></span>
        <span class="w-1 h-1 bg-gray-400 rounded-full mx-0.5"></span>
      </div>`;
    }
    html += `<button data-page="${totalPages}" class="flex items-center justify-center min-w-[40px] h-9 px-3 text-gray-700 bg-white rounded-lg hover:bg-gray-100 font-medium transition-all duration-200 border border-transparent hover:border-gray-200">${totalPages}</button>`;
  }

  html += "</div>";

  const disabledNext =
    current >= totalPages
      ? "disabled:opacity-50 disabled:cursor-not-allowed"
      : "";
  html += `
    <button data-page="${Math.min(totalPages, current + 1)}"
      class="js-page-next flex items-center justify-center px-2 sm:px-4 h-10 text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 hover:shadow-sm transition-all duration-200 group ${disabledNext}" ${
    current >= totalPages ? "disabled" : ""
  }>
      <span class="hidden sm:inline font-medium">Siguiente</span>
      <svg class="w-4 h-4 sm:ml-2 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
      </svg>
    </button>`;

  html += "</div>";
  return html;
}

async function cargarProductos({ page = 1 } = {}) {
  const grid = document.getElementById(GRID_ID);
  const pag = document.getElementById(PAG_ID);
  if (!grid || !pag) return;

  const params = new URLSearchParams();
  params.set("page", page);

  $$('input[name="marca[]"]:checked').forEach((chk) =>
    params.append("marca[]", chk.value)
  );
  $$('input[name="anio[]"]:checked').forEach((chk) =>
    params.append("anio[]", chk.value)
  );

  let res, text, data;
  try {
    res = await fetch(ENDPOINT_LISTA, {
      method: "POST",
      headers: { "X-Requested-With": "XMLHttpRequest" },
      body: params,
    });
    text = await res.text();
    data = JSON.parse(text);
  } catch (e) {
    console.error("Error o JSON inválido:", e, text);
    alert("Error al cargar productos.");
    return;
  }

  if (!res.ok || !data?.ok) {
    console.error("Respuesta de error:", data);
    alert(data?.msg || "No se pudo cargar la lista.");
    return;
  }

  // 1) Si el backend ya envía HTML, úsalo
  if (typeof data.grid_html === "string" && data.grid_html.trim() !== "") {
    grid.innerHTML = data.grid_html;
  } else {
    // 2) Si no envía HTML (tu caso), renderiza desde data[]
    const lista = Array.isArray(data.data) ? data.data : [];
    grid.innerHTML = renderGridFromData(lista);
  }

  // Paginación: usa la del server si existe; si no, calcula
  if (
    typeof data.pagination_html === "string" &&
    data.pagination_html.trim() !== ""
  ) {
    pag.innerHTML = data.pagination_html;
  } else {
    const total = Number(data.total || 0);
    const current = Number(data.page || page || 1);
    pag.innerHTML = renderPagination(total, current);
  }

  // Guarda crudos para otros usos (previsualizar, etc.)
  window.__productosCrudos = Array.isArray(data.data) ? data.data : [];
}

// Delegación para paginación
document.addEventListener("click", (ev) => {
  const btn = ev.target.closest("button[data-page]");
  if (!btn) return;
  const pag = document.getElementById(PAG_ID);
  if (!pag || !pag.contains(btn)) return;

  ev.preventDefault();
  const page = parseInt(btn.getAttribute("data-page"), 10) || 1;
  cargarProductos({ page });
});

// Escuchar cambios en filtros
document.addEventListener("change", (ev) => {
  if (ev.target.matches('input[name="marca[]"], input[name="anio[]"]')) {
    cargarProductos({ page: 1 });
  }
});

// Primera carga
document.addEventListener("DOMContentLoaded", () =>
  cargarProductos({ page: 1 })
);

// Funcion envio tipo ajax para el form de contactenos

(function () {
  const $ = (sel) => document.querySelector(sel);

  const form = $("#contactoForm");
  const btn = $("#btnEnviar");
  const boxMsg = $("#contactoMsg");

  const showMsg = (html, ok = false) => {
    boxMsg.innerHTML = html;
    boxMsg.className =
      "mt-4 text-sm " +
      (ok
        ? "text-green-700 bg-green-100 border border-green-300 rounded p-3"
        : "text-red-700 bg-red-100 border border-red-300 rounded p-3");
  };

  const validate = () => {
    const nombre = $("#nombre_completo").value.trim();
    const pais = $("#pais").value.trim();
    const email = $("#email").value.trim();
    const mensaje = $("#mensaje").value.trim();
    const tel = $("#telefono").value.trim();

    if (!nombre || nombre.length < 2) return "Ingresa tu nombre completo.";
    if (!pais) return "Selecciona un país.";
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))
      return "Ingresa un correo válido.";
    if (!mensaje || mensaje.length < 5)
      return "Escribe un mensaje más detallado.";
    if (tel && !/^[0-9+\-\s()]{6,20}$/.test(tel)) return "Teléfono inválido.";

    return null; // ok
  };

  form.addEventListener("submit", async (ev) => {
    ev.preventDefault();

    // Validación cliente
    const err = validate();
    if (err) {
      showMsg(err, false);
      return;
    }

    // Honeypot (si el bot lo llena, no enviamos)
    const hp = document.getElementById("hp_field").value;
    if (hp) {
      showMsg("Error de validación.", false);
      return;
    }

    // Preparar FormData
    const fd = new FormData(form);
    fd.append("ajax", "1"); // pista para el servidor

    btn.disabled = true;
    btn.classList.add("opacity-60", "cursor-not-allowed");
    showMsg("Enviando...", true);

    try {
      const res = await fetch(BASE_DIR + "contacto_enviar.php", {
        method: "POST",
        body: fd,
      });
      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        data = null;
      }

      if (!res.ok || !data || data.ok !== true) {
        console.error("Respuesta servidor:", text);
        showMsg(
          data?.msg || "No se pudo enviar el mensaje. Intenta nuevamente.",
          false
        );
      } else {
        showMsg("¡Gracias! Tu mensaje fue enviado correctamente.", true);
        form.reset();
      }
    } catch (e) {
      console.error(e);
      showMsg("Error de red. Verifica tu conexión.", false);
    } finally {
      btn.disabled = false;
      btn.classList.remove("opacity-60", "cursor-not-allowed");
    }
  });
})();
