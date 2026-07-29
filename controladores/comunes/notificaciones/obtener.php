<?php
require_once __DIR__ . '/../../../modelos/conectar.php';
require_once __DIR__ . '/../../../include/Security.php';
// GET /controladores/comunes/notificaciones/obtener.php
// Recordatorios de eventos no leídos del usuario de la sesión actual, para el
// widget de campana del calendario. Usa notificaciones_recordatorios (ver
// modelos/notificacionesRecordatorios.php) — NO confundir con la campana
// genérica (modelos/notificaciones.php), son sistemas y tablas distintos.
Security::initSession();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

require_once __DIR__ . "/../../../modelos/notificacionesRecordatorios.php";

// tipoUsuario 'director' (no 'admin') porque obtenerAudienciaEvento() crea las
// filas de notificaciones_recordatorios con ese valor para los administradores
// (coincide con el enum de NOTIF_ROLES_VALIDOS y con la tabla `directores`).
$roles = [
    'director'   => $_SESSION['idAdmin']      ?? null,
    'profesor'   => $_SESSION['idProfesor']   ?? null,
    'secretaria' => $_SESSION['idSecretaria'] ?? null,
    'estudiante' => $_SESSION['idEstudiante'] ?? null,
    'tutor'      => $_SESSION['idTutor']      ?? null,
];
$tipoUsuario = null;
$idUsuario   = null;
foreach ($roles as $tipo => $id) {
    if (!empty($id)) { $tipoUsuario = $tipo; $idUsuario = (int)$id; break; }
}
if (!$tipoUsuario) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'msg' => 'Sesión expirada. Por favor recarga la página.']);
    exit;
}

$notificaciones = obtenerNotificacionesNoLeidas($idUsuario, $tipoUsuario, 10);

echo json_encode(['ok' => true, 'notificaciones' => $notificaciones, 'total' => count($notificaciones)]);
