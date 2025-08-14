<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Si se agrega al carrito
if (isset($_POST['agregar_carrito'])) {
    $id = intval($_POST['id_producto']);
    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id]['cantidad']++;
    } else {
        $prod = $database->get('productos', '*', ['id_producto' => $id]);
        if ($prod) {
            $_SESSION['carrito'][$id] = [
                'nombre' => $prod['nombre'],
                'precio' => $prod['precio'],
                'imagen' => $prod['imagen'],
                'cantidad' => 1
            ];
        }
    }
    // ✅ Agregar mensaje de confirmación
    $_SESSION['mensaje_carrito'] = "✅ Producto añadido al carrito correctamente";
    header("Location: index.php");
    exit;
}

$productos = $database->select('productos', '*', [
    "ORDER" => ["id_producto" => "DESC"],
    "LIMIT" => 12
]);

$favoritos_usuario = [];

if (isset($_SESSION['usuario_id'])) {
    $favoritos = $database->select("favoritos", "id_producto", [
        "id_usuario" => $_SESSION['usuario_id']
    ]);

    if ($favoritos) {
        $favoritos_usuario = $favoritos; // ya es array de IDs
    }
}

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

      <section class="xl:py-20 py-8 sm:py-10 md:py-14 px-4 mx-auto max-w-screen-2xl overflow-hidden">
  <div class="p-4 sm:p-8 md:p-12 lg:p-16 border border-gray-400 border-solid rounded-3xl">
    <h1 class="text-xl sm:text-2xl md:text-3xl font-bold mb-6 sm:mb-8 md:mb-10">
      ¡Póngase en contacto con nosotros!
    </h1>

    <form id="contactoForm" novalidate>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-10 md:gap-14 lg:gap-20">
        <div class="flex flex-col gap-4 sm:gap-5">
          <div>
            <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">
              Tus nombres y apellidos
            </label>
            <div class="relative">
              <input name="nombre_completo" id="nombre_completo" type="text"
                     class="border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                     placeholder="Juan Pérez" required />
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">País</label>
              <select name="pais" id="pais"
                      class="border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                      required>
                <option value="">Elige un país</option>
                <option value="Estados Unidos">Estados Unidos</option>
                <option value="Perú">Perú</option>
                <option value="Francia">Francia</option>
                <option value="Alemania">Alemania</option>
              </select>
            </div>
            <div>
              <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Teléfono</label>
              <input name="telefono" id="telefono" type="text"
                     class="block p-2.5 w-full text-sm text-gray-900 rounded-lg border border-gray-400 focus:ring-blue-500 focus:border-blue-500"
                     placeholder="123-456-7890" />
            </div>
          </div>

          <div>
            <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Correo electrónico</label>
            <div class="relative">
              <input name="email" id="email" type="email"
                     class="border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                     placeholder="juan.perez@example.com" required />
            </div>
          </div>
        </div>

        <div>
          <label class="block mb-2 text-sm xl:text-base font-medium text-gray-900">Mensaje</label>
          <textarea name="mensaje" id="mensaje"
                    class="border border-gray-400 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                    rows="8" placeholder="Escribe tu mensaje aquí..." required></textarea>
        </div>
      </div>

      <!-- Honeypot anti-bots -->
      <input type="text" name="hp_field" id="hp_field" class="hidden" tabindex="-1" autocomplete="off" style="display:none !important">

      <button id="btnEnviar"
              class="mx-auto text-base sm:text-lg md:text-xl px-12 sm:px-20 md:px-30 lg:px-40 shadow-lg mt-6 sm:mt-8 md:mt-10 block py-2.5 rounded-lg btn-secondary hover:brightness-110 transition-all ease-in-out duration-200"
              type="submit">
        Enviar
      </button>

      <!-- Caja de mensajes -->
      <div id="contactoMsg" class="mt-4 text-sm"></div>
    </form>
  </div>
</section>

        <section
            class="xl:py-0 py-8 px-4 mx-auto max-w-screen-xl overflow-hidden grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8 mb-12 md:mb-24">
            <div
                class="bg-cards border border-solid border-gray-400 flex flex-col items-center justify-center p-4 sm:p-6 md:p-8 rounded-3xl gap-3 sm:gap-4 md:gap-6">
                <img src="/assets/icons/contact/1.svg" alt="" class="w-12 h-12 sm:w-14 sm:h-14 md:w-auto md:h-auto">
                <div class="flex w-full items-center justify-center flex-col gap-1 sm:gap-1.5">
                    <h2 class="font-extrabold text-3xl sm:text-4xl md:text-5xl lg:text-6xl">
                        24/7
                    </h2>
                    <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl">Soporte</p>
                </div>
                <p class="text-center text-white text-sm sm:text-base md:text-lg lg:text-2xl">
                    Siempre estamos listos para ayudarlo a resolver problemas y mantener sus herramientas funcionando.
                </p>
            </div>
            <div
                class="bg-cards border border-solid border-gray-400 flex flex-col items-center justify-center p-4 sm:p-6 md:p-8 rounded-3xl gap-3 sm:gap-4 md:gap-6">
                <img src="/assets/icons/contact/2.svg" alt="" class="w-12 h-12 sm:w-14 sm:h-14 md:w-auto md:h-auto">
                <div class="flex w-full items-center justify-center flex-col gap-1 sm:gap-1.5">
                    <h2 class="font-extrabold text-3xl sm:text-4xl md:text-5xl lg:text-6xl">
                        1 Año
                    </h2>
                    <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl">De Garantía</p>
                </div>
                <p class="text-center text-white text-sm sm:text-base md:text-lg lg:text-2xl">
                    Garantía de 1 año. Solucionamos cualquier problema de software sin coste adicional.
                </p>
            </div>
            <div
                class="bg-cards border border-solid border-gray-400 flex flex-col items-center justify-center p-4 sm:p-6 md:p-8 rounded-3xl gap-3 sm:gap-4 md:gap-6">
                <img src="/assets/icons/contact/3.svg" alt="" class="w-12 h-12 sm:w-14 sm:h-14 md:w-auto md:h-auto">
                <div class="flex w-full items-center justify-center flex-col gap-1 sm:gap-1.5">
                    <h2 class="font-extrabold text-3xl sm:text-4xl md:text-5xl lg:text-6xl">
                        Cobertura
                    </h2>
                    <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl">Global</p>
                </div>
                <p class="text-center text-white text-sm sm:text-base md:text-lg lg:text-2xl">
                    Asistimos a clientes en cualquier parte del mundo, de forma remota y confiable.
                </p>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <?php require_once __DIR__ . '/../includes/footer.php'; ?>

    <!-- MODALS -->
    <?php require_once __DIR__ . '/../includes/modal_login_registro.php'; ?>

    <!-- DRAWER -->
    <?php require_once __DIR__ . '/../includes/carrito_home.php'; ?>


    <div id="alertaFavorito"
        class="hidden fixed top-5 right-5 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow z-50 text-sm"
        role="alert">
        <strong class="font-bold">¡Atención!</strong>
        <span class="block" id="alertaTexto"></span>
    </div>


    <!-- SCRIPTS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="<?php echo $url;?>/scripts/main.js"></script>
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
</body>

</html>