<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
// Bloquear si la plataforma SaaS ha bloqueado el control de funcionalidades
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
    echo json_encode(['status' => 'success', 'message' => 'La configuración de la funcionalidad ha sido actualizada correctamente.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'La característica especificada no es válida o se produjo un error al actualizar la configuración.']);
}
