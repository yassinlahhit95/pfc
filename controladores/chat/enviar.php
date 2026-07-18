<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../include/RateLimiter.php';
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/chat.php';

// Security::initSession() (not a bare session_start()) so the cookie-hardening
// flags (Secure/HttpOnly/SameSite/strict_mode) actually get applied.
Security::initSession();
header('Content-Type: application/json; charset=utf-8');

function jsonErr(string $msg): never {
    echo json_encode(['ok' => false, 'error' => $msg]);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!empty($_SESSION['idAdmin'])) {
    $myRol = 'admin';    $myId = (int)$_SESSION['idAdmin'];
} elseif (!empty($_SESSION['idProfesor'])) {
    $myRol = 'profesor'; $myId = (int)$_SESSION['idProfesor'];
} elseif (!empty($_SESSION['idTutor'])) {
    $myRol = 'tutor';    $myId = (int)$_SESSION['idTutor'];
} elseif (!empty($_SESSION['idEstudiante'])) {
    $myRol = 'estudiante';  $myId = (int)$_SESSION['idEstudiante'];
} elseif (!empty($_SESSION['idSecretaria'])) {
    $myRol = 'secretaria';  $myId = (int)$_SESSION['idSecretaria'];
} else {
    jsonErr('Sin sesión');
}
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    jsonErr('Acción bloqueada.');
}

$_con = obtenerConexion();
if (!RateLimiter::allow($_con, "chat:{$myRol}:{$myId}", 30, 60, 120)) {
    jsonErr('Demasiados mensajes seguidos. Espera un momento e inténtalo de nuevo.');
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('Método no permitido');
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '', false)) jsonErr('CSRF inválido');

$convId   = (int)($_POST['conv_id'] ?? 0);
$contenido = trim($_POST['contenido'] ?? '');
if ($convId <= 0 || $contenido === '') jsonErr('Datos incompletos');

$conv = chatConversacionPorId($convId);
if (!$conv || !chatEsParticipante($conv, $myRol, $myId)) jsonErr('No autorizado');

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$newId = chatInsertarMensaje($convId, $myRol, $myId, $contenido);

// Notificación push al destinatario (no bloquea si falla)
$destRol = ($conv['user_a_rol'] === $myRol && (int)$conv['user_a_id'] === $myId)
    ? $conv['user_b_rol'] : $conv['user_a_rol'];
$destId  = ($conv['user_a_rol'] === $myRol && (int)$conv['user_a_id'] === $myId)
    ? (int)$conv['user_b_id'] : (int)$conv['user_a_id'];

$emisorNombre = chatNombreUsuario($myRol, $myId);

require_once __DIR__ . '/../firebase/firebase_helper.php';
$destToken = obtenerTokenUsuario($destId, $destRol);
if ($destToken) {
    $cuerpoPush = mb_strimwidth($contenido, 0, 120, '…');
    @enviarNotificacionFirebase($destToken, 'Nuevo mensaje de ' . $emisorNombre, $cuerpoPush);
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
$msg = [
    'id'              => $newId,
    'conversacion_id' => $convId,
    'emisor_rol'      => $myRol,
    'emisor_id'       => $myId,
    'emisor_nombre'   => $emisorNombre,
    'contenido'       => $contenido,
    'fecha'           => date('Y-m-d H:i:s'),
    'leido'           => 0,
];

echo json_encode(['ok' => true, 'message' => $msg]);
