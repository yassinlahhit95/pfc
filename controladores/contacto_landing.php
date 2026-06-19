<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/comunes/email_helper.php';
require_once __DIR__ . '/../include/RateLimiter.php';
require_once __DIR__ . '/../modelos/conectar.php';

// ══════════════════════════════════════════════════════════════════════
// LÍMITE DE TASA
// ══════════════════════════════════════════════════════════════════════
if (!RateLimiter::allow(obtenerConexion(), 'contacto_landing', 5, 300, 900)) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'msg' => 'Demasiadas solicitudes. Por favor, espera unos minutos.']);
    exit;
}

// Trampa para bots (honeypot)
if (!empty($_POST['website'])) {
    echo json_encode(['ok' => true, 'msg' => '¡Mensaje enviado! Te responderemos en menos de 24h.']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$nombre  = trim($_POST['nombre']  ?? '');
$email   = trim($_POST['email']   ?? '');
$centro  = trim($_POST['centro']  ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

$planesPermitidos = ['Plan Académico', 'Plan Completo', 'Duda General', 'No especificado'];
$planRaw = trim($_POST['plan'] ?? '');
$plan = in_array($planRaw, $planesPermitidos, true) ? $planRaw : 'No especificado';

if (!$nombre || !$email || !$centro) {
    echo json_encode(['ok' => false, 'msg' => 'Por favor, completa los campos obligatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'El correo electrónico no es válido.']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO — ENVÍO DE EMAIL
// ══════════════════════════════════════════════════════════════════════
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

$asunto = "Nueva consulta SaaS — " . $h($nombre) . " (" . $h($centro) . ")";

$filasMensaje = '';
if (!empty($mensaje)) {
    $filasMensaje = "<tr><td style='padding:10px 12px;color:#6b7280;font-weight:600;vertical-align:top;border-bottom:1px solid #f3f4f6;'>Mensaje</td>
       <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>".nl2br($h($mensaje))."</td></tr>";
}

$html = "
<div style='font-family:sans-serif;max-width:600px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;'>
  <div style='background:#4f46e5;padding:24px 28px;'>
    <h2 style='color:#fff;margin:0;font-size:20px;'>Nueva consulta de plan SaaS</h2>
    <p style='color:#c7d2fe;margin:4px 0 0;font-size:14px;'>AulaPro — Landing Page</p>
  </div>
  <table style='width:100%;border-collapse:collapse;font-size:15px;'>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;width:130px;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Nombre</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>" . $h($nombre) . "</td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Email</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'><a href='mailto:" . $h($email) . "' style='color:#4f46e5;'>" . $h($email) . "</a></td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Centro</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>" . $h($centro) . "</td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Plan</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>" . $h($plan) . "</td>
    </tr>
    $filasMensaje
  </table>
  <div style='padding:16px 28px;background:#fafafa;color:#9ca3af;font-size:12px;'>
    Enviado desde yassin.agency · ".date('d/m/Y H:i')." · IP: " . $h($_SERVER['REMOTE_ADDR'] ?? 'desconocida') . "
  </div>
</div>
";

$resultado = sendEmail('yassin.lahhit@gmail.com', $asunto, $html, 'AulaPro - Consultas Web');

if ($resultado) {
    echo json_encode(['ok' => true, 'msg' => '¡Mensaje enviado! Te responderemos en menos de 24h.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.']);
}
