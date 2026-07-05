<?php
// Publica el borrador: copia draft → live (AJAX JSON).
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/landing.php';
require_once __DIR__ . '/../../../modelos/log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']);
    exit;
}

if (!listarSeccionesLanding('draft')) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'El borrador está vacío. Aplica una plantilla o añade secciones antes de publicar.']);
    exit;
}

$ok = publicarLanding();
if ($ok) {
    registrarAccion('actualizar', 'landing', null, 'Landing publicada');
}
ob_clean();
echo json_encode($ok
    ? ['ok' => true, 'msg' => 'Landing publicada. Los cambios ya están visibles en la web.']
    : ['ok' => false, 'msg' => 'No se pudo publicar la landing.']);
