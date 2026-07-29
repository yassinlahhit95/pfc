<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
Security::initSession();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/reclamaciones.php';
require_once __DIR__ . '/../../modelos/notificaciones.php';
require_once __DIR__ . '/../../modelos/aula.php';
require_once __DIR__ . '/../../include/Cache.php';

// idSecretaria estaba ausente de esta cadena — la sesión de secretaría nunca
// llegaba ni siquiera a resolver un $role_id, así que esta llamada devolvía
// 403 en cada sondeo para ese rol y su punto/contador nunca se actualizaba
// en vivo (solo se veía el valor calculado en el último render de página).
$role_id = $_SESSION['idAdmin'] ?? $_SESSION['idProfesor'] ?? $_SESSION['idEstudiante']
    ?? $_SESSION['idTutor'] ?? $_SESSION['idSecretaria'] ?? null;
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
    // Se suma con las notificaciones genéricas (asignación de ciclo/módulo,
    // etc. — modelos/notificaciones.php) y las de Aula Digital (entrega
    // enviada — modelos/aula.php) porque la campana compartida solo
    // entiende un contador; ver mismo patrón en la rama de secretaría.
    $count = contarMensajesNoLeidosProfesor((int)$_SESSION['idProfesor'])
        + contarNotificacionesNoLeidas((int)$_SESSION['idProfesor'], 'profesor')
        + contarNotificacionesAulaNoLeidas((int)$_SESSION['idProfesor'], 'profesor');
    $rol = 'profesor';
} elseif (!empty($_SESSION['idEstudiante'])) {
    $count = contarMensajesNoLeidosEstudiante((int)$_SESSION['idEstudiante'])
        + contarNotificacionesNoLeidas((int)$_SESSION['idEstudiante'], 'estudiante')
        + contarNotificacionesAulaNoLeidas((int)$_SESSION['idEstudiante'], 'estudiante');
    $rol = 'estudiante';
} elseif (!empty($_SESSION['idTutor'])) {
    $count = 0; // tutores have no messaging system yet
    $rol = 'tutor';
} elseif (!empty($_SESSION['idSecretaria'])) {
    require_once __DIR__ . '/../../modelos/panelDeControl.php';
    // Mismo "cualquier cosa nueva" que su badge de Admisiones + Mensajería en
    // el nav: se suman ambos en un único contador porque el punto/campana
    // compartidos (dashboard-shell.js) solo entienden un valor genérico.
    $count = contarMensajesNoLeidosSecretaria() + (int)(obtenerContadoresNavAdmin()['total_admisiones_pendientes'] ?? 0);
    $rol = 'secretaria';
} else {
    http_response_code(403);
    echo json_encode(['count' => 0, 'chat_count' => 0]);
    exit;
}

if ($rol && $role_id) {
    $chat_count = Cache::remember("chat_no_leidos_{$rol}_{$role_id}", 10, function () use ($rol, $role_id) {
        $con = obtenerConexion();
        $st = mysqli_prepare($con,
            'SELECT COUNT(*) as n FROM chat_mensajes m
             JOIN chat_conversaciones c ON m.conversacion_id = c.id
             WHERE m.leido = 0
               AND NOT (m.emisor_rol = ? AND m.emisor_id = ?)
               AND ((c.user_a_rol = ? AND c.user_a_id = ?) OR (c.user_b_rol = ? AND c.user_b_id = ?))');
        mysqli_stmt_bind_param($st, 'sisisi', $rol, $role_id, $rol, $role_id, $rol, $role_id);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        return (int)(mysqli_fetch_assoc($res)['n'] ?? 0);
    });
}

echo json_encode(['count' => (int)$count, 'chat_count' => $chat_count]);
