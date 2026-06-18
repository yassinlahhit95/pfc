<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/chat.php';

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (!empty($_SESSION['idAdmin'])) {
    $myRol = 'admin';      $myId = (int)$_SESSION['idAdmin'];
} elseif (!empty($_SESSION['idProfesor'])) {
    $myRol = 'profesor';   $myId = (int)$_SESSION['idProfesor'];
} elseif (!empty($_SESSION['idTutor'])) {
    $myRol = 'tutor';      $myId = (int)$_SESSION['idTutor'];
} elseif (!empty($_SESSION['idEstudiante'])) {
    $myRol = 'estudiante'; $myId = (int)$_SESSION['idEstudiante'];
} else {
    echo json_encode(['ok' => false]); exit;
}
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    echo json_encode(['ok' => false]); exit;
}

$convId  = (int)($_GET['conv_id'] ?? 0);
$afterId = (int)($_GET['after_id'] ?? 0);
if ($convId <= 0) { echo json_encode(['ok' => false]); exit; }

$conv = chatConversacionPorId($convId);
if (!$conv || !chatEsParticipante($conv, $myRol, $myId)) {
    echo json_encode(['ok' => false]); exit;
}

chatMarcarLeidos($convId, $myRol, $myId);

$msgs = $afterId > 0
    ? chatMensajesDespuesDe($convId, $afterId)
    : chatMensajes($convId);

echo json_encode(['ok' => true, 'messages' => $msgs]);
