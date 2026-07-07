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
    header('Location: /vistas/login.php');
    exit;
}

// Bloquear acciones hasta que se cambie la contraseña temporal o se configure MFA
if (!empty($_SESSION['must_change_password'])) {
    if ($_isAjaxGuard) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Debes cambiar tu contraseña antes de continuar.']);
        exit;
    }
    header('Location: /vistas/cambiar_password.php');
    exit;
}
if (!empty($_SESSION['mfa_setup_required'])) {
    if ($_isAjaxGuard) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Debes configurar la autenticación en dos pasos.']);
        exit;
    }
    header('Location: /vistas/auth/mfa_configurar.php');
    exit;
}

// Para la ruta específica de toggle_feature.php y los controladores de la landing, delegamos/exceptuamos
// la validación estricta de CSRF para evitar conflictos de sesión con el iframe de previsualización.
$isToggleFeature = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/controladores/admin/configuracion/toggle_feature.php') !== false;
$isLanding       = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/controladores/admin/landing/') !== false;

if (!$isToggleFeature && !$isLanding && $_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken(null, false)) {
    if ($_isAjaxGuard) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.']);
        exit;
    }
    require __DIR__ . '/../vistas/error.php';
    exit;
}
