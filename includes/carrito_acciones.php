<?php
declare(strict_types=1);

session_start();

// Endpoint solo JSON
ini_set('display_errors', '0');
ini_set('log_errors', '1');
header('Content-Type: application/json; charset=utf-8');

// Evita basura previa en salida
while (ob_get_level() > 0) { ob_end_clean(); }

// BD (ajusta la ruta si es necesario)
require_once __DIR__ . '/../includes/db.php'; // <-- verifica esta ruta

// Carrito en sesión como array indexado 0..n
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

/**
 * Resumen del carrito: cantidades y totales (numérico + formateado)
 */
function cart_summary(): array {
    $count = 0;
    $total = 0.0;

    foreach ($_SESSION['carrito'] as $it) {
        $qty   = (int)($it['cantidad'] ?? 0);
        $price = (float)($it['precio'] ?? 0);
        $count += $qty;
        $total += $qty * $price;
    }

    return [
        'cart_count'     => $count,
        'cart_total_raw' => $total,
        'cart_total'     => number_format($total, 2, '.', ''),
        'cart_empty'     => empty($_SESSION['carrito']),
    ];
}

/**
 * Busca índice en carrito por id_producto; null si no está.
 */
function find_item_index_by_product_id(int $id_producto): ?int {
    foreach ($_SESSION['carrito'] as $idx => $it) {
        if ((int)($it['id_producto'] ?? 0) === $id_producto) {
            return (int)$idx;
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido'] + cart_summary(), JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {

    // =================================================
    // ADD: Agregar producto (id_producto, cantidad)
    // =================================================
    case 'add': {
        $id       = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
        $cantidad = isset($_POST['cantidad']) ? max(1, (int)$_POST['cantidad']) : 1;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Producto inválido'] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Si ya existe en carrito, solo incrementa
        $existingIndex = find_item_index_by_product_id($id);
        if ($existingIndex !== null) {
            $_SESSION['carrito'][$existingIndex]['cantidad'] += $cantidad;

            $qty   = (int)$_SESSION['carrito'][$existingIndex]['cantidad'];
            $price = (float)$_SESSION['carrito'][$existingIndex]['precio'];
            $itemSubtotal = number_format($qty * $price, 2, '.', '');

            echo json_encode([
                'success'       => true,
                'message'       => 'Cantidad actualizada',
                'item_index'    => $existingIndex,
                'new_qty'       => $qty,
                'item_subtotal' => $itemSubtotal,
            ] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        // No está: traer datos desde BD
        try {
            /** @var \Medoo\Medoo $database */
            $prod = $database->get('productos', [
                'id_producto',
                'nombre',
                'precio',
                'imagen',
            ], [
                'id_producto' => $id
            ]);

            if (!$prod) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Producto no encontrado'] + cart_summary(), JSON_UNESCAPED_UNICODE);
                exit;
            }

            $newItem = [
                'id_producto' => (int)$prod['id_producto'],
                'nombre'      => (string)$prod['nombre'],
                'precio'      => (float)$prod['precio'],
                'imagen'      => (string)($prod['imagen'] ?? ''),
                'cantidad'    => (int)$cantidad,
            ];
            $_SESSION['carrito'][] = $newItem;

            $newIndex     = count($_SESSION['carrito']) - 1;
            $itemSubtotal = number_format($newItem['cantidad'] * $newItem['precio'], 2, '.', '');

            echo json_encode([
                'success'       => true,
                'message'       => 'Producto añadido',
                'item_index'    => $newIndex,
                'new_qty'       => (int)$newItem['cantidad'],
                'item_subtotal' => $itemSubtotal,
            ] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Throwable $e) {
            error_log('carrito_acciones add error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al agregar producto'] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // =================================================
    // UPDATE: Actualizar cantidad por índice (delta o set_qty)
    // =================================================
    case 'update': {
        $index    = isset($_POST['index']) ? (int)$_POST['index'] : -1;
        $hasDelta = array_key_exists('delta', $_POST);
        $hasSet   = array_key_exists('set_qty', $_POST);

        if ($index < 0 || !isset($_SESSION['carrito'][$index])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ítem inválido'] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!$hasDelta && !$hasSet) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Parámetros faltantes'] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        if ($hasDelta) {
            $delta = (int)$_POST['delta'];
            $_SESSION['carrito'][$index]['cantidad'] += $delta;
        } else {
            $newQty = max(0, (int)$_POST['set_qty']);
            $_SESSION['carrito'][$index]['cantidad'] = $newQty;
        }

        // Si la cantidad cae a 0, elimina y reindexa
        if ((int)$_SESSION['carrito'][$index]['cantidad'] <= 0) {
            unset($_SESSION['carrito'][$index]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']); // reindexar
            echo json_encode(['success' => true, 'item_removed' => true] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        $qty   = (int)$_SESSION['carrito'][$index]['cantidad'];
        $price = (float)$_SESSION['carrito'][$index]['precio'];
        $itemSubtotal = number_format($qty * $price, 2, '.', '');

        echo json_encode([
            'success'        => true,
            'item_removed'   => false,
            'item_index'     => $index,
            'new_qty'        => $qty,
            'item_subtotal'  => $itemSubtotal,
        ] + cart_summary(), JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =================================================
    // REMOVE: Eliminar por índice
    // =================================================
    case 'remove': {
        $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

        if ($index >= 0 && isset($_SESSION['carrito'][$index])) {
            unset($_SESSION['carrito'][$index]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']); // reindexa

            echo json_encode(['success' => true] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Ítem no encontrado'] + cart_summary(), JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =================================================
    // REMOVE BY ID: Eliminar por id_producto (útil si el front envía id)
    // =================================================
    case 'removeById': {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID inválido'] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        $idx = find_item_index_by_product_id($id);
        if ($idx !== null) {
            unset($_SESSION['carrito'][$idx]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
            echo json_encode(['success' => true] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Ítem no encontrado'] + cart_summary(), JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =================================================
    // SUMMARY: Totales para Order Summary
    // =================================================
    case 'summary': {
        $summary         = cart_summary();                        // cart_count, cart_total_raw, cart_total, cart_empty
        $cart_total_raw  = (float)$summary['cart_total_raw'];     // numérico seguro
        $discounts       = 0.00;                                  // TODO: tu lógica real
        $voucher         = 0.00;                                  // TODO: tu lógica real
        $total_calc      = max(0, $cart_total_raw - $discounts - $voucher);

        echo json_encode([
            'success'           => true,
            'subtotal'          => number_format($cart_total_raw, 2, '.', ''),
            'discounts_applied' => number_format($discounts, 2, '.', ''),
            'voucher_discount'  => number_format($voucher, 2, '.', ''),
            'total'             => number_format($total_calc, 2, '.', ''),
        ] + $summary, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // =================================================
    // DEFAULT: Acción desconocida
    // =================================================
    default: {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción no válida'] + cart_summary(), JSON_UNESCAPED_UNICODE);
        exit;
    }
}