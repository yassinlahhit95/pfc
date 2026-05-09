<?php
header('Content-Type: application/json');

// Solo acepta peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

// Honeypot anti-spam: si viene relleno, es un bot
if (!empty($_POST['website'])) {
    echo json_encode(['ok' => true, 'msg' => '¡Mensaje enviado! Te responderemos en menos de 24h.']);
    exit;
}

$nombre  = trim($_POST['nombre']  ?? '');
$email   = trim($_POST['email']   ?? '');
$centro  = trim($_POST['centro']  ?? '');
$plan    = trim($_POST['plan']    ?? 'No especificado');
$mensaje = trim($_POST['mensaje'] ?? '');

if (!$nombre || !$email || !$centro) {
    echo json_encode(['ok' => false, 'msg' => 'Por favor, completa los campos obligatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'El correo electrónico no es válido.']);
    exit;
}

require_once __DIR__ . '/comunes/email_helper.php';

$asunto = "Nueva consulta SaaS — " . htmlspecialchars($nombre) . " (" . htmlspecialchars($centro) . ")";

$filasMensaje = $mensaje
    ? "<tr><td style='padding:10px 12px;color:#6b7280;font-weight:600;vertical-align:top;border-bottom:1px solid #f3f4f6;'>Mensaje</td>
       <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>".nl2br(htmlspecialchars($mensaje))."</td></tr>"
    : '';

$html = "
<div style='font-family:sans-serif;max-width:600px;margin:auto;background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;'>
  <div style='background:#4f46e5;padding:24px 28px;'>
    <h2 style='color:#fff;margin:0;font-size:20px;'>Nueva consulta de plan SaaS</h2>
    <p style='color:#c7d2fe;margin:4px 0 0;font-size:14px;'>AulaPro — Landing Page</p>
  </div>
  <table style='width:100%;border-collapse:collapse;font-size:15px;'>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;width:130px;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Nombre</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>".htmlspecialchars($nombre)."</td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Email</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'><a href='mailto:".htmlspecialchars($email)."' style='color:#4f46e5;'>".htmlspecialchars($email)."</a></td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Centro</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>".htmlspecialchars($centro)."</td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Plan</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>".htmlspecialchars($plan ?: 'No especificado')."</td>
    </tr>
    $filasMensaje
  </table>
  <div style='padding:16px 28px;background:#fafafa;color:#9ca3af;font-size:12px;'>
    Enviado desde yassin.agency · ".date('d/m/Y H:i')." · IP: ".htmlspecialchars($_SERVER['REMOTE_ADDR'] ?? 'desconocida')."
  </div>
</div>
";

$ok = sendEmail('yassin.lahhit@gmail.com', $asunto, $html);

if ($ok) {
    echo json_encode(['ok' => true, 'msg' => '¡Mensaje enviado! Te responderemos en menos de 24h.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.']);
}
