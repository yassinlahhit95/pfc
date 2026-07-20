<?php
// Helpers compartidos de los controladores del catálogo de ciclos.
require_once __DIR__ . '/../../../include/ImageOptimizer.php';

// Sube la imagen de portada de un ciclo a public/uploads/ofertaCiclos/.
// Devuelve el nombre de archivo, '' si no se envió nada, o null si el
// archivo no es válido (en ese caso deja el motivo en $msgError).
function cicloSubirImagen(&$msgError) {
    if (empty($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) return '';
    $file = $_FILES['imagen'];
    if ($file['error'] !== UPLOAD_ERR_OK) { $msgError = 'Error al subir la imagen.'; return null; }
    if ($file['size'] > 10 * 1024 * 1024) { $msgError = 'La imagen supera el máximo de 10 MB.'; return null; }

    $mimeExtMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($mimeExtMap[$mime])) { $msgError = 'Formato no permitido. Usa JPG, PNG o WebP.'; return null; }

    $dir = __DIR__ . '/../../../public/uploads/ofertaCiclos/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $nombre = 'ciclo_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];
    if (!move_uploaded_file($file['tmp_name'], $dir . $nombre)) {
        $msgError = 'No se pudo guardar la imagen.';
        return null;
    }
    ImageOptimizer::optimize($dir . $nombre, $mime);
    return $nombre;
}
