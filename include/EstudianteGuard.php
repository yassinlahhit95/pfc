<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/Security.php';

if (empty($_SESSION['idEstudiante'])) {
    require __DIR__ . '/../vistas/error.php';
    exit;
}

// Bloquear acciones hasta que se cambie la contraseña temporal
if (!empty($_SESSION['must_change_password'])) {
    require __DIR__ . '/../vistas/error.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken()) {
    require __DIR__ . '/../vistas/error.php';
    exit;
}

require_once __DIR__ . '/SuspensionGuard.php';
