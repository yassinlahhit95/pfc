<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida.']);
    exit;
}

$estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

if (actualizarFeatureToggle('feature_subida_tfg', $estado)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar.']);
}
