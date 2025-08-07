<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config.php';

use Mpdf\Mpdf;

if (!isset($_GET['id'])) {
    die("ID de orden no especificado.");
}

$id_orden = (int) $_GET['id'];

$orden = $database->get("ordenes", [
    "[>]usuarios" => ["id_usuario" => "id_usuario"],
    "[>]productos" => ["id_producto" => "id_producto"]
], [
    "ordenes.id_orden",
    "ordenes.invoice",
    "ordenes.fecha",
    "ordenes.total",
    "ordenes.estado",
    "ordenes.comentario",
    "ordenes.metodo_pago",
    "usuarios.nombre",
    "usuarios.apellidos",
    "usuarios.direccion",
    "productos.nombre(producto)",
    "productos.precio"
], [
    "ordenes.id_orden" => $id_orden
]);

if (!$orden) {
    die("Orden no encontrada.");
}

// HTML del PDF
$html = '
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11pt; }
        .header, .footer { width: 100%; text-align: center; position: fixed; }
        .header { top: 0px; }
        .footer { bottom: 0px; font-size: 9pt; color: #888; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }

        .empresa { text-align: right; font-size: 10pt; }
        .empresa h2 { margin: 0; font-size: 14pt; }
        .estado { padding: 4px 8px; color: #fff; border-radius: 4px; display: inline-block; font-size: 10pt; }
        .pagado { background-color: #28a745; }
        .pendiente { background-color: #ffc107; color: #000; }
        .cancelado { background-color: #6c757d; }

        .total { text-align: right; font-size: 14pt; margin-top: 10px; }
        .comentario { margin-top: 20px; }
    </style>
</head>
<body>

<table>
    <tr>
        <td style="width: 50%;">
            <img src="http://10.211.55.3/tienda/panel/assets/images/brand/logo-3.png" style="height: 50px;">
        </td>
        <td class="empresa" style="width: 50%;">
            <h2>DIAGNOSTIKA DIESEL GLOBAL</h2>
            <p>Av. Principal 123, Lima, Perú<br>
            Tel: +51 987 654 321<br>
            contacto@tiendaxyz.com</p>
            <p><strong>Fecha emisión:</strong> ' . date('d/m/Y') . '</p>
        </td>
    </tr>
</table>

<h2 style="margin-top: 30px;">Factura # ' . htmlspecialchars($orden['invoice']) . '</h2>

<table>
    <tr>
        <td><strong>Cliente:</strong> ' . htmlspecialchars($orden['nombre'] . ' ' . $orden['apellidos']) . '</td>
        <td><strong>Dirección:</strong> ' . htmlspecialchars($orden['direccion']) . '</td>
    </tr>
    <tr>
        <td><strong>Método de pago:</strong> ' . htmlspecialchars($orden['metodo_pago']) . '</td>
        <td><strong>Estado:</strong> 
            <span class="estado ' . $orden['estado'] . '">' . strtoupper($orden['estado']) . '</span>
        </td>
    </tr>
</table>

<table style="margin-top: 20px;">
    <thead>
        <tr>
            <th>#</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>' . htmlspecialchars($orden['producto']) . '</td>
            <td>1</td>
            <td>$' . number_format($orden['precio'], 2) . '</td>
            <td>$' . number_format($orden['precio'], 2) . '</td>
        </tr>
    </tbody>
</table>

<div class="total"><strong>Total: $' . number_format($orden['total'], 2) . ' USD</strong></div>';

if (!empty($orden['comentario'])) {
    $html .= '<div class="comentario"><strong>Comentario:</strong><br>' . nl2br(htmlspecialchars($orden['comentario'])) . '</div>';
}

$html .= '</body></html>';

// Crear PDF con mPDF
$mpdf = new Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output("Factura_" . $orden['invoice'] . ".pdf", 'I'); // 'I' para mostrar en navegador
exit;
