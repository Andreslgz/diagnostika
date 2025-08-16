window.BASE_DIR = 'https://diagnostika:8890';

// =====================================================
// UTILIDADES GENERALES (FIX okHTTP + parseo tolerante)
// =====================================================
(() => {
  // Base URL robusta
  const getBase = () =>
    (typeof window !== "undefined" && window.BASE_DIR) ||
    (typeof BASE_DIR !== "undefined" && BASE_DIR) ||
    "";

  const baseNorm = () => (getBase() ? getBase().replace(/\/$/, "") : "");

  // Parseo tolerante a HTML alrededor del JSON
  const parseJSONSafe = (text) => {
    try { return JSON.parse(text); } catch (_) {}
    const s = text.indexOf("{"), e = text.lastIndexOf("}");
    if (s >= 0 && e >= s) return JSON.parse(text.slice(s, e + 1));
    return null; // <-- en vez de lanzar, devolvemos null (el caller decide)
  };

  // Fetch con respuesta JSON tolerante
  async function fetchJSON(url, options = {}) {
    const res  = await fetch(url, options);
    const text = await res.text();
    const json = parseJSONSafe(text);
    // Devolvemos ambas claves para que cualquier caller funcione
    return { okHTTP: res.ok, ok: res.ok, json, raw: text, res };
  }

  const fmtUSD = (n) => {
    const num = parseFloat(n);
    return "USD " + (isNaN(num) ? "0.00" : num.toFixed(2));
  };

  // Exponer utilidades
  window.App = window.App || {};
  Object.assign(window.App, { getBase, baseNorm, parseJSONSafe, fetchJSON, fmtUSD });
})();



// =====================================================
// TABS / MODAL (LOGIN / REGISTER)
// =====================================================
(() => {
  document.addEventListener("DOMContentLoaded", () => {
    const modalBody     = document.getElementById("modal-body");
    const registerTab   = document.getElementById("register-tab");
    const loginTab      = document.getElementById("login-tab");
    const registerPanel = document.getElementById("register");
    const loginPanel    = document.getElementById("login");

    const resetTab = (tab) => {
      if (!tab) return;
      tab.className =
        "inline-block w-full p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 tab-button-transition";
      tab.setAttribute("aria-selected", "false");
    };
    const activateTab = (tab) => {
      if (!tab) return;
      tab.className =
        "inline-block w-full p-4 border-b-2 border-amber-500 rounded-t-lg text-amber-600 tab-button-transition";
      tab.setAttribute("aria-selected", "true");
    };

    const showLoginTab = () => {
      resetTab(registerTab);  activateTab(loginTab);
      if (registerPanel && loginPanel) {
        registerPanel.className = "hidden flex-col justify-center h-full tab-content-transition";
        loginPanel.className    = "flex flex-col justify-center h-full tab-content-transition";
        loginPanel.style.opacity = "1";
      }
    };
    const showRegisterTab = () => {
      resetTab(loginTab); activateTab(registerTab);
      if (registerPanel && loginPanel) {
        loginPanel.className    = "hidden flex-col justify-center h-full tab-content-transition";
        registerPanel.className = "flex flex-col justify-center h-full tab-content-transition";
        registerPanel.style.opacity = "1";
      }
    };

    // Abrir modal apuntando a tab específica
    document.querySelectorAll('[data-modal-target="authentication-modal"]').forEach((btn) => {
      btn.addEventListener("click", () => {
        const target = btn.getAttribute("data-active-tab");
        setTimeout(() => {
          if (!registerTab || !loginTab || !registerPanel || !loginPanel || !modalBody) return;
          if (target === "register") showRegisterTab();
          else if (target === "login") showLoginTab();
          setTimeout(() => {
            modalBody.classList.remove("modal-content-hidden");
            modalBody.classList.add("modal-content-visible");
          }, 10);
        }, 10);
      });
    });

    // Click directo en tabs
    loginTab?.addEventListener("click", (e) => { e.preventDefault(); showLoginTab(); });
    registerTab?.addEventListener("click", (e) => { e.preventDefault(); showRegisterTab(); });

    // Switch dentro de formularios
    document.getElementById("switch-to-register")?.addEventListener("click", (e) => {
      e.preventDefault(); showRegisterTab();
    });
    document.getElementById("switch-to-login")?.addEventListener("click", (e) => {
      e.preventDefault(); showLoginTab();
    });

    // Reset visual al cerrar modal
    document.getElementById("authentication-modal")?.addEventListener("hidden.bs.modal", () => {
      modalBody?.classList.remove("modal-content-visible");
      modalBody?.classList.add("modal-content-hidden");
    });
  });
})();



// =====================================================
// LOGIN / REGISTER (AJAX)
// =====================================================
(() => {
  // LOGIN
  document.addEventListener("DOMContentLoaded", () => {
    const loginFormEl = document.getElementById("login-form");
    const errorBox    = document.getElementById("login-error");
    const showErr = (msg) => { if (!errorBox) return; errorBox.textContent = msg; errorBox.classList.remove("hidden"); };
    const hideErr = () => errorBox?.classList.add("hidden");
    if (!loginFormEl) return;

    loginFormEl.addEventListener("submit", async (e) => {
      e.preventDefault();
      const endpoint = (App.baseNorm() || "") + "/login.php";
      const formData = new FormData(loginFormEl);
      const submitBtn = loginFormEl.querySelector('[type="submit"]');
      if (submitBtn) submitBtn.disabled = true;

      try {
        const res = await fetch(endpoint, {
          method: "POST",
          body: formData,
          credentials: "same-origin",
          headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
          cache: "no-store"
        });
        if (res.redirected) { window.location.href = res.url; return; }

        const text = await res.text();
        if (!res.ok) throw new Error(`HTTP ${res.status}: ${text.slice(0,200)}`);
        const data = App.parseJSONSafe(text);

        if (data?.success) {
          hideErr();
          if (data.redirect) window.location.href = data.redirect; else location.reload();
        } else {
          showErr(data?.message || "Correo o contraseña incorrectos.");
        }
      } catch (err) {
        console.error(err);
        showErr("Error de conexión con el servidor.");
      } finally {
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  });

  // REGISTER
  const showError = (msg) => {
    const e = document.getElementById("register-error");
    const s = document.getElementById("register-success");
    if (!e || !s) return;
    s.classList.add("hidden"); e.textContent = msg; e.classList.remove("hidden");
  };
  const showSuccess = (msg) => {
    const e = document.getElementById("register-error");
    const s = document.getElementById("register-success");
    if (!e || !s) return;
    e.classList.add("hidden"); s.textContent = msg; s.classList.remove("hidden");
  };

  const registerFormEl = document.getElementById("register-form");
  if (registerFormEl) {
    registerFormEl.addEventListener("submit", async (e) => {
      e.preventDefault();
      const form = e.target;
      const pwd  = form.querySelector('input[name="password"]').value.trim();
      const pwd2 = form.querySelector('input[name="password_confirm"]').value.trim();
      if (pwd !== pwd2) { showError("Las contraseñas no coinciden."); return; }

      const endpoint = (App.baseNorm() || "") + "/register.php";
      const submitBtn = form.querySelector('[type="submit"]'); submitBtn && (submitBtn.disabled = true);
      try {
        const res = await fetch(endpoint, {
          method: "POST",
          body: new FormData(form),
          credentials: "same-origin",
          headers: { "Accept":"application/json", "X-Requested-With":"XMLHttpRequest" },
          cache: "no-store"
        });

        if (res.redirected) { window.location.href = res.url; return; }

        const text = await res.text();
        if (!res.ok) throw new Error(`HTTP ${res.status}: ${text.slice(0,200)}`);
        const data = App.parseJSONSafe(text);

        if (data.success) {
          showSuccess(data.message || "¡Registro exitoso!");
          if (data.redirect) window.location.href = data.redirect; else form.reset();
        } else {
          showError(data.message || "No pudimos crear tu cuenta.");
        }
      } catch (err) {
        console.error(err);
        showError("Ocurrió un error: " + err.message);
      } finally {
        if (submitBtn) submitBtn.disabled = false;
      }
    });
  }
})();



// =====================================================
// LOGOUT MODAL
// =====================================================
(() => {
  const logoutBtnEl   = document.getElementById("logoutModalBtn");
  const cancelLogoutEl= document.getElementById("cancelLogout");

  logoutBtnEl?.addEventListener("click", () => {
    const modal = document.getElementById("logoutModal");
    if (!modal) return;
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  });

  cancelLogoutEl?.addEventListener("click", () => {
    const modal = document.getElementById("logoutModal");
    if (!modal) return;
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  });
})();



// =====================================================
// FAVORITOS (AJAX) + ALERTA
// =====================================================
(() => {
  function mostrarAlerta(mensaje) {
    const alerta = document.getElementById("alertaFavorito");
    const texto  = document.getElementById("alertaTexto");
    if (!alerta || !texto) { alert(mensaje); return; }
    texto.textContent = mensaje;
    alerta.classList.remove("hidden"); alerta.classList.add("flex");
    setTimeout(() => { alerta.classList.add("hidden"); alerta.classList.remove("flex"); }, 3000);
  }
  window.mostrarAlerta = window.mostrarAlerta || mostrarAlerta;

  document.addEventListener("click", (e) => {
    const btn = e.target.closest(".favorito-btn");
    if (!btn) return;
    e.preventDefault();

    const id = btn.dataset.id;
    const svg = btn.querySelector("svg");
    if (!id) { console.error("favorito-btn sin data-id"); return; }

    const endpoint = (App.baseNorm() || "") + "/ajax_favorito.php";

    fetch(endpoint, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: new URLSearchParams({ id_producto: id }).toString(),
      cache: "no-store",
      credentials: "same-origin",
    })
    .then(r => r.text())
    .then(App.parseJSONSafe)
    .then((data) => {
      if (data?.auth === false) return mostrarAlerta("Debes iniciar sesión para agregar productos a favoritos.");
      if (data?.success) {
        const marcar = !!data.favorito;
        if (svg) {
          svg.setAttribute("fill", marcar ? "red" : "none");
          svg.classList.toggle("text-red-600", marcar);
          svg.classList.toggle("text-black", !marcar);
        }
        btn.classList.toggle("is-fav", marcar);
      } else {
        mostrarAlerta(data?.message || "Ocurrió un error.");
      }
    })
    .catch((err) => {
      console.error(err);
      mostrarAlerta("Error: " + err.message);
    });
  });
})();



// =====================================================
// CARRITO: REFRESH MINI-CART + ORDER SUMMARY
// =====================================================
(() => {
  async function refreshMiniCart() {
    // 1) Items del mini-carrito (AJUSTA ruta si tu archivo está en otra carpeta)
    try {
      const resItems = await fetch('/tienda/mini_cart_html.php', {
        method: 'GET',
        credentials: 'same-origin',
        cache: 'no-store'
      });
      const html = await resItems.text();
      const itemsContainer = document.getElementById('mini-cart-items') || document.getElementById('mini-cart');
      if (itemsContainer) itemsContainer.innerHTML = html;
    } catch (e) {
      console.error('Error refrescando items:', e);
    }

    // 2) Order Summary (totales)
    try {
      const { ok, json, raw } = await App.fetchJSON('/includes/carrito_acciones.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({ action: 'summary' }).toString(),
        cache: 'no-store'
      });
      if (!ok || !json?.success) throw new Error(json?.message || raw);

      const elSubtotal  = document.getElementById('subtotalAmount');
      const elDiscounts = document.getElementById('discountsAppliedAmount');
      const elVoucher   = document.getElementById('voucherDiscountAmount');
      const elTotal     = document.getElementById('cart-total');

      elSubtotal  && (elSubtotal.textContent  = App.fmtUSD(json.subtotal));
      elDiscounts && (elDiscounts.textContent = '- ' + App.fmtUSD(json.discounts_applied));
      elVoucher   && (elVoucher.textContent   = '- ' + App.fmtUSD(json.voucher_discount));
      elTotal     && (elTotal.textContent     = App.fmtUSD(json.total));

      const badge = document.getElementById('cart-count');
      if (badge && typeof json.cart_count !== 'undefined') {
        badge.textContent = json.cart_count;
        badge.classList.toggle('hidden', json.cart_count <= 0);
      }
    } catch (e) {
      console.error('Error actualizando Order Summary:', e);
    }
  }
  window.refreshMiniCart = refreshMiniCart;

  // Al cargar
  document.addEventListener('DOMContentLoaded', () => { refreshMiniCart(); });

  // Añadir al carrito (botón .add-to-cart)
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".add-to-cart");
    if (!btn) return;
    e.preventDefault();
    const id  = btn.dataset.id;
    const qty = parseInt(btn.dataset.qty || "1", 10) || 1;
    if (!id) { console.error("add-to-cart sin data-id"); return; }

    const endpoint = (App.baseNorm() || "") + "/tienda/ajax_carrito.php";
    btn.disabled = true;
    try {
      const res = await fetch(endpoint, {
        method: "POST",
        credentials: "same-origin",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
          "Accept": "application/json",
          "X-Requested-With": "XMLHttpRequest"
        },
        body: new URLSearchParams({ id_producto: id, cantidad: String(qty) }).toString(),
        cache: "no-store"
      });
      const text = await res.text();
      if (!res.ok) throw new Error(`HTTP ${res.status}: ${text.slice(0,200)}`);
      const data = App.parseJSONSafe(text);

      if (data?.success) {
        const badge = document.getElementById("cart-count");
        if (badge && typeof data.cart_count !== "undefined") {
          badge.textContent = data.cart_count; badge.classList.remove("hidden");
        }
        await refreshMiniCart();
        (typeof mostrarAlerta === "function")
          ? mostrarAlerta(data.message || "Producto añadido al carrito")
          : console.log(data.message || "Producto añadido al carrito");
      } else {
        const msg = data?.message || "No se pudo añadir al carrito.";
        (typeof mostrarAlerta === "function") ? mostrarAlerta(msg) : alert(msg);
      }
    } catch (err) {
      console.error(err);
      (typeof mostrarAlerta === "function")
        ? mostrarAlerta("Error de conexión con el servidor.")
        : alert("Error de conexión con el servidor.");
    } finally {
      btn.disabled = false;
    }
  });

  // Actualizar cantidad (desde tus botones ± que llaman updateQuantity)
  window.updateQuantity = async function updateQuantity(index, delta) {
    const endpoint = (App.baseNorm() || "") + "/tienda/ajax_carrito_update.php";
    try {
      const res = await fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({ index: String(index), delta: String(delta) }).toString(),
        cache: 'no-store'
      });
      const text = await res.text();
      if (!res.ok) throw new Error(`HTTP ${res.status}: ${text.slice(0,200)}`);

      const data = App.parseJSONSafe(text);
      if (!data?.success) {
        (typeof mostrarAlerta === "function") ? mostrarAlerta(data?.message || 'No se pudo actualizar')
          : alert(data?.message || 'No se pudo actualizar');
        return;
      }

      if (data.item_removed) {
        document.querySelector(`li[data-item="${index}"]`)?.remove();
      } else {
        const qtySpan = document.getElementById(`qty-${index}`) ||
                        document.querySelector(`li[data-item="${index}"] [data-qty]`);
        qtySpan && (qtySpan.textContent = data.new_qty);
        const subSpan = document.getElementById(`subtotal-${index}`) ||
                        document.querySelector(`li[data-item="${index}"] [data-subtotal]`);
        subSpan && (subSpan.textContent = `USD. ${Number(data.item_subtotal).toFixed(2)}`);
      }

      const totalEl = document.getElementById('totalCarrito');
      totalEl && (totalEl.textContent = `$${Number(data.cart_total).toFixed(2)}`);

      const badge = document.getElementById("cart-count");
      if (badge && typeof data.cart_count !== "undefined") {
        badge.textContent = data.cart_count;
        badge.classList.toggle("hidden", data.cart_count <= 0);
      }

      await refreshMiniCart();
    } catch (err) {
      console.error(err);
      (typeof mostrarAlerta === "function") ? mostrarAlerta("Error de conexión con el servidor.")
        : alert("Error de conexión con el servidor.");
    }
  };

  // Eliminar ítem (delegación click en .js-remove-item)
document.addEventListener("click", async (e) => {
  const removeBtn = e.target.closest(".js-remove-item");
  if (!removeBtn) return;
  e.preventDefault();

  const index = Number(removeBtn.dataset.index);
  if (!Number.isInteger(index) || index < 0) {
    console.error("remove: data-index inválido:", removeBtn.dataset.index);
    return;
  }

  const base =
    (typeof App !== "undefined" && App.baseNorm && App.baseNorm()) ||
    (typeof window !== "undefined" && window.BASE_DIR && window.BASE_DIR.replace(/\/$/, "")) ||
    (typeof BASE_DIR !== "undefined" && BASE_DIR && BASE_DIR.replace(/\/$/, "")) ||
    "";
  const endpoint = base + "/includes/carrito_acciones.php";

  removeBtn.disabled = true;
  try {
    const { okHTTP, json, raw } = await App.fetchJSON(endpoint, {
      method: "POST",
      credentials: "same-origin",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: new URLSearchParams({ action: "remove", index: String(index) }).toString(),
      cache: "no-store",
    });

    // Errores reales de red/HTTP
    if (!okHTTP) {
      (typeof mostrarAlerta === "function")
        ? mostrarAlerta("HTTP error: " + String(raw).slice(0, 200))
        : alert("HTTP error: " + String(raw).slice(0, 200));
      return;
    }

    // Respuesta debe ser JSON válido
    if (!json) {
      (typeof mostrarAlerta === "function")
        ? mostrarAlerta("Respuesta no-JSON del servidor.")
        : alert("Respuesta no-JSON del servidor.");
      return;
    }

    // Si el backend avisa fallo lógico
    if (json.success !== true) {
      (typeof mostrarAlerta === "function")
        ? mostrarAlerta(json.message || "No se pudo eliminar el producto.")
        : alert(json.message || "No se pudo eliminar el producto.");
      return;
    }

    // ÉXITO: actualiza UI sin lanzar excepciones
    const li = removeBtn.closest('li[data-item]');
    if (li) li.remove();

    if (typeof refreshMiniCart === "function") {
      await refreshMiniCart(); // también actualiza totales/order summary si tu función lo hace
    }

    if (typeof json.cart_count !== "undefined") {
      const badge = document.getElementById("cart-count");
      if (badge) {
        badge.textContent = json.cart_count;
        badge.classList.toggle("hidden", json.cart_count <= 0);
      }
    }
  } catch (err) {
    console.error(err);
    (typeof mostrarAlerta === "function")
      ? mostrarAlerta("Error al eliminar: " + err.message)
      : alert("Error al eliminar: " + err.message);
  } finally {
    removeBtn.disabled = false;
  }
});

  // Auto-hide cart alert
  document.addEventListener("DOMContentLoaded", () => {
    const alertCarrito = document.getElementById("alertCarrito");
    if (!alertCarrito) return;
    setTimeout(() => {
      alertCarrito.style.opacity = "0";
      alertCarrito.style.transform = "translateX(100%)";
      setTimeout(() => { alertCarrito.remove(); }, 300);
    }, 4000);
    alertCarrito.addEventListener("click", function () {
      this.style.opacity = "0"; this.style.transform = "translateX(100%)";
      setTimeout(() => { this.remove(); }, 300);
    });
  });
})();



// =====================================================
// SPLIDE + FILTRO DE MARCAS (HOME)
// =====================================================
(() => {
  // Reemplaza slides y refresca Splide
  function replaceSlidesAndRefresh(html) {
    const list = document.getElementById('productos-lista');
    if (!list) return;
    list.innerHTML = html;

    if (window.splideInstance) {
      try {
        window.splideInstance.refresh();
        window.splideInstance.go(0);
        window.splideInstance.Components.Layout?.reposition?.();
        window.dispatchEvent(new Event('resize'));
      } catch (e) {
        console.error("Error refresh Splide, remontando...", e);
        try { window.splideInstance.destroy(true); } catch(_) {}
        window.splideInstance = new Splide('#products-carousel', window.__splideOptions || {}).mount();
        window.splideInstance.go(0);
      }
    }
  }
  window.replaceSlidesAndRefresh = replaceSlidesAndRefresh;

  // Init Splide
  document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('products-carousel');
    if (!el) return;
    const options = {
      type: 'slide',
      perPage: 4,
      gap: '1rem',
      pagination: false,
      arrows: true,
      breakpoints: { 1024:{perPage:3}, 768:{perPage:2}, 480:{perPage:1} },
    };
    window.__splideOptions = options;
    window.splideInstance  = new Splide('#products-carousel', options).mount();
  });

  // Click en logos de marca
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".brand-tile");
    if (!btn) return;
    const marca = btn.dataset.brand;
    if (!marca) return;

    // Marca activo en UI
    document.querySelectorAll('.brand-tile').forEach(el => el.classList.remove('ring-2','ring-amber-500'));
    btn.classList.add('ring-2','ring-amber-500');

    try {
      // Endpoint en raíz (ajusta si tu archivo vive en otra ruta)
      const res = await fetch("/ajax_productos.php", {
        method: "POST",
        headers: {
          "Content-Type":"application/x-www-form-urlencoded; charset=UTF-8",
          "Accept":"text/html",
          "X-Requested-With":"XMLHttpRequest"
        },
        body: new URLSearchParams({ marca }).toString(),
        cache: "no-store"
      });
      const html = await res.text();
      if (!res.ok) throw new Error(html.slice(0,200));
      replaceSlidesAndRefresh(html);
    } catch (err) {
      console.error(err);
      alert("No se pudieron cargar productos para " + marca);
    }
  });
})();



// =====================================================
// CHIPS DE FILTROS (marca[] / anio[])
// =====================================================
(() => {
  const debounce = (fn, ms = 100) => { let t; return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); }; };
  const escHTML = (s) => String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  const getChecked = (name) => [...document.querySelectorAll(`input[name="${name}[]"]:checked`)].map(el => el.value);

  function renderActiveFilters() {
    const container = document.getElementById('active-filters');
    if (!container) return;
    const marcas = getChecked('marca');
    const anios  = getChecked('anio');

    let html = '';
    for (const v of marcas) {
      html += `
        <div class="bg-gray-200 px-2 py-1 text-sm rounded-md flex items-center gap-2 cursor-pointer hover:bg-gray-300"
             data-type="marca" data-value="${escHTML(v)}" title="Quitar filtro">
          <p>${escHTML(v)}</p>
          <svg class="w-4 h-4 text-gray-800" viewBox="0 0 24 24" fill="none">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18 17.94 6M18 18 6.06 6"/>
          </svg>
        </div>`;
    }
    for (const v of anios) {
      html += `
        <div class="bg-gray-200 px-2 py-1 text-sm rounded-md flex items-center gap-2 cursor-pointer hover:bg-gray-300"
             data-type="anio" data-value="${escHTML(v)}" title="Quitar filtro">
          <p>${escHTML(v)}</p>
          <svg class="w-4 h-4 text-gray-800" viewBox="0 0 24 24" fill="none">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18 17.94 6M18 18 6.06 6"/>
          </svg>
        </div>`;
    }
    if (marcas.length + anios.length > 0) {
      html += `
        <button type="button" id="clear-all-filters"
          class="bg-gray-800 text-white px-3 py-1 text-xs rounded-md hover:brightness-110">
          Clear all
        </button>`;
    }
    container.innerHTML = html;
  }

  const applyFilters = debounce(() => {
    renderActiveFilters();
    // TODO: si tienes recarga AJAX de productos por filtros, invócala aquí
    // ej: cargarProductos({ page: 1 });
  }, 80);

  document.addEventListener('click', (e) => {
    const chip = e.target.closest('#active-filters [data-type][data-value]');
    if (chip) {
      const type = chip.getAttribute('data-type');
      const value= chip.getAttribute('data-value');
      const sel = `input[name="${type}[]"][value="${CSS?.escape ? CSS.escape(value) : value.replace(/"/g,'\\"')}"]`;
      const input = document.querySelector(sel);
      if (input && input.checked) {
        input.checked = false;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
      return;
    }
    if (e.target.closest('#clear-all-filters')) {
      document.querySelectorAll('input[name="marca[]"]:checked, input[name="anio[]"]:checked').forEach(el => {
        el.checked = false;
        el.dispatchEvent(new Event('change', { bubbles: true }));
      });
    }
  });

  document.addEventListener('change', (e) => {
    if (e.target.matches('input[name="marca[]"], input[name="anio[]"]')) {
      applyFilters();
    }
  });

  document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', renderActiveFilters)
    : renderActiveFilters();
})();



// =====================================================
// TIENDA: GRID + PAGINACIÓN (AJAX)
// =====================================================
(() => {
  // ================================
  // Config
  // ================================
  const ENDPOINT_LISTA = (window.BASE_DIR || window.BASE_DIR === '' ? window.BASE_DIR : (typeof BASE_DIR !== 'undefined' ? BASE_DIR : '')) + "/tienda/ajax_productos.php";
  const GRID_ID = "productosGrid";
  const PAG_ID  = "paginacion";
  const PAGE_SIZE = 12;

  // ================================
  // Helpers favoritos (expuestos)
  // ================================
  function setFavorites(favs) {
    window.__favorites = Array.isArray(favs) ? favs.map(n => Number(n)) : [];
  }
  function applyFavoritesInDOM(root = document) {
    const favs = Array.isArray(window.__favorites) ? window.__favorites : [];
    root.querySelectorAll('.favorito-btn[data-id]').forEach(btn => {
      const id  = Number(btn.dataset.id || 0);
      const svg = btn.querySelector('svg');
      const isFav = favs.includes(id);
      if (svg) {
        svg.setAttribute('fill', isFav ? 'currentColor' : 'none');
        svg.classList.toggle('text-red-600', isFav);
        svg.classList.toggle('text-gray-600', !isFav);
      }
      btn.classList.toggle('is-fav', isFav);
      btn.setAttribute('aria-label', isFav ? 'Quitar de favoritos' : 'Marcar favorito');
    });
  }
  // Hazlos accesibles si quieres usarlos desde otro módulo:
  window.setFavorites = setFavorites;
  window.applyFavoritesInDOM = applyFavoritesInDOM;

  // ================================
  // Utilidades DOM
  // ================================
  const $  = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  // ================================
  // Render del grid (cliente)
  // ================================
  function renderGridFromData(list = []) {
    const placeholder = "https://placehold.co/600x400/png";
    if (!list.length) {
      return `
      <div class="col-span-full">
        <div class="flex flex-col items-center justify-center gap-2 p-8 text-center bg-gray-50 border border-dashed border-gray-300 rounded-lg">
          <span class="text-3xl">🛍️</span>
          <p class="text-gray-600">No se encontraron productos con los filtros seleccionados.</p>
        </div>
      </div>`;
    }

    const favs = Array.isArray(window.__favorites) ? window.__favorites : [];
    let html = "";

    for (const p of list) {
      const id    = Number(p.id_producto || 0);
      const name  = String(p.nombre || "Producto");
      const price = Number(p.precio || 0).toFixed(2);
      const img   = p.imagen ? (window.BASE_DIR || "") + `/uploads/${p.imagen}` : placeholder;

      const isFav = favs.includes(id);

      html += `
      <div class="border border-gray-100 border-solid shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg p-3 sm:p-4 lg:p-6 flex flex-col gap-3 sm:gap-3 h-full">
        <div class="flex justify-end -mb-1">
          <button type="button" class="favorito-btn ${isFav ? 'is-fav' : ''}" data-id="${id}" aria-label="${isFav ? 'Quitar de favoritos' : 'Marcar favorito'}">
            <svg xmlns="http://www.w3.org/2000/svg"
                 fill="${isFav ? 'currentColor' : 'none'}"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="w-5 h-5 sm:w-6 sm:h-6 transition-all duration-200 ${isFav ? 'text-red-600' : 'text-gray-600'}">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3.75h10.5a.75.75 0 01.75.75v15.375a.375.375 0 01-.6.3L12 16.5l-5.4 3.675a.375.375 0 01-.6-.3V4.5a.75.75 0 01.75-.75z" />
            </svg>
          </button>
        </div>

        <img src="${img}" alt="${name.replace(/"/g, "&quot;")}" loading="lazy" decoding="async"
             class="w-full h-40 sm:h-40 lg:h-48 object-cover rounded-md"
             onerror="this.onerror=null;this.src='${placeholder}';" />

        <p class="inline font-semibold text-sm sm:text-base lg:text-lg text-balance leading-tight uppercase">${name}</p>
        <p class="inline text-lg sm:text-xl lg:text-xl uppercase font-bold">USD ${price}</p>

        <div class="flex flex-col gap-2 sm:gap-3 mt-auto">
          <button type="button"
                  class="btn-secondary inline w-full py-1.5 sm:py-2 rounded-lg uppercase font-semibold text-sm sm:text-base add-to-cart"
                  data-id="${id}" data-qty="1">BUY NOW</button>

          <button class="flex flex-row items-center justify-center gap-2 border border-gray-400 rounded-lg py-1.5 sm:py-2 uppercase font-semibold text-sm sm:text-base preview"
                  data-id="${id}" aria-label="Previsualizar ${name}">
            <div class="btn-secondary size-[24px] items-center flex rounded-full justify-center">
              <img src="/assets/icons/tienda/previsualizar.svg" alt="">
            </div>
            <p>PREVIEW</p>
          </button>
        </div>
      </div>`;
    }
    return html;
  }

  // ================================
  // Render paginación (cliente)
  // ================================
  function renderPagination(total, currentPage) {
    const totalPages = Math.max(1, Math.ceil((Number(total) || 0) / PAGE_SIZE));
    const current    = Math.max(1, Number(currentPage) || 1);
    let html = '<div class="flex items-center gap-2">';

    const disPrev = current <= 1 ? "disabled:opacity-50 disabled:cursor-not-allowed" : "";
    html += `
      <button data-page="${Math.max(1, current - 1)}"
        class="js-page-prev flex items-center justify-center px-2 sm:px-4 h-10 text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 hover:shadow-sm transition-all duration-200 group ${disPrev}" ${current <= 1 ? "disabled" : ""}>
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
      html += (i === current)
        ? `<button class="relative flex items-center justify-center min-w-[40px] h-9 px-3 text-white btn-secondary rounded-lg font-semibold shadow-sm hover:shadow-md transition-all duration-200">${i}</button>`
        : `<button data-page="${i}" class="flex items-center justify-center min-w-[40px] h-9 px-3 text-gray-700 bg-white rounded-lg hover:bg-gray-100 font-medium transition-all duration-200 border border-transparent hover:border-gray-200">${i}</button>`;
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

    const disNext = current >= totalPages ? "disabled:opacity-50 disabled:cursor-not-allowed" : "";
    html += `
      <button data-page="${Math.min(totalPages, current + 1)}"
        class="js-page-next flex items-center justify-center px-2 sm:px-4 h-10 text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 hover:shadow-sm transition-all duration-200 group ${disNext}" ${current >= totalPages ? "disabled" : ""}>
        <span class="hidden sm:inline font-medium">Siguiente</span>
        <svg class="w-4 h-4 sm:ml-2 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </button>`;
    html += "</div>";
    return html;
  }

  // ================================
  // Cargar productos (trae y pinta)
  // ================================
  async function cargarProductos({ page = 1 } = {}) {
    const grid = document.getElementById(GRID_ID);
    const pag  = document.getElementById(PAG_ID);
    if (!grid || !pag) return;

    const params = new URLSearchParams();
    params.set("page", page);

    $$('input[name="marca[]"]:checked').forEach(chk => params.append("marca[]", chk.value));
    $$('input[name="anio[]"]:checked').forEach(chk  => params.append("anio[]",  chk.value));

    let res, text, data;
    try {
      res  = await fetch(ENDPOINT_LISTA, {
        method: "POST",
        headers: { "X-Requested-With": "XMLHttpRequest" },
        body: params
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

    // 1) Setear favoritos del payload ANTES de renderizar
    setFavorites(data.favorites);

    // 2) Render del grid (si viene HTML del server, aplicar favoritos al DOM)
    if (typeof data.grid_html === "string" && data.grid_html.trim() !== "") {
      grid.innerHTML = data.grid_html;
      applyFavoritesInDOM(grid);
    } else {
      grid.innerHTML = renderGridFromData(Array.isArray(data.data) ? data.data : []);
      // (aquí no hace falta applyFavoritesInDOM, ya que el render usó __favorites)
    }

    // 3) Paginación
    if (typeof data.pagination_html === "string" && data.pagination_html.trim() !== "") {
      pag.innerHTML = data.pagination_html;
    } else {
      const total   = Number(data.total || 0);
      const current = Number(data.page || page || 1);
      pag.innerHTML = renderPagination(total, current);
    }

    // (opcional) guarda crudo para otros usos
    window.__productosCrudos = Array.isArray(data.data) ? data.data : [];
  }

  // ================================
  // Delegación: paginación
  // ================================
  document.addEventListener("click", (ev) => {
    const btn = ev.target.closest("button[data-page]");
    if (!btn) return;
    const pag = document.getElementById(PAG_ID);
    if (!pag || !pag.contains(btn)) return;
    ev.preventDefault();
    const p = parseInt(btn.getAttribute("data-page"), 10) || 1;
    cargarProductos({ page: p });
  });

  // ================================
  // Filtros -> recarga
  // ================================
  document.addEventListener("change", (ev) => {
    if (ev.target.matches('input[name="marca[]"], input[name="anio[]"]')) {
      cargarProductos({ page: 1 });
    }
  });

  // ================================
  // Primera carga
  // ================================
  document.addEventListener("DOMContentLoaded", () => cargarProductos({ page: 1 }));

})();


// =====================================================
// FAQ
// =====================================================
(() => {
  function closeFAQ(faqItem, header, content) {
    if (getComputedStyle(content).maxHeight === "none") {
      content.style.maxHeight = content.scrollHeight + "px";
      content.offsetHeight;
    }
    content.style.overflow = "hidden";
    content.style.maxHeight = "0px";
    faqItem.classList.remove("active");
    header.setAttribute("aria-expanded", "false");
  }

  function openFAQ(faqItem, header, content) {
    faqItem.classList.add("active");
    header.setAttribute("aria-expanded", "true");
    content.style.overflow = "hidden";
    content.style.maxHeight = "0px";
    content.offsetHeight;
    const targetHeight = content.scrollHeight;
    requestAnimationFrame(() => { content.style.maxHeight = targetHeight + "px"; });
    const onEnd = (e) => { if (e.propertyName === "max-height") { content.style.maxHeight = "none"; content.removeEventListener("transitionend", onEnd); } };
    content.addEventListener("transitionend", onEnd);
  }

  function closeAllFAQs() {
    document.querySelectorAll(".faq-item.active").forEach((faqItem) => {
      const header  = faqItem.querySelector(".faq-header");
      const content = faqItem.querySelector(".faq-content");
      closeFAQ(faqItem, header, content);
    });
  }

  function handleFAQToggle(header, index) {
    const faqItem = header.closest(".faq-item");
    const content = header.nextElementSibling;
    if (!faqItem || !content) return;

    const isActive = faqItem.classList.contains("active");
    if (isActive) {
      closeFAQ(faqItem, header, content);
    } else {
      closeAllFAQs();
      openFAQ(faqItem, header, content);
      setTimeout(() => { faqItem.scrollIntoView({ behavior: "smooth", block: "center" }); }, 300);
    }
  }

  function handleFAQKeyboard(e, header, index) {
    const faqHeaders = document.querySelectorAll(".faq-header");
    switch (e.key) {
      case "Enter":
      case " ":
        e.preventDefault(); handleFAQToggle(header, index); break;
      case "ArrowDown":
        e.preventDefault(); faqHeaders[(index + 1) % faqHeaders.length]?.focus(); break;
      case "ArrowUp":
        e.preventDefault(); faqHeaders[(index - 1 + faqHeaders.length) % faqHeaders.length]?.focus(); break;
      case "Home":
        e.preventDefault(); faqHeaders[0]?.focus(); break;
      case "End":
        e.preventDefault(); faqHeaders[faqHeaders.length - 1]?.focus(); break;
      case "Escape":
        e.preventDefault(); closeAllFAQs(); header.blur(); break;
    }
  }

  function initializeFAQ() {
    const faqContainer = document.getElementById("faq-container");
    if (!faqContainer) return;

    const faqHeaders = document.querySelectorAll(".faq-header");
    if (!faqHeaders.length) return;

    faqContainer.addEventListener("click", (e) => {
      const header = e.target.closest(".faq-header");
      if (!header || !faqContainer.contains(header)) return;
      const headers = Array.from(document.querySelectorAll(".faq-header"));
      const index = headers.indexOf(header);
      if (index === -1) return;
      e.preventDefault(); handleFAQToggle(header, index);
    });

    faqHeaders.forEach((header, index) => {
      header.removeEventListener("keydown", header._faqKeyHandler);
      header._faqKeyHandler = function (e) { handleFAQKeyboard(e, header, index); };
      header.addEventListener("keydown", header._faqKeyHandler);

      header.setAttribute("role", "button");
      header.setAttribute("tabindex", "0");
      header.setAttribute("aria-expanded", "false");
      header.setAttribute("aria-controls", `faq-content-${index}`);
      header.setAttribute("id", `faq-header-${index}`);

      const content = header.nextElementSibling;
      if (content?.classList.contains("faq-content")) {
        content.setAttribute("id", `faq-content-${index}`);
        content.setAttribute("role", "region");
        content.setAttribute("aria-labelledby", `faq-header-${index}`);
      }
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => { initializeFAQ(); }, 100);
  });

  // Exponer utilidades
  window.FAQUtils = {
    reinitialize: initializeFAQ,
    openByIndex: (index = 0) => {
      const faqItems = document.querySelectorAll(".faq-item");
      if (index >= 0 && index < faqItems.length) {
        const header = faqItems[index].querySelector(".faq-header");
        if (header) { closeAllFAQs(); setTimeout(() => handleFAQToggle(header, index), 100); }
      }
    },
    openByQuestion: (searchText) => {
      const faqItems = document.querySelectorAll(".faq-item");
      for (let i = 0; i < faqItems.length; i++) {
        const question = faqItems[i].querySelector("h3")?.textContent.toLowerCase() || "";
        if (question.includes(searchText.toLowerCase())) {
          const header = faqItems[i].querySelector(".faq-header");
          if (header) { closeAllFAQs(); setTimeout(() => handleFAQToggle(header, i), 100); return true; }
        }
      }
      return false;
    },
    closeAll: closeAllFAQs
  };
})();



// =====================================================
// CONTACTO (AJAX)
// =====================================================
(() => {
  const $ = (sel) => document.querySelector(sel);
  const form   = $("#contactoForm");
  const btn    = $("#btnEnviar");
  const boxMsg = $("#contactoMsg");
  if (!form || !btn || !boxMsg) return;

  const showMsg = (html, ok = false) => {
    boxMsg.innerHTML = html;
    boxMsg.className = "mt-4 text-sm " + (ok
      ? "text-green-700 bg-green-100 border border-green-300 rounded p-3"
      : "text-red-700 bg-red-100 border border-red-300 rounded p-3");
  };

  const validate = () => {
    const nombre  = $("#nombre_completo").value.trim();
    const pais    = $("#pais").value.trim();
    const email   = $("#email").value.trim();
    const mensaje = $("#mensaje").value.trim();
    const tel     = $("#telefono").value.trim();
    if (!nombre || nombre.length < 2) return "Ingresa tu nombre completo.";
    if (!pais) return "Selecciona un país.";
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return "Ingresa un correo válido.";
    if (!mensaje || mensaje.length < 5) return "Escribe un mensaje más detallado.";
    if (tel && !/^[0-9+\-\s()]{6,20}$/.test(tel)) return "Teléfono inválido.";
    return null;
  };

  form.addEventListener("submit", async (ev) => {
    ev.preventDefault();
    const err = validate();
    if (err) { showMsg(err, false); return; }
    if (document.getElementById("hp_field")?.value) { showMsg("Error de validación.", false); return; }

    const fd = new FormData(form);
    fd.append("ajax", "1");

    btn.disabled = true;
    btn.classList.add("opacity-60","cursor-not-allowed");
    showMsg("Enviando...", true);

    try {
      const endpoint = (App.baseNorm() || "") + "/pages/contacto_enviar.php"; // AJUSTA si tu ruta difiere
      const res = await fetch(endpoint, { method: "POST", body: fd });
      const text = await res.text();
      let data; try { data = JSON.parse(text); } catch (_) { data = null; }

      if (!res.ok || !data || data.ok !== true) {
        console.error("Respuesta servidor:", text);
        showMsg(data?.msg || "No se pudo enviar el mensaje. Intenta nuevamente.", false);
      } else {
        showMsg("¡Gracias! Tu mensaje fue enviado correctamente.", true);
        form.reset();
      }
    } catch (e) {
      console.error(e);
      showMsg("Error de red. Verifica tu conexión.", false);
    } finally {
      btn.disabled = false;
      btn.classList.remove("opacity-60","cursor-not-allowed");
    }
  });
})();
