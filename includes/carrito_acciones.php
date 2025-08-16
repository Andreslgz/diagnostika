<?php
declare(strict_types=1);

session_start();

// Este endpoint devuelve SOLO JSON
ini_set('display_errors', '0');
ini_set('log_errors', '1');
header('Content-Type: application/json; charset=utf-8');

// Evita contaminar la salida (BOM/espacios/echo en includes)
while (ob_get_level() > 0) { ob_end_clean(); }

// Asegura estructura de carrito en sesión
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

/**
 * Recalcula y devuelve resumen del carrito
 */
function cart_summary(): array {
    $count = 0;
    $total = 0.0;
    foreach ($_SESSION['carrito'] as $it) {
        $qty = (int)($it['cantidad'] ?? 0);
        $price = (float)($it['precio'] ?? 0);
        $count += $qty;
        $total += $qty * $price;
    }
    return [
        'cart_count' => $count,
        'cart_total' => number_format($total, 2, '.', ''),
        'cart_empty' => empty($_SESSION['carrito']),
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido'] + cart_summary(), JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    // Agregar un producto (id_producto, cantidad opcional)
    case 'add': {
        $id = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
        $cantidad = isset($_POST['cantidad']) ? max(1, (int)$_POST['cantidad']) : 1;

        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Producto inválido'] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Si ya existe, incrementa; de lo contrario debes traer datos del producto.
        // Aquí asumo que ya fue agregado antes con sus datos en sesión.
        if (isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad'] += $cantidad;
        } else {
            // Si no tienes los datos en sesión, aquí deberías consultar a la BD.
            // Placeholder defensivo: evita agregar sin datos mínimos.
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Datos del producto no disponibles'] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Producto añadido'] + cart_summary(), JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Actualizar cantidad por índice de la lista (index) con delta (+1/-1) o set_qty
    case 'update': {
        $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
        $hasDelta = isset($_POST['delta']);
        $hasSet = isset($_POST['set_qty']);

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

        // Si la cantidad es 0 o menos, elimina el ítem y reindexa
        if ((int)$_SESSION['carrito'][$index]['cantidad'] <= 0) {
            unset($_SESSION['carrito'][$index]);
            $_SESSION['carrito'] = array_values($_SESSION['carrito']); // reindexar
            echo json_encode(['success' => true, 'item_removed' => true] + cart_summary(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        // Subtotal del ítem actualizado
        $qty = (int)$_SESSION['carrito'][$index]['cantidad'];
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

    // Eliminar por índice (index)
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

    default: {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Acción no válida'] + cart_summary(), JSON_UNESCAPED_UNICODE);
        exit;
    }
}