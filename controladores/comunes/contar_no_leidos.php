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
    echo json_encode(['count' => 0, 'chat_count' => 0]);
    exit;
}
session_write_close(); // release session lock before DB work

$count = 0;
$chat_count = 0;
$rol = '';

if (!empty($_SESSION['idAdmin'])) {
    $count = contarMensajesNoLeidosAdmin();
    $rol = 'admin';
} elseif (!empty($_SESSION['idProfesor'])) {
    $count = contarMensajesNoLeidosProfesor((int)$_SESSION['idProfesor']);
    $rol = 'profesor';
} elseif (!empty($_SESSION['idEstudiante'])) {
    $count = contarMensajesNoLeidosEstudiante((int)$_SESSION['idEstudiante']);
    $rol = 'estudiante';
} elseif (!empty($_SESSION['idTutor'])) {
    $count = 0; // tutores have no messaging system yet
    $rol = 'tutor';
} elseif (!empty($_SESSION['idSecretaria'])) {
    $count = 0;
    $rol = 'secretaria';
} else {
    http_response_code(403);
    echo json_encode(['count' => 0, 'chat_count' => 0]);
    exit;
}

if ($rol && $role_id) {
    $con = obtenerConexion();
    $st = mysqli_prepare($con, 
        'SELECT COUNT(*) as n FROM chat_mensajes m 
         JOIN chat_conversaciones c ON m.conversacion_id = c.id
         WHERE m.leido = 0 
           AND NOT (m.emisor_rol = ? AND m.emisor_id = ?)
           AND ((c.user_a_rol = ? AND c.user_a_id = ?) OR (c.user_b_rol = ? AND c.user_b_id = ?))');
    mysqli_stmt_bind_param($st, 'sisisisi', $rol, $role_id, $rol, $role_id, $rol, $role_id);
    mysqli_stmt_execute($st);
    $res = mysqli_stmt_get_result($st);
    $chat_count = (int)(mysqli_fetch_assoc($res)['n'] ?? 0);
}

echo json_encode(['count' => (int)$count, 'chat_count' => $chat_count]);
