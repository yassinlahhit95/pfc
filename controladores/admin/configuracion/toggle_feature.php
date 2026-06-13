<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

$feature = $_POST['feature'] ?? '';
$estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

if (actualizarFeatureToggle($feature, $estado)) {
    echo json_encode(['status' => 'success', 'message' => 'Configuración actualizada']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar']);
}
?>
