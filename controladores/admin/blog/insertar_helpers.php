<?php
// Helpers compartidos de los controladores del blog.
require_once __DIR__ . '/../../../include/ImageOptimizer.php';
require_once __DIR__ . '/../../../include/R2Client.php';

// Sube una imagen a Cloudflare R2 (usada tanto para la portada del post como
// para imágenes insertadas en el contenido) mediante URL pública permanente,
// NO firmada — a diferencia de los documentos protegidos (TFG, aula, etc.),
// estas imágenes no tienen control de acceso hoy y, sobre todo, las
// insertadas en el contenido quedan grabadas para siempre en el HTML del
// post (ver subir_imagen_impl.php) — una URL firmada caducaría y rompería
// la imagen de forma permanente sin ningún mecanismo para renovarla.
// Devuelve el nombre de archivo, '' si no se envió nada, o null si el
// archivo no es válido (en ese caso deja el motivo en $msgError).
function blogSubirImagen(&$msgError) {
    if (empty($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) return '';
    $file = $_FILES['imagen'];
    if ($file['error'] !== UPLOAD_ERR_OK) { $msgError = 'Error al subir la imagen.'; return null; }
    if ($file['size'] > 10 * 1024 * 1024) { $msgError = 'La imagen supera el máximo de 10 MB.'; return null; }

    $mimeExtMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($mimeExtMap[$mime])) { $msgError = 'Formato no permitido. Usa JPG, PNG o WebP.'; return null; }

    $nombre = 'blog_' . bin2hex(random_bytes(6)) . '.' . $mimeExtMap[$mime];

    ImageOptimizer::optimize($file['tmp_name'], $mime); // optimizar el temporal ANTES de subir a R2
    $bytes = file_get_contents($file['tmp_name']);
    $subioOk = $bytes !== false && R2Client::putObject('blog/' . $nombre, $bytes, $mime);
    @unlink($file['tmp_name']);

    if (!$subioOk) {
        $msgError = 'No se pudo guardar la imagen.';
        return null;
    }
    return $nombre;
}
