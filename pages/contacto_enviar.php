<?php
// contacto_enviar.php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', '0');
session_start();

require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function out($arr, $code=200){
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
  exit;
}

// Permitir solo POST/AJAX
$isAjax = isset($_POST['ajax']) || (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])==='xmlhttprequest'
);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$isAjax) {
  out(['ok'=>false, 'msg'=>'Método no permitido'], 405);
}

// Honeypot
if (!empty($_POST['hp_field'] ?? '')) {
  out(['ok'=>false, 'msg'=>'Error de validación'], 400);
}

// Sanitizar / validar
$nombre  = trim($_POST['nombre_completo'] ?? '');
$pais    = trim($_POST['pais'] ?? '');
$telefono= trim($_POST['telefono'] ?? '');
$email   = trim($_POST['email'] ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

if (!$nombre || mb_strlen($nombre) < 2) out(['ok'=>false, 'msg'=>'Nombre inválido'], 400);
if (!$pais) out(['ok'=>false, 'msg'=>'País requerido'], 400);
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) out(['ok'=>false, 'msg'=>'Email inválido'], 400);
if (!$mensaje || mb_strlen($mensaje) < 5) out(['ok'=>false, 'msg'=>'Mensaje muy corto'], 400);
if ($telefono && !preg_match('/^[0-9+\-\s()]{6,20}$/', $telefono)) out(['ok'=>false, 'msg'=>'Teléfono inválido'], 400);

// HTML bonito para el correo
$fecha = date('Y-m-d H:i:s');
$ip    = $_SERVER['REMOTE_ADDR'] ?? '';
$ua    = $_SERVER['HTTP_USER_AGENT'] ?? '';

$style = 'font-family:Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";';
$badge = 'display:inline-block;padding:2px 8px;border-radius:999px;background:#EEF2FF;color:#3730A3;font-size:12px;border:1px solid #C7D2FE;';
$card  = 'border:1px solid #E5E7EB;border-radius:16px;padding:20px;';

$html  = <<<HTML
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Nuevo mensaje de contacto</title>
</head>
<body style="$style;background:#F9FAFB;padding:24px;">
  <div style="max-width:640px;margin:0 auto;">
    <div style="$card;background:#fff;">
      <h2 style="margin:0 0 8px 0;font-size:20px;color:#111827;">Nuevo mensaje de contacto</h2>
      <div style="$badge">Formulario web</div>

      <table cellpadding="0" cellspacing="0" border="0" style="width:100%;margin-top:16px;border-collapse:separate;border-spacing:0 8px;">
        <tr><td style="width:180px;color:#6B7280;">Nombre</td><td style="color:#111827;"><strong>{$nombre}</strong></td></tr>
        <tr><td style="color:#6B7280;">Correo</td><td style="color:#111827;"><a href="mailto:{$email}">{$email}</a></td></tr>
        <tr><td style="color:#6B7280;">País</td><td style="color:#111827;">{$pais}</td></tr>
        <tr><td style="color:#6B7280;">Teléfono</td><td style="color:#111827;">{$telefono}</td></tr>
      </table>

      <div style="margin-top:16px;">
        <div style="color:#6B7280;margin-bottom:6px;">Mensaje</div>
        <div style="white-space:pre-wrap;color:#111827;line-height:1.6;border:1px solid #E5E7EB;border-radius:12px;padding:12px;background:#F9FAFB;">{nl2br(htmlspecialchars($mensaje, ENT_QUOTES,'UTF-8'))}</div>
      </div>

      <hr style="border:none;border-top:1px solid #E5E7EB;margin:20px 0;">
      <div style="font-size:12px;color:#6B7280;">
        Enviado el <strong>{$fecha}</strong><br>
        IP: {$ip}<br>
        Navegador: {$ua}
      </div>
    </div>
  </div>
</body>
</html>
HTML;

try {
  $mail = new PHPMailer(true);

  // Usar la función mail() de PHP
  $mail->isMail();

  // Remitente y destinatario
  $mail->setFrom('no-reply@tudominio.com', 'Diagnostika');
  $mail->addAddress('andreslg20@gmail.com', 'Andres Lopez');

  $mail->isHTML(true);
  $mail->CharSet = 'UTF-8';
  $mail->Subject = 'Nuevo mensaje de contacto - ' . $nombre;
  $mail->Body    = $html;
  $mail->AltBody = strip_tags("Nombre: $nombre\nEmail: $email\nPaís: $pais\nTeléfono: $telefono\n\nMensaje:\n$mensaje\n\nEnviado: $fecha, IP: $ip");

  $mail->send();

  out(['ok'=>true, 'msg'=>'Enviado correctamente']);

} catch (Exception $e) {
  error_log('[contacto_enviar] ' . $e->getMessage());
  out(['ok'=>false, 'msg'=>'No se pudo enviar el correo. Intenta nuevamente.'], 500);
}