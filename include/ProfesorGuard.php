<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/Security.php';

$_isAjaxGuard = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
             && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (empty($_SESSION['idProfesor'])) {
    if ($_isAjaxGuard) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Sesión expirada. Por favor recarga la página.']);
        exit;
    }
    require __DIR__ . '/../vistas/error.php';
    exit;
}

if (!empty($_SESSION['must_change_password'])) {
    if ($_isAjaxGuard) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Debes cambiar tu contraseña antes de continuar.']);
        exit;
    }
    header('Location: /vistas/cambiar_password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken(null, false)) {
    if ($_isAjaxGuard) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.']);
        exit;
    }
    require __DIR__ . '/../vistas/error.php';
    exit;
}

require_once __DIR__ . '/SuspensionGuard.php';
