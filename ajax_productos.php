<?php
declare(strict_types=1);
session_start();
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors','0');
ini_set('log_errors','1');
while (ob_get_level() > 0) { ob_end_clean(); }

require_once __DIR__ . '/includes/db.php';

// 1) Leer y normalizar la marca (sin htmlspecialchars para la consulta)
$marca = isset($_POST['marca']) ? trim((string)$_POST['marca']) : '';
if ($marca === '') {
  http_response_code(400);
  echo '<li class="splide__slide p-4">Marca inválida</li>';
  exit;
}
$marca = mb_strtoupper($marca, 'UTF-8'); // si guardas en mayúsculas

// 2) Favoritos (ajusta a tu estructura)
$favoritos_usuario = $_SESSION['favoritos'] ?? [];

try {
  // 3) JOIN: productos (p) + caracteristicas_productos (c) por id_producto
  //    y filtro por c.marca
  $productos = $database->select(
    'productos(p)',
    [
      '[><]caracteristicas_productos(c)' => ['p.id_producto' => 'id_producto']
    ],
    [
      'p.id_producto',
      'p.nombre',
      'p.precio',
      'p.imagen',
      'c.marca'
    ],
    [
      'c.marca' => $marca,                // ← filtra SOLO por el campo marca en la tabla de características
      'ORDER'   => ['p.id_producto' => 'DESC'],
      'LIMIT'   => 60
    ]
  );

  if (!$productos || count($productos) === 0) {
    echo '<li class="splide__slide p-4 text-center text-gray-500">Productos no disponibles para esta marca</li>';
    exit;
  }

} catch (Throwable $e) {
  error_log('ajax_productos JOIN error: ' . $e->getMessage());
  http_response_code(500);
  echo '<li class="splide__slide p-4">Error del servidor al cargar productos</li>';
  exit;
}

// 4) Render de cada <li>
foreach ($productos as $prod):
  $id      = (int)($prod['id_producto'] ?? 0);
  $nombre  = htmlspecialchars((string)($prod['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
  $precio  = number_format((float)($prod['precio'] ?? 0), 2);
  $imgPath = !empty($prod['imagen']) ? 'uploads/' . $prod['imagen'] : 'https://placehold.co/600x400/png';
  $img     = htmlspecialchars($imgPath, ENT_QUOTES, 'UTF-8');
  $esFav   = in_array($id, $favoritos_usuario, true);
  $fill    = $esFav ? 'currentColor' : 'none';
  $color   = $esFav ? 'text-red-600' : 'text-gray-600';
?>
<li class="splide__slide !pb-4 sm:!pb-6 lg:!pb-10 !pr-2 lg:!pr-6 !pl-2 sm:!pl-4 lg:!pl-6">
  <div class="border border-gray-100 border-solid shadow-lg hover:shadow-xl transition-all duration-300 rounded-lg p-2 sm:p-4 lg:p-6 flex flex-col gap-3 sm:gap-3 h-full">

    <div class="flex justify-end -mb-1">
      <button type="button" class="favorito-btn" data-id="<?=$id?>">
        <svg xmlns="http://www.w3.org/2000/svg"
             fill="<?=$fill?>"
             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 sm:w-6 sm:h-6 transition-all duration-200 <?=$color?>">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M6.75 3.75h10.5a.75.75 0 01.75.75v15.375a.375.375 0 01-.6.3L12 16.5l-5.4 3.675a.375.375 0 01-.6-.3V4.5a.75.75 0 01.75-.75z" />
        </svg>
      </button>
    </div>

    <img src="<?=$img?>" alt="<?=$nombre?>" class="w-full h-36 sm:h-36 lg:h-48 object-cover rounded-md" />

    <p class="inline font-semibold text-base lg:text-xl text-balance leading-tight uppercase"><?=$nombre?></p>

    <p class="inline text-base lg:text-2xl uppercase font-bold">USD <?=$precio?></p>

    <div class="flex flex-col gap-2 sm:gap-3 mt-auto">
      <button type="button"
              class="btn-secondary add-to-cart inline w-full py-1.5 sm:py-2 rounded-lg uppercase font-semibold text-xs sm:text-base"
              data-id="<?=$id?>" data-qty="1"
              aria-label="Añadir <?=$nombre?> al carrito">
        <span>Añadir al carrito</span>
      </button>

      <button data-modal-target="product-details-modal" data-modal-toggle="product-details-modal"
              type="button" class="inline border border-gray-400 rounded-lg py-1.5 sm:py-2 uppercase font-semibold text-xs sm:text-base">
        Preview
      </button>
    </div>
  </div>
</li>
<?php endforeach; ?>