<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';

if (FeatureGuard::isLocked()) {
    echo json_encode(['status' => 'error', 'message' => 'Las funcionalidades están bloqueadas por la plataforma SaaS.']);
    exit;
}

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['status' => 'error', 'message' => 'La sesión ha expirado o la solicitud no es válida. Por favor, inténtelo de nuevo.']);
    exit;
}

$estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

if (actualizarFeatureToggle('feature_subida_tfg', $estado)) {
    FeatureGuard::clearCache();
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el estado de subida en la configuración del sistema.']);
}
