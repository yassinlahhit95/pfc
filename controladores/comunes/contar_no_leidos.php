<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/reclamaciones.php';

$count = 0;

if (!empty($_SESSION['idAdmin'])) {
    $count = contarMensajesNoLeidosAdmin();
} elseif (!empty($_SESSION['idProfesor'])) {
    $count = contarMensajesNoLeidosProfesor((int)$_SESSION['idProfesor']);
} elseif (!empty($_SESSION['idEstudiante'])) {
    $count = contarMensajesNoLeidosEstudiante((int)$_SESSION['idEstudiante']);
} else {
    http_response_code(403);
    echo json_encode(['count' => 0]);
    exit;
}

echo json_encode(['count' => (int)$count]);
