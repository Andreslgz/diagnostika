<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../auth.php';


error_reporting(E_ALL);
ini_set('display_errors', '1');

header_remove('Content-Type'); // evitamos doble header
// Endpoint AJAX para eliminar favorito (mismo archivo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'remove_fav') {
    header('Content-Type: application/json; charset=utf-8');
    while (ob_get_level() > 0) { @ob_end_clean(); }

    if (empty($_SESSION['usuario_id'])) {
        http_response_code(401);
        echo json_encode(['ok' => false, 'message' => 'No autorizado']);
        exit;
    }
    $uid = (int)$_SESSION['usuario_id'];
    $idp = (int)($_POST['id_producto'] ?? 0);
    if ($idp <= 0) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'ID de producto inválido']);
        exit;
    }

    try {
        $database->delete('favoritos', [
            'id_usuario'  => $uid,
            'id_producto' => $idp
        ]);
        echo json_encode(['ok' => true, 'message' => 'Eliminado de favoritos']);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()]);
    }
    exit;
}

// ----- Página normal (listado) -----
$uid = (int)($_SESSION['usuario_id'] ?? 0);

// Traer favoritos + producto
$productos = $database->select('favoritos(f)', [
    '[>]productos(p)' => ['id_producto' => 'id_producto']
], [
    'p.id_producto',
    'p.nombre',
    'p.precio',
    'p.imagen',
    'p.descripcion',
    // Si quieres marca desde características podrías hacer otra join opcional aquí
], [
    'f.id_usuario' => $uid,
    'ORDER' => ['f.id' => 'DESC']
]);

// Para pintar el corazón lleno
$favoritos_usuario = array_map(fn($r) => (int)$r['id_producto'], $productos ?? []);
$titulo = 'cDIAGNOSTIKA DIESEL GLOBAL';
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
    <link rel="stylesheet" href="/../styles/main.css" />
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
                            <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2">Productos Guardados</span>
                        </div>
                    </li>
                </ol>
            </nav>

        </section>
        <section class="xl:pb-16 py-4 md:py-6 px-4 mx-auto max-w-screen-2xl overflow-hidden">
            <div>
                <h1 class="text-xl md:text-2xl font-extrabold mb-4">
                    Mi cuenta
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
                            <a href="./informacionpersonal.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Información personal
    </a>
                            <a href="./misoftware.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Mis software
                            </a>
                            <a href="./estadoinstalaciones.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Estado de instalación
                            </a>
                            <a href="./miscupones.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Mis cupones
                            </a>
                            <a href="./miscreditos.php"
                                class="p-3 border-b block border-gray-300 hover:bg-gray-200 hover:cursor-pointer transition-colors">
                                Mis créditos
                            </a>
                            <div class="p-3 btn-primary bg-blue-600 ">
                                Productos guardados
    </div>
                            <div
                                class="p-3 hover:bg-gray-200 hover:cursor-pointer transition-colors text-red-600 font-medium">
                                Cerrar sesión
                            </div>
                        </div>
                    </div>

                    <!-- Contenido principal - Responsive -->
                    <div class="col-span-1 lg:col-span-8 xl:col-span-9">
                        

                              <?php if (empty($productos)): ?>
            <div class="border border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-600">
              <p class="text-lg mb-2">No tienes productos guardados aún.</p>
              <p class="text-sm">Ve a la tienda y marca con el icono de favorito ❤️ los productos que te interesan.</p>
            </div>
          <?php else: ?>
            <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4" id="favoritos-list">
              <?php foreach ($productos as $prod): 
                $idProd = (int)$prod['id_producto'];
                $isFav  = in_array($idProd, $favoritos_usuario, true);
                $imgSrc = !empty($prod['imagen']) ? '/uploads/' . ltrim((string)$prod['imagen'], '/') : 'https://placehold.co/600x400/png';
                // Galería (opcional)
                $galeria = $database->select(
                  "galeria_productos",
                  "gal_img",
                  [
                    "id_producto" => $idProd,
                    "gal_est"     => "activo",
                    "ORDER"       => ["gal_id" => "DESC"]
                  ]
                );
                $galeria_full = array_map(fn($f) => '/uploads/' . ltrim((string)$f, '/'), is_array($galeria) ? $galeria : []);
                if (!empty($prod['imagen'])) array_unshift($galeria_full, $imgSrc);
                $data_gallery = htmlspecialchars(json_encode($galeria_full, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
              ?>
              <li class="splide__slide !pb-4 sm:!pb-6 lg:!pb-10 !pr-2 lg:!pr-6 !pl-2 sm:!pl-4 lg:!pl-6" data-item-id="<?= $idProd ?>">
                <div class="border border-gray-100 border-solid shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg p-2 sm:p-4 lg:p-6 flex flex-col gap-3 sm:gap-3 h-full">
                  <div class="flex justify-end -mb-1">
                    <!-- Botón eliminar de favoritos -->
                    <button type="button" class="favorito-btn" data-id="<?= $idProd; ?>" aria-label="Eliminar de favoritos">
                      <svg xmlns="http://www.w3.org/2000/svg"
                          fill="<?= $isFav ? 'currentColor' : 'none'; ?>"
                          viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                          class="w-5 h-5 sm:w-6 sm:h-6 transition-all duration-200 <?= $isFav ? 'text-red-600' : 'text-gray-600'; ?>">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75h10.5a.75.75 0 01.75.75v15.375a.375.375 0 01-.6.3L12 16.5l-5.4 3.675a.375.375 0 01-.6-.3V4.5a.75.75 0 01.75-.75z"/>
                      </svg>
                    </button>
                  </div>

                  <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>"
                      alt="<?= htmlspecialchars($prod['nombre'] ?? 'Producto', ENT_QUOTES, 'UTF-8'); ?>"
                      class="w-full h-36 sm:h-36 lg:h-48 object-cover rounded-md" />

                  <p class="inline font-semibold text-base lg:text-xl text-balance leading-tight uppercase">
                    <?= htmlspecialchars($prod['nombre'] ?? 'Producto', ENT_QUOTES, 'UTF-8'); ?>
                  </p>

                  <p class="inline text-base lg:text-2xl uppercase font-bold">
                    USD <?= number_format((float)($prod['precio'] ?? 0), 2); ?>
                  </p>

                  
                </div>
              </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
                   

                        
                      
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
    <script src="../scripts/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    
<script>
// Quitar/Eliminar de favoritos (AJAX al mismo archivo)
document.addEventListener('click', async (ev) => {
  const btn = ev.target.closest('.favorito-btn');
  if (!btn) return;

  const li   = btn.closest('li[data-item-id]');
  const id   = btn.dataset.id;
  if (!id || !li) return;

  // estado visual
  btn.disabled = true;
  btn.classList.add('opacity-60');

  try {
    const res = await fetch(location.pathname, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: new URLSearchParams({ action: 'remove_fav', id_producto: id })
    });
    const text = await res.text();
    let data = null;
    try { data = JSON.parse(text); } catch {
      const s = text.indexOf('{'), e = text.lastIndexOf('}');
      if (s > -1 && e > s) { try { data = JSON.parse(text.slice(s, e+1)); } catch {} }
    }
    if (!data || data.ok !== true) throw new Error(data?.message || 'Error al eliminar');

    // eliminar del DOM
    li.remove();

    // si la lista quedó vacía, muestra estado vacío
    const list = document.getElementById('favoritos-list');
    if (list && list.children.length === 0) {
      list.outerHTML = `
        <div class="border border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-600">
          <p class="text-lg mb-2">No tienes productos guardados.</p>
        </div>`;
    }
  } catch (err) {
    console.error(err);
    // revertir estado
    btn.disabled = false;
    btn.classList.remove('opacity-60');
    // alerta
    const al = document.getElementById('alertaFavorito');
    const tx = document.getElementById('alertaTexto');
    if (al && tx) {
      tx.textContent = (err.message || 'No se pudo eliminar de favoritos.');
      al.classList.remove('hidden');
      setTimeout(() => al.classList.add('hidden'), 3000);
    } else {
      alert(err.message || 'No se pudo eliminar de favoritos.');
    }
  }
});
</script>

</body>

</html>