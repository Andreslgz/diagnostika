<?php

declare(strict_types=1);
session_start();
header('Content-Type: text/html; charset=utf-8');

// Si usas $url para las rutas de imágenes, defínelo aquí o inclúyelo
// require __DIR__ . '/includes/config.php'; // donde definas $url

$total = 0;
?>

<?php if (!empty($_SESSION['carrito'])): ?>
    <ul class="space-y-3 max-h-[750px] overflow-y-auto rounded-lg ">
        <?php foreach ($_SESSION['carrito'] as $index => $item): ?>
            <?php
            $subtotal = (float)$item['precio'] * (int)$item['cantidad'];
            $total += $subtotal;
            ?>
            <li class="relative bg-white rounded-xl  xl:p-4 p-2.5 h-full flex items-center xl:gap-3 gap-2"
                data-item="<?php echo $index; ?>">
                <div class="flex-shrink-0">
                    <div class="xl:w-[85px] xl:h-[85px] w-[65px] h-[65px]">
                        <img src="<?php
                                    echo !empty($item['imagen'])
                                        ? rtrim($url ?? '', '/') . '/uploads/' . htmlspecialchars($item['imagen'], ENT_QUOTES, 'UTF-8')
                                        : 'https://placehold.co/600x400/png';
                                    ?>" alt="<?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                            class="w-full h-full object-cover">
                    </div>
                </div>

                <div class="flex-grow flex flex-col justify-between h-full py-1 xl:gap-6 gap-4">
                    <div class="flex items-start justify-between">
                        <h3 class="font-semibold text-gray-800 xl:text-base text-sm uppercase tracking-wide">
                            <?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                        </h3>

                        <button type="button"
                            class="js-remove-item text-gray-600 hover:text-gray-800 transition-colors ml-2"
                            aria-label="Eliminar <?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                            title="Eliminar"
                            data-index="<?php echo $index; ?>"
                            <?php if (!empty($item['id_producto'])): ?>
                            data-id="<?php echo (int)$item['id_producto']; ?>"
                            <?php endif; ?>>
                            <svg class="xl:w-6 xl:h-6 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between">

                        <!-- Precio -->
                        <span class="xl:text-xl text-sm font-semibold text-gray-800"
                            data-subtotal
                            id="subtotal-<?php echo $index; ?>">
                            USD. <?php echo number_format($subtotal, 2); ?>
                        </span>

                        <!-- Controles de cantidad -->
                        <div class="flex items-center border border-gray-300 rounded-md">
                            <button onclick="updateQuantity(<?php echo $index; ?>, -1)"
                                class="xl:w-6 xl:h-6 w-4 h-4 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors xl:text-xl font-medium">−</button>

                            <span class="xl:w-12 w-8 text-center text-gray-800 font-medium xl:text-lg text-base border-x border-gray-300"
                                data-qty
                                id="qty-<?php echo $index; ?>">
                                <?php echo (int)$item['cantidad']; ?>
                            </span>

                            <button onclick="updateQuantity(<?php echo $index; ?>, 1)"
                                class="xl:w-6 xl:h-6 w-4 h-4 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors xl:text-xl font-medium">+</button>
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
        <button class="mt-5 block w-full text-white text-center btn-transparent border border-solid border-white rounded-lg xl:py-3 py-2 xl:text-lg font-semibold shadow hover:brightness-110 transition-all duration-200 ease-in-out"
            type="button" data-drawer-hide="drawer-right-example" aria-controls="drawer-right-example">
            Continue Shopping
        </button>
    </div>
<?php else: ?>
    <p class="flex flex-col items-center justify-center text-center text-gray-500 text-sm mb-3 bg-gray-50 border border-dashed border-gray-300 rounded-lg p-4 shadow-sm">
        <span class="text-2xl mb-1">🛒</span>
        <span class="font-medium">Your cart is empty</span>
    </p>
<?php endif; ?>