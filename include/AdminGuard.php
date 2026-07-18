<?php
ob_start();
require_once __DIR__ . '/Security.php';
// Security::initSession() (not a bare session_start()) so the cookie-hardening
// flags (Secure/HttpOnly/SameSite/strict_mode) actually get applied — calling
// session_start() before Security.php loaded used to skip them silently.
Security::initSession();

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

// toggle_feature.php se exceptúa de la validación estricta de CSRF por su flujo propio.
// Los controladores de la landing SÍ validan CSRF: el token no rota (rotate=false),
// así que sobrevive a las múltiples llamadas AJAX del constructor, y todas las
// peticiones (constructor, plantillas, onboarding) lo envían.
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
