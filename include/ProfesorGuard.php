<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/Security.php';

if (empty($_SESSION['idProfesor'])) {
    require __DIR__ . '/../vistas/error.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken()) {
    require __DIR__ . '/../vistas/error.php';
    exit;
}
