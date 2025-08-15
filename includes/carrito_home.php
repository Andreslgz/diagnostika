<div id="drawer-right-example"
    class="fixed top-0 right-0 z-40 h-screen px-4 py-10 overflow-y-auto transition-transform translate-x-full btn-secondary xl:w-[500px] w-[calc(100vw-50px)]"
    tabindex="-1" aria-labelledby="drawer-right-label">
    <div class="flex flex-col items-center w-full  ">

        <?php if (isset($_SESSION['usuario_id'])): ?>
            <?php $usuarioMenu = $database->get('usuarios', ['nombre'], ['id_usuario' => $_SESSION['usuario_id']]); ?>

            <!-- Saludo -->
            <div class="text-center mb-4">
                <p class="text-lg font-semibold text-slate-900">
                    👋 Hello <span class="text-indigo-600"><?php echo htmlspecialchars($usuarioMenu['nombre']); ?></span>
                </p>
                <p class="text-sm text-slate-500">Welcome back</p>
            </div>

            <!-- Botón cerrar sesión -->
            <button type="button" id="logoutModalBtn"
                class="cursor-pointer flex items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-sm hover:border-red-300 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-1 focus:ring-red-400 focus:ring-offset-1 transition-colors mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                </svg>
                <span>Cerrar sesión</span>
            </button>

            <!-- Modal -->
            <div id="logoutModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">¿Cerrar sesión?</h2>
                    <p class="text-sm text-slate-600 mb-6">Se cerrará tu sesión actual y volverás a la página de
                        inicio
                        de sesión.</p>
                    <div class="flex justify-end gap-3">
                        <button id="cancelLogout"
                            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancelar</button>
                        <a href="logout.php"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">Sí,
                            salir</a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Botones login y registro -->
            <div class="flex gap-3 mb-4">
                <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                    data-active-tab="login"
                    class="bg-black text-white rounded-lg px-4 py-2 text-sm font-medium shadow hover:bg-gray-800 transition-colors"
                    type="button">
                    Log In
                </button>
                <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal"
                    data-active-tab="register"
                    class="text-gray-700 border bg-white border-gray-300 rounded-lg px-4 py-2 text-sm font-medium shadow hover:bg-gray-100 transition-colors"
                    type="button">
                    Sign Up
                </button>
            </div>
        <?php endif; ?>

        <!-- Carrito -->
        <div class="w-full">
            <?php if (!empty($_SESSION['carrito'])): ?>
                <ul class="space-y-3 max-h-[750px] overflow-y-auto rounded-lg ">
                    <?php $total = 0; ?>
                    <?php foreach ($_SESSION['carrito'] as $index => $item): ?>
                        <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                        <?php $total += $subtotal; ?>
                        <li class="relative bg-white rounded-xl  xl:p-4 p-2.5 h-full flex items-center xl:gap-3 gap-2"
                            data-item="<?php echo $index; ?>">
                            <!-- Imagen del producto con borde -->
                            <div class="flex-shrink-0">
                                <div class="xl:w-[85px] xl:h-[85px] w-[65px] h-[65px]">
                                    <img src="<?php
                                    echo !empty($item['imagen'])
                                        ? rtrim($url, '/') . '/uploads/' . htmlspecialchars($item['imagen'], ENT_QUOTES, 'UTF-8')
                                        : 'https://placehold.co/600x400/png';
                                    ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>"
                                        class="w-full h-full object-cover">
                                </div>
                            </div>

                            <!-- Contenido derecho -->
                            <div class="flex-grow flex flex-col justify-between h-full py-1 xl:gap-6 gap-4">
                                <!-- Parte superior: Nombre y botón cerrar -->
                                <div class="flex items-start justify-between">
                                    <h3 class="font-semibold text-gray-800 xl:text-base text-sm uppercase tracking-wide">
                                        <?php echo htmlspecialchars($item['nombre']); ?>
                                    </h3>

                                    <button type="button"
                                        class="js-remove-item text-gray-600 hover:text-gray-800 transition-colors ml-2"
                                        aria-label="Eliminar <?php echo htmlspecialchars($item['nombre']); ?>" title="Eliminar"
                                        data-index="<?php echo $index; ?>" <?php if (!empty($item['id_producto'])): ?>
                                            data-id="<?php echo (int) $item['id_producto']; ?>" <?php endif; ?>>
                                        <svg class="xl:w-6 xl:h-6 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>

                                </div>

                                <!-- Parte inferior: Precio y controles -->
                                <div class="flex items-center justify-between">
                                    <!-- Precio -->
                                    <span class="xl:text-xl text-sm font-semibold text-gray-800">
                                        USD. <?php echo number_format($subtotal, 2); ?>
                                    </span>

                                    <!-- Controles de cantidad -->
                                    <div class="flex items-center border border-gray-300 rounded-md">
                                        <button onclick="updateQuantity(<?php echo $index; ?>, -1)"
                                            class="xl:w-6 xl:h-6 w-4 h-4 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors xl:text-xl font-medium">
                                            −
                                        </button>
                                        <span
                                            class="xl:w-12 w-8 text-center text-gray-800 font-medium xl:text-lg text-base border-x border-gray-300">
                                            <?php echo $item['cantidad']; ?>
                                        </span>
                                        <button onclick="updateQuantity(<?php echo $index; ?>, 1)"
                                            class="xl:w-6 xl:h-6 w-4 h-4 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors xl:text-xl font-medium">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="border-t pt-4 mt-4">
                    <div class="flex justify-between items-center mb-3">
                        <span class="font-bold xl:text-xl">Total:</span>
                        <span class="text-white font-bold xl:text-xl" id="totalCarrito">
                            $<?php echo number_format($total, 2); ?>
                        </span>
                    </div>
                    <a href="/tienda/carrito.php"
                        class="block w-full text-center btn-primary rounded-lg xl:py-3 py-2 xl:text-lg font-semibold shadow hover:brightness-110 transition-all duration-200 ease-in-out">
                        Go to Cart
                    </a>
                    <button
                        class="mt-5 block w-full text-white text-center btn-transparent border border-solid border-white rounded-lg xl:py-3 py-2 xl:text-lg font-semibold shadow hover:brightness-110 transition-all duration-200 ease-in-out"
                        type="button" data-drawer-hide="drawer-right-example" aria-controls="drawer-right-example">
                        Continue Shopping
                    </button>
                </div>
            <?php else: ?>
                <p
                    class="flex flex-col items-center justify-center text-center text-gray-500 text-sm mb-3 bg-gray-50 border border-dashed border-gray-300 rounded-lg p-4 shadow-sm">
                    <span class="text-2xl mb-1">🛒</span>
                    <span class="font-medium">
                        Your cart is empty
                    </span>
                </p>
            <?php endif; ?>
        </div>

    </div>
</div>