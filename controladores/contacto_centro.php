<?php
// ══════════════════════════════════════════════════════════════════════
// FORMULARIO DE CONTACTO DE LA LANDING DEL CENTRO
// ══════════════════════════════════════════════════════════════════════
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/comunes/email_helper.php';
require_once __DIR__ . '/../include/RateLimiter.php';
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/../modelos/conectar.php';
require_once __DIR__ . '/../modelos/configuracion.php';

// ══════════════════════════════════════════════════════════════════════
// LÍMITE DE TASA
// ══════════════════════════════════════════════════════════════════════
if (!RateLimiter::allow(obtenerConexion(), 'contacto_centro', 5, 300, 900)) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'msg' => 'Demasiadas solicitudes. Por favor, espera unos minutos.']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN CSRF
// ══════════════════════════════════════════════════════════════════════
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '', false)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Token de seguridad inválido. Recarga la página e intenta de nuevo.']);
    exit;
}

// Trampa para bots (honeypot)
if (!empty($_POST['website'])) {
    echo json_encode(['ok' => true, 'msg' => '¡Mensaje enviado! Te responderemos lo antes posible.']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$nombre   = trim($_POST['nombre']   ?? '');
$email    = trim($_POST['email']    ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$mensaje  = trim($_POST['mensaje']  ?? '');

if (!$nombre || !$email || !$mensaje) {
    echo json_encode(['ok' => false, 'msg' => 'Por favor, completa los campos obligatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'El correo electrónico no es válido.']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO — ENVÍO DE EMAIL AL CENTRO
// ══════════════════════════════════════════════════════════════════════
$cfg = obtenerConfiguracionCentro();
$nombreCentro = $cfg['nombreCentro'] ?: 'Centro de Formación';
$destino = filter_var($cfg['emailCentro'] ?? '', FILTER_VALIDATE_EMAIL)
    ? $cfg['emailCentro']
    : (defined('MAIL_FROM') ? MAIL_FROM : '');

if (!$destino) {
    echo json_encode(['ok' => false, 'msg' => 'El formulario no está disponible en este momento. Contacta por teléfono.']);
    exit;
}

$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

$asunto = "Nueva consulta web — " . $h($nombre);

$filaTelefono = '';
if (!empty($telefono)) {
    $filaTelefono = "<tr><td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Teléfono</td>
       <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>" . $h($telefono) . "</td></tr>";
}

$html = "
<div style='font-family:sans-serif;max-width:600px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;'>
  <div style='background:#1f2937;padding:24px 28px;'>
    <h2 style='color:#fff;margin:0;font-size:20px;'>Nueva consulta desde la web</h2>
    <p style='color:#d1d5db;margin:4px 0 0;font-size:14px;'>" . $h($nombreCentro) . "</p>
  </div>
  <table style='width:100%;border-collapse:collapse;font-size:15px;'>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;width:130px;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Nombre</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>" . $h($nombre) . "</td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Email</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'><a href='mailto:" . $h($email) . "' style='color:#1f2937;'>" . $h($email) . "</a></td>
    </tr>
    $filaTelefono
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;vertical-align:top;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Mensaje</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>" . nl2br($h($mensaje)) . "</td>
    </tr>
  </table>
  <div style='padding:16px 28px;background:#fafafa;color:#9ca3af;font-size:12px;'>
    Enviado desde la web del centro · " . date('d/m/Y H:i') . " · IP: " . $h($_SERVER['REMOTE_ADDR'] ?? 'desconocida') . "
  </div>
</div>
";

$resultado = sendEmail($destino, $asunto, $html, $nombreCentro . ' - Web');

if ($resultado) {
    echo json_encode(['ok' => true, 'msg' => '¡Mensaje enviado! Te responderemos lo antes posible.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.']);
}
