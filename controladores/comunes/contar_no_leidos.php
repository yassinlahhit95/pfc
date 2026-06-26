<?php
require_once __DIR__ . '/../../include/Security.php';
Security::initSession();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/reclamaciones.php';

$role_id = $_SESSION['idAdmin'] ?? $_SESSION['idProfesor'] ?? $_SESSION['idEstudiante'] ?? $_SESSION['idTutor'] ?? null;
if (!$role_id) {
    http_response_code(403);
    echo json_encode(['count' => 0]);
    exit;
}
session_write_close(); // release session lock before DB work

$count = 0;

if (!empty($_SESSION['idAdmin'])) {
    $count = contarMensajesNoLeidosAdmin();
} elseif (!empty($_SESSION['idProfesor'])) {
    $count = contarMensajesNoLeidosProfesor((int)$_SESSION['idProfesor']);
} elseif (!empty($_SESSION['idEstudiante'])) {
    $count = contarMensajesNoLeidosEstudiante((int)$_SESSION['idEstudiante']);
} elseif (!empty($_SESSION['idTutor'])) {
    $count = 0; // tutores have no messaging system yet
} else {
    http_response_code(403);
    echo json_encode(['count' => 0]);
    exit;
}

echo json_encode(['count' => (int)$count]);
