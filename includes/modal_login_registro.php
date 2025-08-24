<div id="authentication-modal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[900px]">
    <div class="relative p-4 w-full max-w-6xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white shadow-lg">
            <!-- Modal Body with 2 Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 min-h-[500px] modal-content-hidden" id="modal-body">
                <!-- Column 1: Tabs and Forms -->
                <div class="flex flex-col xl:pt-6 xl:px-10 px-6">
                    <!-- Tabs -->
                    <div class="">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center gap-10" id="auth-tab"
                            data-tabs-toggle="#auth-tab-content" role="tablist">
                            <li class="flex-1" role="presentation">
                                <button
                                    class="w-min p-4 border-b-2 border-amber-500 rounded-t-lg text-amber-600 tab-button-transition"
                                    id="login-tab" data-tabs-target="#login" type="button" role="tab"
                                    aria-controls="login" aria-selected="true">
                                    Iniciar Sesión
                                </button>
                            </li>
                            <li class="flex-1" role="presentation">
                                <button
                                    class="w-min p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 tab-button-transition"
                                    id="register-tab" data-tabs-target="#register" type="button" role="tab"
                                    aria-controls="register" aria-selected="false">
                                    Registrarse
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div id="auth-tab-content" class="p-4 md:p-6 flex-1 flex flex-col">
                        <!-- Login Form -->
                        <div class="flex flex-col tab-content-transition" id="login" role="tabpanel"
                            aria-labelledby="login-tab">
                            <div class="xl:!-mt-[165px] xl:mb-[35px]">
                                <h4 class="text-2xl font-bold text-gray-900 mb-2 text-center">
                                    Hola, <span class="font-extrabold">amigo</span>!
                                </h4>
                            </div>

                            <form class="space-y-4" id="login-form" action="login.php" method="POST">
                                <div>
                                    <label for="input-group-1"
                                        class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tu
                                        correo
                                        electrónico</label>
                                    <div class="relative mb-6">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none z-10">
                                            <img src="<?php echo $url; ?>/assets/icons/svg/correo-electronico-input.svg"
                                                alt="" />
                                        </div>
                                        <input name="email" type="text" id="login-email-input" autocomplete="off"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                            placeholder="name@flowbite.com" />

                                        <!-- Dropdown de sugerencias de email para login -->
                                        <div id="login-email-suggestions"
                                            class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg z-50 hidden max-h-48 overflow-y-auto">
                                            <!-- Las sugerencias se generarán dinámicamente -->
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <label for="input-group-1"
                                        class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tu
                                        contraseña</label>
                                    <div class="relative mb-6">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3.5 -ml-0.5 pointer-events-none">
                                            <img src="<?php echo $url; ?>/assets/icons/svg/password-input.svg" alt="" />
                                        </div>
                                        <input type="password" name="password" id="input-group-2"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                            placeholder="••••••••" />
                                    </div>
                                    <div class="text-end -mt-4">
                                        <p class="text-sm font-medium text-gray-500">
                                            ¿Olvidaste tu contraseña?
                                            <a href="#" class="text-amber-600 hover:underline">Recuperar
                                                contraseña</a>
                                        </p>
                                    </div>
                                </div>


                                <button type="submit"
                                    class="w-full cursor-pointer xl:mt-14 mt-6 text-white btn-secondary shadow-lg focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-6 py-2.5 text-center">
                                    Iniciar Sesión
                                </button>
                                <div class="text-center">
                                    <p class="text-gray-700 text-sm">
                                        ¿Aún no tienes una cuenta?
                                        <button type="button"
                                            class="text-amber-600 hover:underline bg-transparent border-none cursor-pointer"
                                            id="switch-to-register">
                                            Regístrate
                                        </button>
                                    </p>
                                </div>

                                <div id="login-error"
                                    class="hidden mt-2 text-xs text-red-700 bg-red-100 border border-red-200 rounded px-3 py-1.5 text-center shadow-sm">
                                    <!-- El mensaje de error se insertará aquí dinámicamente -->
                                </div>

                            </form>

                        </div>

                        <!-- Register Form -->
                        <div class="hidden flex-col justify-center h-[900px] tab-content-transition" id="register"
                            role="tabpanel" aria-labelledby="register-tab">
                            <h4 class="text-2xl font-bold text-gray-900 mb-2 text-center">
                                Hola, <span class="font-extrabold">amigo</span>!
                            </h4>
                            <form id="register-form" class="space-y-4" action="#" method="POST">
                                <!-- Nombre completo -->
                                <div>
                                    <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tus
                                        nombres y apellidos</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                            <img src="<?php echo $url; ?>/assets/icons/svg/full-name-input.svg"
                                                alt="" />
                                        </div>
                                        <input name="nombre_completo" type="text"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                            placeholder="Juan Pérez" required />
                                    </div>
                                </div>

                                <!-- País + Teléfono -->
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block mb-2 text-sm xl:text-base font-medium text-gray-900">País</label>
                                        <select name="pais"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                            required>
                                            <option value="">Elige un país</option>
                                            <option value="Estados Unidos">Estados Unidos</option>
                                            <option value="Perú">Perú</option>
                                            <option value="Francia">Francia</option>
                                            <option value="Alemania">Alemania</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Teléfono</label>
                                        <input name="telefono" type="text"
                                            class="block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="123-456-7890" />
                                    </div>
                                </div>

                                <!-- Email + Código referido -->
                                <div class="grid md:grid-cols-2 gap-4">
                                    <div class="relative">
                                        <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tu
                                            correo electrónico</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none z-10">
                                                <img src="<?php echo $url; ?>/assets/icons/svg/correo-electronico-input.svg"
                                                    alt="" />
                                            </div>
                                            <input name="email" type="email" id="email-input" autocomplete="off"
                                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                                placeholder="name@correo.com" required />

                                            <!-- Dropdown de sugerencias de email -->
                                            <div id="email-suggestions"
                                                class="absolute top-full left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg z-50 hidden max-h-48 overflow-y-auto">
                                                <!-- Las sugerencias se generarán dinámicamente -->
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Código
                                            de referido</label>
                                        <input name="codigo_referido" type="text"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                            placeholder="Opcional" />
                                    </div>
                                </div>

                                <!-- Contraseña + Confirmación -->
                                <div>
                                    <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tu
                                        contraseña</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                            <img src="<?php echo $url; ?>/assets/icons/svg/password-input.svg" alt="" />
                                        </div>
                                        <input name="password" type="password"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                            placeholder="••••••••" required minlength="6" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Repita
                                        su contraseña</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                            <img src="<?php echo $url; ?>/assets/icons/svg/password-input.svg" alt="" />
                                        </div>
                                        <input name="password_confirm" type="password"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                            placeholder="••••••••" required minlength="6" />
                                    </div>
                                </div>

                                <!-- Términos -->
                                <div class="flex items-start justify-center mb-5 mx-auto">
                                    <input id="terms" name="terms" type="checkbox"
                                        class="w-4 h-4 border border-gray-300 rounded-sm focus:ring-3 focus:ring-blue-300"
                                        required />
                                    <label for="terms" class="ms-2 text-sm font-medium text-gray-900">
                                        Acepto los <a href="#" class="text-[#F7A615] hover:underline">términos y
                                            condiciones</a>
                                    </label>
                                </div>

                                <button type="submit"
                                    class="w-full cursor-pointer mt-2 shadow-lg text-white btn-secondary hover:bg-amber-600 focus:ring-4 focus:outline-none focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    Crear Cuenta
                                </button>
                            </form>

                            <!-- Alertas -->
                            <div id="register-error"
                                class="hidden mt-2 text-xs text-red-700 bg-red-100 border border-red-200 rounded px-3 py-1.5 text-center shadow-sm">
                            </div>
                            <div id="register-success"
                                class="hidden mt-2 text-xs text-green-700 bg-green-100 border border-green-200 rounded px-3 py-1.5 text-center shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Static Image -->
                <div class="hidden md:block">
                    <img src="<?php echo $url; ?>/assets/images/auth.jpg" alt="Authentication"
                        class="w-full h-[750px] object-cover rounded-r-lg" />
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    console.log("Modal script loaded");

    // Funcionalidad de autocompletado para el email
    document.addEventListener('DOMContentLoaded', function () {
        const emailInput = document.getElementById('email-input');
        const suggestionsDiv = document.getElementById('email-suggestions');
        const loginEmailInput = document.getElementById('login-email-input');
        const loginSuggestionsDiv = document.getElementById('login-email-suggestions');

        const emailDomains = [
            '@gmail.com',
            '@hotmail.com',
            '@icloud.com',
            '@outlook.com'
        ];

        function initEmailAutocomplete(inputElement, suggestionsElement) {
            function showSuggestions(inputValue) {
                suggestionsElement.innerHTML = '';

                if (!inputValue || inputValue.includes('@')) {
                    hideSuggestions();
                    return;
                }

                // Crear sugerencias solo si no hay @ en el input
                const suggestions = emailDomains.map(domain => inputValue + domain);

                suggestions.forEach(suggestion => {
                    const suggestionItem = document.createElement('div');
                    suggestionItem.className = 'px-4 py-2 cursor-pointer hover:bg-gray-100 transition-colors duration-150 text-sm text-gray-700 flex items-center';
                    suggestionItem.innerHTML = `
                        <span class="font-medium">${suggestion}</span>
                    `;

                    suggestionItem.addEventListener('click', function () {
                        inputElement.value = suggestion;
                        hideSuggestions();
                        inputElement.focus();
                    });

                    suggestionsElement.appendChild(suggestionItem);
                });

                suggestionsElement.classList.remove('hidden');
            }

            function hideSuggestions() {
                suggestionsElement.classList.add('hidden');
            }

            // Event listeners
            inputElement.addEventListener('input', function () {
                const value = this.value.trim();
                showSuggestions(value);
            });

            inputElement.addEventListener('focus', function () {
                const value = this.value.trim();
                if (value && !value.includes('@')) {
                    showSuggestions(value);
                }
            });

            inputElement.addEventListener('blur', function () {
                // Retrasar el ocultamiento para permitir clicks en las sugerencias
                setTimeout(() => {
                    hideSuggestions();
                }, 150);
            });

            // Manejar teclas de navegación
            inputElement.addEventListener('keydown', function (e) {
                const suggestions = suggestionsElement.querySelectorAll('div');
                let selectedIndex = -1;

                // Encontrar el elemento seleccionado actual
                suggestions.forEach((item, index) => {
                    if (item.classList.contains('bg-blue-100')) {
                        selectedIndex = index;
                    }
                });

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = selectedIndex < suggestions.length - 1 ? selectedIndex + 1 : 0;
                    updateSelection(suggestions, selectedIndex);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = selectedIndex > 0 ? selectedIndex - 1 : suggestions.length - 1;
                    updateSelection(suggestions, selectedIndex);
                } else if (e.key === 'Enter' && selectedIndex >= 0) {
                    e.preventDefault();
                    suggestions[selectedIndex].click();
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });

            function updateSelection(suggestions, selectedIndex) {
                suggestions.forEach((item, index) => {
                    if (index === selectedIndex) {
                        item.classList.add('bg-blue-100', 'text-blue-700');
                        item.classList.remove('hover:bg-gray-100');
                    } else {
                        item.classList.remove('bg-blue-100', 'text-blue-700');
                        item.classList.add('hover:bg-gray-100');
                    }
                });
            }

            // Cerrar sugerencias al hacer click fuera
            document.addEventListener('click', function (e) {
                if (!inputElement.contains(e.target) && !suggestionsElement.contains(e.target)) {
                    hideSuggestions();
                }
            });
        }

        // Inicializar autocompletado para ambos campos de email
        if (emailInput && suggestionsDiv) {
            initEmailAutocomplete(emailInput, suggestionsDiv);
        }

        if (loginEmailInput && loginSuggestionsDiv) {
            initEmailAutocomplete(loginEmailInput, loginSuggestionsDiv);
        }
    });
</script>


<style>
    #email-suggestions,
    #login-email-suggestions {
        border-top: none;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
        margin-top: -1px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    #email-suggestions div:first-child,
    #login-email-suggestions div:first-child {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    #email-suggestions div:last-child,
    #login-email-suggestions div:last-child {
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    /* Animación suave para el dropdown */
    #email-suggestions,
    #login-email-suggestions {
        animation: slideDown 0.15s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mejorar el focus del input cuando hay sugerencias */
    #email-input:focus+#email-suggestions,
    #login-email-input:focus+#login-email-suggestions {
        border-color: #3b82f6;
    }

    /* Efecto hover más suave */
    #email-suggestions div,
    #login-email-suggestions div {
        transition: all 0.15s ease-in-out;
    }

    /* Estilo para el elemento seleccionado con teclado */
    #email-suggestions div.bg-blue-100,
    #login-email-suggestions div.bg-blue-100 {
        background-color: #dbeafe !important;
        color: #1d4ed8 !important;
    }
</style>