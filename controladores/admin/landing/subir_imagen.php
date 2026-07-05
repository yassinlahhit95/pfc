<?php
// Sube una imagen del constructor a public/uploads/landing/ (AJAX multipart).
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']);
    exit;
}

if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'No se recibió ninguna imagen.']);
    exit;
}

$file = $_FILES['imagen'];
if ($file['size'] > 2 * 1024 * 1024) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'La imagen supera el máximo de 2 MB.']);
    exit;
}

$mimeExtMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$mime = mime_content_type($file['tmp_name']);
if (!isset($mimeExtMap[$mime])) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Formato no permitido. Usa JPG, PNG o WebP.']);
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

registrarAccion('insertar', 'landing', null, 'Imagen subida: ' . $filename);
ob_clean();
echo json_encode(['ok' => true, 'msg' => 'Imagen subida.', 'filename' => $filename,
                  'url' => '/public/uploads/landing/' . $filename]);
