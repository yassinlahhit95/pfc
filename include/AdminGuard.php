<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/Security.php';

if (empty($_SESSION['idAdmin'])) {
    http_response_code(403);
    exit('Acceso denegado');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !Security::validateCSRFToken()) {
    http_response_code(403);
    exit('Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
}
