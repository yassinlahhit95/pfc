<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/secretarias.php';
require_once __DIR__ . '/../../../modelos/log.php';

header('Content-Type: application/json');

if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
    exit;
}

$id     = (int)($_POST['idSecretaria'] ?? 0);
$activo = (int)($_POST['activo'] ?? 0) === 1 ? 1 : 0;

if (!$id) {
    echo json_encode(['ok' => false, 'msg' => 'ID no válido.']);
    exit;
}

if (toggleActivoSecretaria($id, $activo)) {
    registrarAccion($activo ? 'activar' : 'desactivar', 'secretarias', $id);
    echo json_encode(['ok' => true, 'msg' => 'Estado actualizado correctamente.']);
} else {
    echo json_encode(['ok' => false, 'msg' => 'Error al actualizar el estado.']);
}
