<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

// Block if SaaS platform has locked feature control
if (FeatureGuard::isLocked()) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Las funcionalidades están bloqueadas por la plataforma SaaS. Contacta con el proveedor para modificarlas.',
    ]);
    exit;
}

$feature = $_POST['feature'] ?? '';
$estado  = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

if (actualizarFeatureToggle($feature, $estado)) {
    FeatureGuard::clearCache();
    echo json_encode(['status' => 'success', 'message' => 'Configuración actualizada']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Función no válida o error al actualizar']);
}
