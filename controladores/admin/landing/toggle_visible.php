<?php
// Muestra/oculta una sección del borrador (AJAX JSON).
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

$idSeccion = (int)($_POST['idSeccion'] ?? 0);
$visible   = (int)($_POST['visible'] ?? 0) === 1 ? 1 : 0;
if ($idSeccion <= 0 || !obtenerSeccionPorId($idSeccion)) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Sección no encontrada.']);
    exit;
}

$ok = actualizarVisibleSeccion($idSeccion, $visible);
if ($ok) {
    registrarAccion('actualizar', 'landing', $idSeccion, 'Sección ' . ($visible ? 'mostrada' : 'oculta'));
}
ob_clean();
echo json_encode($ok
    ? ['ok' => true, 'msg' => $visible ? 'Sección visible.' : 'Sección oculta.', 'visible' => $visible]
    : ['ok' => false, 'msg' => 'No se pudo actualizar la sección.']);
