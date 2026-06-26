<?php
require_once __DIR__ . '/Security.php';
Security::initSession();

if (empty($_SESSION['idTutor'])) {
    header('Location: /vistas/login.php');
    exit;
}

// Bloquear acciones hasta que se cambie la contraseña temporal
if (!empty($_SESSION['must_change_password'])) {
    header('Location: /vistas/cambiar_password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken(null, false)) {
    require __DIR__ . '/../vistas/error.php';
    exit;
}

require_once __DIR__ . '/SuspensionGuard.php';
