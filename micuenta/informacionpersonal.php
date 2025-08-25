<?php
// public/index.php (añadir lógica para mostrar el carrito)
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../auth.php';


$id_usuario = (int) $_SESSION['usuario_id'];

// Traer el usuario actual
$usuario = $database->get("usuarios", "*", [
    "id_usuario" => $id_usuario
]);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $titulo; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo $url; ?>/styles/main.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />

</head>

<body>

    <?php if (!empty($_SESSION['mensaje_carrito'])): ?>
        <div id="alertCarrito"
            class="fixed bottom-6 right-6 flex items-center gap-3 bg-green-600 text-white px-5 py-4 rounded-xl shadow-xl z-50 animate-slide-in">
            <!-- Icono -->
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white/20">
                ✅
            </div>
            <!-- Mensaje -->
            <div class="flex-1">
                <p class="font-semibold text-base">¡Producto añadido!</p>
                <p class="text-sm text-green-100">Se agregó correctamente al carrito.</p>
            </div>
        </div>
        <?php unset($_SESSION['mensaje_carrito']); ?>
    <?php endif; ?>

    <!-- TOP HEADER -->
    <?php require_once __DIR__ . '/../includes/top_header.php'; ?>
    <!-- HEADER - NAVBAR -->
    <?php require_once __DIR__ . '/../includes/header.php'; ?>

    <main>
        <!-- Breadcrumbs -->
        <section class="xl:pt-16 py-4 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="../tienda/index.php"
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-orange-600 ">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z" />
                            </svg>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                            <a href="#" class="ms-1 text-sm font-medium text-gray-700 hover:text-orange-600 md:ms-2 ">Mi
                                cuenta</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 9 4-4-4-4" />
                            </svg>
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">Información personal</span>
                        </div>
                    </li>
                </ol>
            </nav>

        </section>
        <section class="xl:pb-16 py-4 md:py-6 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold mb-4">
                    My Account
                </h1>

                <!-- Botón del menú móvil (visible solo en móviles) -->
                <button id="mobileMenuToggle"
                    class="lg:hidden w-full mb-4 p-3 btn-secondary text-white rounded-lg flex items-center justify-between">
                    <span>Menú de navegación</span>
                    <svg class="w-5 h-5 transform transition-transform" id="menuIcon" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div class="grid grid-cols-1 lg:grid-cols-12 mt-4 gap-4 lg:gap-10 xl:gap-20">
                    <!-- Menú lateral - Responsive -->
                    <div class="col-span-1 lg:col-span-4 xl:col-span-3">
                        <div id="sideMenu"
                            class="border border-solid border-gray-300 rounded menu-transition overflow-hidden max-h-0 lg:max-h-none opacity-0 lg:opacity-100">
                            <div class="p-3 btn-primary bg-blue-600 ">
                                Personal Info
                            </div>
                            <a href="./misoftware.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                My Software
                            </a>
                            <a href="./estadoinstalaciones.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Installation Status
                            </a>
                            <a href="./miscupones.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                My Coupons
                            </a>
                            <a href="./miscreditos.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                My Credits
                            </a>
                            <a href="./productosguardados.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Saved Products
                            </a>
                            <div
                                class="p-3 hover:bg-gray-200 hover:cursor-pointer transition-colors text-red-600 font-medium">
                                Log Out
                            </div>
                        </div>
                    </div>

                    <!-- Contenido principal - Responsive -->
                    <div class="col-span-1 lg:col-span-8 xl:col-span-9">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <h2 class="font-bold text-lg md:text-xl mb-2 sm:mb-4">
                                Personal Info
                            </h2>
                            <div class="flex flex-col sm:flex-row gap-3 sm:gap-6 w-full sm:w-auto">
                                <button id="btn-edit" type="button"
                                    class="flex flex-row gap-2 items-center justify-center py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <p class="text-sm md:text-base">Edit Information</p>
                                </button>

                                <button id="btn-save" type="button"
                                    class="flex flex-row gap-2 items-center justify-center border border-solid border-gray-100 shadow-xl py-2 px-4 rounded-lg hover:bg-gray-50 bg-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V2" />
                                    </svg>
                                    <p class="text-sm md:text-base">Save Changes</p>
                                </button>
                            </div>
                        </div>

                        <!-- mensajes -->
                        <div id="profile-msg" class="hidden mt-2 text-sm"></div>

                        <form id="profile-form" class="flex flex-col gap-4 max-w-full lg:max-w-[750px] mt-4"
                            autocomplete="off">
                            <input type="hidden" name="action" value="update_profile">

                            <!-- Nombres y apellidos -->
                            <div>
                                <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">
                                    Full Name
                                </label>
                                <div class="relative">
                                    <input name="nombre_completo" type="text" class="border border-gray-300 text-gray-900 text-sm rounded-lg 
                    focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                    disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed" placeholder="Juan Pérez"
                                        required
                                        value="<?= htmlspecialchars($usuario['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($usuario['apellidos'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        disabled />
                                </div>
                            </div>

                            <!-- País y Teléfono -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">
                                        Country
                                    </label>
                                    <select name="pais" class="border border-gray-300 text-gray-900 text-sm rounded-lg 
               focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
               disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed" required disabled>
                                        <option value="">Choose a country</option>
                                        <?php
                                        $paises = ['Estados Unidos', 'Perú', 'Francia', 'Alemania'];
                                        $paisSel = (string) ($usuario['pais'] ?? '');
                                        foreach ($paises as $p) {
                                            $sel = ($paisSel === $p) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($p, ENT_QUOTES, 'UTF-8') . '" ' . $sel . '>' . $p . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">
                                        Phone
                                    </label>
                                    <input name="telefono" type="tel" class="block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 
                    focus:ring-blue-500 focus:border-blue-500
                    disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed" placeholder="123-456-7890"
                                        value="<?= htmlspecialchars($usuario['telefono'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        disabled />
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">
                                    Email address
                                </label>
                                <div class="relative">
                                    <input name="email" type="email" class="border border-gray-300 text-gray-900 text-sm rounded-lg 
                    focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                    disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed"
                                        placeholder="juan.perez@example.com" required
                                        value="<?= htmlspecialchars($usuario['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        disabled />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>
    <!-- MODALS -->

    <?php require_once __DIR__ . '/../includes/modal_login_registro.php'; ?>

    <!-- DRAWER -->
    <?php require_once __DIR__ . '/../includes/carrito_home.php'; ?>

    <!-- SCRIPTS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="<?php echo $url; ?>/scripts/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        .bg-cards {
            background: linear-gradient(0deg, #A7A7A6 0%, #DEDEDE 100%);
        }

        .bg-cards:hover {
            background: linear-gradient(0deg, #8A8A89 0%, #C0C0C0 100%);
        }
    </style>
    <script>
        // Toggle del menú móvil
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sideMenu = document.getElementById('sideMenu');
        const menuIcon = document.getElementById('menuIcon');
        let isMenuOpen = false;

        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function () {
                isMenuOpen = !isMenuOpen;

                if (isMenuOpen) {
                    sideMenu.style.maxHeight = sideMenu.scrollHeight + 'px';
                    sideMenu.style.opacity = '1';
                    menuIcon.style.transform = 'rotate(180deg)';
                } else {
                    sideMenu.style.maxHeight = '0';
                    sideMenu.style.opacity = '0';
                    menuIcon.style.transform = 'rotate(0deg)';
                }
            });
        }

        // Asegurar que el menú esté visible en pantallas grandes
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) { // lg breakpoint
                sideMenu.style.maxHeight = 'none';
                sideMenu.style.opacity = '1';
            } else if (!isMenuOpen) {
                sideMenu.style.maxHeight = '0';
                sideMenu.style.opacity = '0';
            }
        });

    </script>

    <script>
        (() => {
            const form = document.getElementById('profile-form');
            const btnEdit = document.getElementById('btn-edit');
            const btnSave = document.getElementById('btn-save');
            const msgBox = document.getElementById('profile-msg');

            if (!form || !btnEdit || !btnSave || !msgBox) return;

            // Usa tu URL base de PHP
            const UPDATE_ENDPOINT = "<?= $url ?>/micuenta/update_profile.php";

            function setMsg(text, ok = true) {
                msgBox.textContent = text;
                msgBox.classList.remove('hidden');
                msgBox.classList.toggle('text-green-600', ok);
                msgBox.classList.toggle('text-red-600', !ok);
                msgBox.classList.toggle('bg-green-50', ok);
                msgBox.classList.toggle('bg-red-50', !ok);
                msgBox.classList.add('px-3', 'py-2', 'rounded', 'border');
                msgBox.classList.toggle('border-green-200', ok);
                msgBox.classList.toggle('border-red-200', !ok);
            }

            function setDisabled(disabled) {
                // Habilita/Deshabilita inputs visibles (no los type=hidden)
                form.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(el => {
                    el.disabled = disabled;
                    // Refuerzo visual además de las clases disabled: de Tailwind
                    el.classList.toggle('opacity-60', disabled);
                    el.classList.toggle('cursor-not-allowed', disabled);
                });
                // Botones: Guardar solo activo cuando se puede editar
                btnSave.disabled = disabled;
                // (opcional) Cambiar estilo del botón guardar
                btnSave.classList.toggle('opacity-50', disabled);
                btnSave.classList.toggle('cursor-not-allowed', disabled);
            }

            // Estado inicial: todo bloqueado
            setDisabled(true);

            // Click en "Editar información" -> habilitar edición
            btnEdit.addEventListener('click', () => {
                setDisabled(false);
                msgBox.classList.add('hidden');
            });

            // Click en "Guardar cambios" -> enviar
            btnSave.addEventListener('click', async () => {
                try {
                    // Asegúrate de que estén habilitados antes de leer FormData
                    setDisabled(false);

                    const fd = new FormData(form);

                    // Validaciones rápidas
                    const nombre = (fd.get('nombre_completo') || '').toString().trim();
                    const pais = (fd.get('pais') || '').toString().trim();
                    const email = (fd.get('email') || '').toString().trim();
                    if (!nombre || !pais || !email) {
                        setMsg('Completa los campos obligatorios.', false);
                        return;
                    }

                    btnSave.disabled = true;

                    const res = await fetch(UPDATE_ENDPOINT, {
                        method: "POST",
                        body: fd,
                        credentials: "same-origin",
                        headers: {
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest"
                        },
                        cache: "no-store"
                    });

                    const text = await res.text();
                    let data = null;
                    try { data = JSON.parse(text); } catch {
                        const s = text.indexOf('{'), e = text.lastIndexOf('}');
                        if (s > -1 && e > s) { try { data = JSON.parse(text.slice(s, e + 1)); } catch { } }
                    }

                    if (!res.ok || !data || data.ok !== true) {
                        const msg = (data && (data.message || data.error)) || `Error ${res.status}`;
                        throw new Error(msg);
                    }

                    // Éxito: bloquear de nuevo
                    setMsg(data.message || 'Datos actualizados correctamente.', true);
                    setDisabled(true);
                } catch (err) {
                    console.error(err);
                    setMsg(err.message || 'Error de conexión con el servidor.', false);
                    // Permitir reintentar
                    btnSave.disabled = false;
                }
            });
        })();
    </script>

</body>

</html>