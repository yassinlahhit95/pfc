<?php
// Sube una imagen del constructor a Cloudflare R2 (AJAX multipart). URL
// pública permanente, sin firma — mismo motivo que blogSubirImagen(): es
// contenido de marketing sin control de acceso hoy, y una URL firmada
// caducaría rompiendo la imagen en el sitio publicado.
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/ImageOptimizer.php';
require_once __DIR__ . '/../../../include/R2Client.php';
require_once __DIR__ . '/../../../modelos/log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']);
    exit;
}

if (!Security::validateCSRFToken(null, false)) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
    exit;
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'No se recibió ninguna imagen.']);
    exit;
}

$file = $_FILES['imagen'];
// El límite real ya lo impone upload_max_filesize (20M, .user.ini); esta
// comprobación solo da un mensaje claro en vez de un error genérico de PHP.
if ($file['size'] > 20 * 1024 * 1024) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'El archivo supera el máximo de 20 MB.']);
    exit;
}

$mimeExtMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'video/mp4' => 'mp4'];
$mime = mime_content_type($file['tmp_name']);

if (!isset($mimeExtMap[$mime])) {
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext === 'mp4' && in_array($mime, ['application/octet-stream', 'video/quicktime', 'application/mp4', 'video/x-m4v'])) {
        $mime = 'video/mp4';
    }
}

if (!isset($mimeExtMap[$mime])) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Formato no permitido. Usa JPG, PNG, WebP o MP4.']);
    exit;
}

$filename = 'landing_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];

if ($mime !== 'video/mp4') {
    ImageOptimizer::optimize($file['tmp_name'], $mime); // optimizar el temporal ANTES de subir a R2
}

$bytes = file_get_contents($file['tmp_name']);
$subioOk = $bytes !== false && R2Client::putObject('landing/' . $filename, $bytes, $mime);
@unlink($file['tmp_name']);

if (!$subioOk) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'No se pudo guardar la imagen.']);
    exit;
}

registrarAccion('insertar', 'landing', null, 'Imagen subida: ' . $filename);
ob_clean();
$url = R2Client::publicUrl('landing/' . $filename);
echo json_encode(['ok' => true, 'msg' => 'Imagen subida.', 'filename' => $url, 'url' => $url]);
