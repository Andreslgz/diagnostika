<header>
    <nav class="bg-header border-gray-200 px-4 xl:py-0 py-3 lg:px-6">
        <div class="flex flex-wrap justify-between items-center mx-auto max-w-screen-2xl">
            <a href="<?php echo $url; ?>" class="flex items-center">
                <img src="<?php echo $url; ?>/assets/icons/Logotipo.svg" class="mr-3 h-6 sm:h-9"
                    alt="<?php echo $titulo; ?>" />
            </a>
            <div class="flex items-center lg:order-2">
                <form id="search-form" class="hidden mr-3 w-full lg:inline-block">
                    <label for="search-bar" class="mb-2 text-sm font-medium text-gray-900 sr-only">Busca tu
                        producto</label>
                    <div class="relative">
                        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="search" id="search-bar"
                            class="block py-2 px-4 pr-[420px] pl-10 w-full btn-primary text-sm text-gray-900 rounded-full border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Busca tu producto aquí" required />
                    </div>
                </form>


                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                        data-active-tab="login"
                        class="bg-black text-white rounded-lg px-3 py-1 text-nowrap xl:mr-3 cursor-pointer xl:text-base text-sm"
                        type="button">
                        Iniciar sesión
                    </button>
                    <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                        data-active-tab="register"
                        class="text-gray-500 border border-solid border-gray-500 rounded-lg px-3 py-1 cursor-pointer hidden lg:inline-block"
                        type="button">
                        Registro
                    </button>

                <?php endif; ?>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php $usuarioMenu = $database->get('usuarios', ['nombre'], ['id_usuario' => $_SESSION['usuario_id']]); ?>
                    <div class="flex flex-row gap-2 items-center justify-center xl:mx-6">

                        <img class="xl:size-[43px] size-[25px] " src="<?php echo $url; ?>/assets/icons/svg/userLog.svg"
                            alt="">


                        <div class="flex flex-col">
                            <p class="xl:text-sm text-[11px] font-extrabold text-gray-500">
                                <?php echo htmlspecialchars($usuarioMenu['nombre']); ?>
                            </p>
                            <a class="xl:text-sm text-[11px]  text-gray-500 text-nowrap underline underline-offset-4"
                                href="<?php echo $url; ?>/micuenta/informacionpersonal.php">
                                My account
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <button
                    class="text-white focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm xl:ml-6 ml-4 cursor-pointer"
                    type="button" data-drawer-target="drawer-right-example" data-drawer-show="drawer-right-example"
                    data-drawer-placement="right" aria-controls="drawer-right-example">
                    <div
                        class="btn-secondary rounded-full xl:w-[60px] w-[40px] xl:h-[60px] h-[40px] flex items-center justify-center">
                        <img src="<?php echo $url; ?>/assets/icons/Cart.svg" alt="" class="xl:w-[39px] w-[25px]" />
                    </div>
                </button>

                <div class="hidden z-50 my-4 w-48 text-base list-none bg-white rounded divide-y divide-gray-100 shadow"
                    id="language-dropdown">
                    <ul class="py-1" role="none">
                        <li>
                            <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                <div class="inline-flex items-center">English (US)</div>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">
                                <div class="inline-flex items-center">Español (ES)</div>
                            </a>
                        </li>
                    </ul>
                </div>
                <button data-collapse-toggle="mobile-menu-search" type="button"
                    class="inline-flex items-center p-2 ml-1 text-sm text-gray-500 rounded-lg lg:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
                    aria-controls="mobile-menu-search" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <svg class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
            <div class="hidden justify-between items-center w-full lg:flex lg:w-auto lg:order-1"
                id="mobile-menu-search">
                <form class="flex items-center mt-4 lg:hidden">
                    <label for="search-mobile" class="sr-only">Search</label>
                    <div class="relative w-full">
                        <div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <input type="search" id="search-mobile"
                            class="border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5"
                            placeholder="Search for anything..." required />
                    </div>
                    <button type="submit"
                        class="inline-flex items-center p-2.5 ml-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300">
                        Buscar
                    </button>
                </form>

                <?php
                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

                // Función para saber si es la página actual
                function isActive($path)
                {
                    global $currentPath;
                    return rtrim($currentPath, '/') === rtrim($path, '/');
                }
                ?>

                <ul class="flex flex-col mt-4 font-medium lg:flex-row lg:space-x-0 lg:mt-0 lg:h-full">
                    <li
                        class="lg:h-full lg:flex lg:items-center xl:py-3 <?php echo isActive('/') ? 'btn-secondary' : ''; ?>">
                        <a href="<?php echo $url; ?>"
                            class="block py-2 pr-4 pl-3 border-b border-gray-100 font-semibold lg:px-6 lg:py-5 lg:h-full lg:flex lg:items-center lg:border-0 <?php echo isActive('/') ? 'text-white' : 'text-gray-600 lg:hover:bg-white lg:hover:text-blue-700'; ?>">
                            Inicio
                        </a>
                    </li>

                    <li
                        class="lg:h-full lg:flex lg:items-center xl:py-3 <?php echo isActive('/tienda') ? 'btn-secondary' : ''; ?>">
                        <a href="<?php echo $url; ?>/tienda"
                            class="block py-2 pr-4 pl-3 border-b border-gray-100 font-semibold lg:px-6 lg:py-5 lg:h-full lg:flex lg:items-center lg:border-0 <?php echo isActive('/tienda') ? 'text-white' : 'text-gray-600 lg:hover:bg-white lg:hover:text-blue-700'; ?>">
                            Tienda
                        </a>
                    </li>

                    <li
                        class="lg:h-full lg:flex lg:items-center xl:py-3 <?php echo isActive('/pages/contacto.php') ? 'btn-secondary' : ''; ?>">
                        <a href="<?php echo $url; ?>/pages/contacto.php"
                            class="block py-2 pr-4 pl-3 border-b border-gray-100 font-semibold lg:px-6 lg:py-5 lg:h-full lg:flex lg:items-center lg:border-0 <?php echo isActive('/pages/contacto.php') ? 'text-white' : 'text-gray-600 lg:hover:bg-white lg:hover:text-blue-700'; ?>">
                            Contacto
                        </a>
                    </li>
                </ul>

            </div>
        </div>
    </nav>
</header>