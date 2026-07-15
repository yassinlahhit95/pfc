<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requireJson('feature_landing');
require_once __DIR__ . "/insertar_helpers.php";

header('Content-Type: application/json');

if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida. Recarga la página e inténtalo de nuevo.']);
    exit;
}

$msgError = '';
$nombre = blogSubirImagenPortada($msgError);
if ($nombre === null) {
    echo json_encode(['ok' => false, 'msg' => $msgError]);
    exit;
}
if ($nombre === '') {
    echo json_encode(['ok' => false, 'msg' => 'No se ha seleccionado ninguna imagen.']);
    exit;
}

echo json_encode(['ok' => true, 'url' => '/public/uploads/blog/' . $nombre]);
