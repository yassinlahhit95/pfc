<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../modelos/chat.php';
require_once __DIR__ . '/../../include/Cache.php';
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// Security::initSession() (not a bare session_start()) so the cookie-hardening
// flags (Secure/HttpOnly/SameSite/strict_mode) actually get applied.
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

// Red de seguridad frente a un cliente atascado/duplicado que sondee más
// rápido que su propio mínimo (3s en chat.js/chat-widget.js); no afecta al
// sondeo normal, que nunca llega a este límite.
if (!Throttle::allow("chat_poll_{$myRol}_{$myId}_{$convId}", 2.0)) {
    echo json_encode(['ok' => true, 'messages' => []]);
    exit;
}

$conv = chatConversacionPorId($convId);
if (!$conv || !chatEsParticipante($conv, $myRol, $myId)) {
    echo json_encode(['ok' => false]); exit;
}

chatMarcarLeidos($convId, $myRol, $myId);

$msgs = $afterId > 0
    ? chatMensajesDespuesDe($convId, $afterId)
    : chatMensajes($convId);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
echo json_encode(['ok' => true, 'messages' => $msgs]);
