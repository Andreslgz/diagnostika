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
                                            class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                            <img src="<?php echo $url; ?>/assets/icons/svg/correo-electronico-input.svg"
                                                alt="" />
                                        </div>
                                        <input name="email" type="text" id="input-group-1"
                                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                            placeholder="name@flowbite.com" />
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
                                    <div>
                                        <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Tu
                                            correo electrónico</label>
                                        <div class="relative">
                                            <div
                                                class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                                                <img src="<?php echo $url; ?>/assets/icons/svg/correo-electronico-input.svg"
                                                    alt="" />
                                            </div>
                                            <input name="email" type="email"
                                                class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5"
                                                placeholder="name@correo.com" required />
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