<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/Security.php';

$_isAjaxGuard = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
             && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (empty($_SESSION['idAdmin'])) {
    if ($_isAjaxGuard) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Sesión expirada. Por favor recarga la página.']);
        exit;
    }
    require __DIR__ . '/../vistas/error.php';
    exit;
}

// Bloquear acciones hasta que se cambie la contraseña temporal o se configure MFA
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    if ($_isAjaxGuard) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Debes completar la configuración de tu cuenta antes de continuar.']);
        exit;
    }
    require __DIR__ . '/../vistas/error.php';
    exit;
}

// Para la ruta específica de toggle_feature.php, delegamos la validación CSRF al controlador
// para que pueda usar una comprobación no rotatoria (non-rotating) que permita toggles consecutivos.
$isToggleFeature = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/controladores/admin/configuracion/toggle_feature.php') !== false;

if (!$isToggleFeature && $_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken(null, false)) {
    if ($_isAjaxGuard) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.']);
        exit;
    }
    require __DIR__ . '/../vistas/error.php';
    exit;
}
