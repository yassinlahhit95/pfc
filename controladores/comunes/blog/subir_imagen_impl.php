<?php
// ══════════════════════════════════════════════════════════════════════
// Implementación compartida de controladores/{admin,secretaria}/blog/subir_imagen_contenido.php
// El wrapper de cada rol ya validó el Guard correspondiente antes de
// hacer require de este archivo.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../admin/blog/insertar_helpers.php";

header('Content-Type: application/json');

if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida. Recarga la página e inténtalo de nuevo.']);
    exit;
}

$msgError = '';
$nombre = blogSubirImagen($msgError);
if ($nombre === null) {
    echo json_encode(['ok' => false, 'msg' => $msgError]);
    exit;
}
if ($nombre === '') {
    echo json_encode(['ok' => false, 'msg' => 'No se ha seleccionado ninguna imagen.']);
    exit;
}

require_once __DIR__ . "/../../../include/R2Client.php";
echo json_encode(['ok' => true, 'url' => R2Client::publicUrl('blog/' . $nombre)]);
