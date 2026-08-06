<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';

if (FeatureGuard::isLocked()) {
    echo json_encode(['ok' => false, 'msg' => 'Las funcionalidades están bloqueadas por la plataforma SaaS.']);
    exit;
}

// rotate=false: este toggle se llama repetidamente por fetch() desde una página
// que nunca recarga — con el rotate por defecto, el primer clic borraría el
// token y todos los siguientes fallarían con "solicitud no válida" (ver la
// regla de CSRF en CLAUDE.md).
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '', false)) {
    echo json_encode(['ok' => false, 'msg' => 'La sesión ha expirado o la solicitud no es válida. Por favor, inténtelo de nuevo.']);
    exit;
}

$estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

if (actualizarFeatureToggle('feature_subida_tfg', $estado)) {
    FeatureGuard::clearCache();
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'msg' => 'No se pudo actualizar el estado de subida en la configuración del sistema.']);
}
