<?php
require_once __DIR__ . '/../modelos/conectar.php';
require_once __DIR__ . '/Security.php';
ob_start();
// Security::initSession() (no un session_start() a secas): así los flags de
// endurecimiento de cookies (Secure/HttpOnly/SameSite/strict_mode) sí se
// aplican — llamar a session_start() antes de cargar Security.php hacía que
// se saltaran en silencio.
Security::initSession();

$_isAjaxGuardSec = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
               && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (empty($_SESSION['idSecretaria'])) {
    if ($_isAjaxGuardSec) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Sesión expirada. Por favor recarga la página.']);
        exit;
    }
    header('Location: /vistas/login.php');
    exit;
}

if (!empty($_SESSION['must_change_password'])) {
    if ($_isAjaxGuardSec) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Debes cambiar tu contraseña antes de continuar.']);
        exit;
    }
    header('Location: /vistas/cambiar_password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken(null, false)) {
    if ($_isAjaxGuardSec) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.']);
        exit;
    }
    require __DIR__ . '/../vistas/error.php';
    exit;
}

require_once __DIR__ . '/SuspensionGuard.php';
