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

  // Products carousel
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

document.getElementById("login-form").addEventListener("submit", function (e) {
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

document
  .getElementById("register-form")
  .addEventListener("submit", async (e) => {
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

document.getElementById("logoutModalBtn").addEventListener("click", () => {
  document.getElementById("logoutModal").classList.remove("hidden");
  document.getElementById("logoutModal").classList.add("flex");
});

document.getElementById("cancelLogout").addEventListener("click", () => {
  document.getElementById("logoutModal").classList.add("hidden");
  document.getElementById("logoutModal").classList.remove("flex");
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
