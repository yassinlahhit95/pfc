<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../modelos/chat.php';
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// Security::initSession() (no un session_start() a secas): así los flags de
// endurecimiento de cookies (Secure/HttpOnly/SameSite/strict_mode) sí se aplican.
Security::initSession();
header('Content-Type: application/json; charset=utf-8');

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!empty($_SESSION['idAdmin'])) {
    $myRol = 'admin';      $myId = (int)$_SESSION['idAdmin'];
} elseif (!empty($_SESSION['idProfesor'])) {
    $myRol = 'profesor';   $myId = (int)$_SESSION['idProfesor'];
} elseif (!empty($_SESSION['idTutor'])) {
    $myRol = 'tutor';      $myId = (int)$_SESSION['idTutor'];
} elseif (!empty($_SESSION['idEstudiante'])) {
    $myRol = 'estudiante';  $myId = (int)$_SESSION['idEstudiante'];
} elseif (!empty($_SESSION['idSecretaria'])) {
    $myRol = 'secretaria';  $myId = (int)$_SESSION['idSecretaria'];
} else {
    echo json_encode(['ok' => false]); exit;
}
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    echo json_encode(['ok' => false]); exit;
}
session_write_close(); // release session lock before DB work

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$convId  = (int)($_GET['conv_id'] ?? 0);
$afterId = (int)($_GET['after_id'] ?? 0);
if ($convId <= 0) { echo json_encode(['ok' => false]); exit; }

$conv = chatConversacionPorId($convId);
if (!$conv || !chatEsParticipante($conv, $myRol, $myId)) {
    echo json_encode(['ok' => false]); exit;
}

$con = obtenerConexion();
$st = mysqli_prepare($con,
    'SELECT COALESCE(MAX(id), 0) AS max_seen_id
     FROM chat_mensajes
     WHERE conversacion_id = ? AND emisor_rol = ? AND emisor_id = ? AND leido = 1 AND id > ?');
mysqli_stmt_bind_param($st, 'isii', $convId, $myRol, $myId, $afterId);
mysqli_stmt_execute($st);
$resultado = mysqli_stmt_get_result($st);
$row       = mysqli_fetch_assoc($resultado);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
echo json_encode(['ok' => true, 'max_seen_id' => (int)($row['max_seen_id'] ?? 0)]);
