<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../modelos/reclamaciones.php';
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('X-Content-Type-Options: nosniff');

// Security::initSession() (no un session_start() a secas): así los flags de
// endurecimiento de cookies (Secure/HttpOnly/SameSite/strict_mode) sí se aplican.
Security::initSession();

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$role = null;
$uid  = null;

if (!empty($_SESSION['idAdmin']))            { $role = 'admin';      $uid = (int)$_SESSION['idAdmin']; }
elseif (!empty($_SESSION['idSecretaria']))  { $role = 'admin';      $uid = (int)$_SESSION['idSecretaria']; }
elseif (!empty($_SESSION['idProfesor']))    { $role = 'profesor';   $uid = (int)$_SESSION['idProfesor']; }
elseif (!empty($_SESSION['idEstudiante'])) { $role = 'estudiante'; $uid = (int)$_SESSION['idEstudiante']; }

if (!$role) {
    http_response_code(401);
    echo json_encode(['ok' => false]);
    exit;
}
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    echo json_encode(['ok' => true, 'unread' => 0]);
    exit;
}
session_write_close(); // release session lock before DB work

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
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

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
echo json_encode(['ok' => true, 'unread' => $unread]);
