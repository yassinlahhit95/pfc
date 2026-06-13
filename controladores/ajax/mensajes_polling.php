<?php
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../modelos/reclamaciones.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('X-Content-Type-Options: nosniff');

$role = null;
$uid  = null;

if (!empty($_SESSION['idAdmin']))         { $role = 'admin';     $uid = (int)$_SESSION['idAdmin']; }
elseif (!empty($_SESSION['idProfesor']))  { $role = 'profesor';  $uid = (int)$_SESSION['idProfesor']; }
elseif (!empty($_SESSION['idEstudiante'])){ $role = 'estudiante'; $uid = (int)$_SESSION['idEstudiante']; }

if (!$role) {
    http_response_code(401);
    echo json_encode(['ok' => false]);
    exit;
}

$unread = 0;
switch ($role) {
    case 'admin':
        $unread = contarMensajesNoLeidosAdmin();
        break;
    case 'profesor':
        $unread = contarMensajesNoLeidosProfesor($uid);
        break;
    case 'estudiante':
        $unread = contarMensajesNoLeidosEstudiante($uid);
        break;
}

echo json_encode(['ok' => true, 'unread' => $unread]);
