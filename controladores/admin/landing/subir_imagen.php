<?php
// Sube una imagen del constructor a public/uploads/landing/ (AJAX multipart).
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/ImageOptimizer.php';
require_once __DIR__ . '/../../../modelos/log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']);
    exit;
}

if (!Security::validateCSRFToken()) {
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

$uploadDir = __DIR__ . '/../../../public/uploads/landing/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = 'landing_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];
if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'No se pudo guardar la imagen.']);
    exit;
}

if ($mime !== 'video/mp4') {
    ImageOptimizer::optimize($uploadDir . $filename, $mime);
}

registrarAccion('insertar', 'landing', null, 'Imagen subida: ' . $filename);
ob_clean();
echo json_encode(['ok' => true, 'msg' => 'Imagen subida.', 'filename' => $filename,
                  'url' => '/public/uploads/landing/' . $filename]);
