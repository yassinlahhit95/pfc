<?php
// Descarta el borrador: copia live → draft (AJAX JSON).
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/landing.php';
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

$ok = descartarBorradorLanding();
if ($ok) {
    registrarAccion('actualizar', 'landing', null, 'Borrador de la landing descartado');
}
ob_clean();
echo json_encode($ok
    ? ['ok' => true, 'msg' => 'Cambios descartados. El borrador vuelve a la última versión publicada.']
    : ['ok' => false, 'msg' => 'No hay versión publicada de la que restaurar.']);
