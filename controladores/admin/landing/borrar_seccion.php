<?php
// Elimina una sección del borrador (AJAX JSON, compatible con modal-borrar.js).
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/landing.php';
require_once __DIR__ . '/../../../modelos/log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Método no permitido.']);
    exit;
}



$idSeccion = (int)($_POST['idSeccion'] ?? 0);
$seccion   = $idSeccion > 0 ? obtenerSeccionPorId($idSeccion) : null;
if (!$seccion) {
    ob_clean();
    echo json_encode(['ok' => false, 'msg' => 'Sección no encontrada.']);
    exit;
}

$ok = borrarSeccionLanding($idSeccion);
if ($ok) {
    registrarAccion('borrar', 'landing', $idSeccion, 'Sección «' . $seccion['tipo'] . '» eliminada');
}
ob_clean();
echo json_encode($ok
    ? ['ok' => true, 'msg' => 'Sección eliminada.']
    : ['ok' => false, 'msg' => 'No se pudo eliminar la sección.']);
