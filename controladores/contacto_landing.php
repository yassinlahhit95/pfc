<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido']);
    exit;
}

if (!empty($_REQUEST['website'])) {
    echo json_encode(['ok' => true, 'msg' => '¡Mensaje enviado! Te responderemos en menos de 24h.']);
    exit;
}

$nombre  = trim($_REQUEST['nombre']  ?? '');
$email   = trim($_REQUEST['email']   ?? '');
$centro  = trim($_REQUEST['centro']  ?? '');
$plan    = trim($_REQUEST['plan']    ?? 'No especificado');
$mensaje = trim($_REQUEST['mensaje'] ?? '');

if (!$nombre || !$email || !$centro) {
    echo json_encode(['ok' => false, 'msg' => 'Por favor, completa los campos obligatorios.']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
    echo json_encode(['ok' => false, 'msg' => 'El correo electrónico no es válido.']);
    exit;
}

require_once __DIR__ . '/comunes/email_helper.php';

$asunto = "Nueva consulta SaaS — $nombre ($centro)";

$filasMensaje = '';
if (!empty($mensaje)) {
    $filasMensaje = "<tr><td style='padding:10px 12px;color:#6b7280;font-weight:600;vertical-align:top;border-bottom:1px solid #f3f4f6;'>Mensaje</td>
       <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>".nl2br($mensaje)."</td></tr>";
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
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>$nombre</td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Email</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'><a href='mailto:$email' style='color:#4f46e5;'>$email</a></td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Centro</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>$centro</td>
    </tr>
    <tr>
      <td style='padding:10px 12px;color:#6b7280;font-weight:600;border-bottom:1px solid #f3f4f6;background:#fafafa;'>Plan</td>
      <td style='padding:10px 12px;border-bottom:1px solid #f3f4f6;'>$plan</td>
    </tr>
    $filasMensaje
  </table>
  <div style='padding:16px 28px;background:#fafafa;color:#9ca3af;font-size:12px;'>
    Enviado desde yassin.agency · ".date('d/m/Y H:i')." · IP: ".($_SERVER['REMOTE_ADDR'] ?? 'desconocida')."
  </div>
</div>
";

$resultado = sendEmail('yassin.lahhit@gmail.com', $asunto, $html, 'AulaPro - Consultas Web');

if ($resultado) {
    echo json_encode(['ok' => true, 'msg' => '¡Mensaje enviado! Te responderemos en menos de 24h.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Error al enviar el mensaje. Por favor, inténtalo de nuevo más tarde.']);
}
