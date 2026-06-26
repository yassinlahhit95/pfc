<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/chat.php';

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!empty($_SESSION['idAdmin'])) {
    $myRol = 'admin';      $myId = (int)$_SESSION['idAdmin'];
} elseif (!empty($_SESSION['idProfesor'])) {
    $myRol = 'profesor';   $myId = (int)$_SESSION['idProfesor'];
} elseif (!empty($_SESSION['idTutor'])) {
    $myRol = 'tutor';      $myId = (int)$_SESSION['idTutor'];
} elseif (!empty($_SESSION['idEstudiante'])) {
    $myRol = 'estudiante'; $myId = (int)$_SESSION['idEstudiante'];
} elseif (!empty($_SESSION['idSecretaria'])) {
    $myRol = 'secretaria'; $myId = (int)$_SESSION['idSecretaria'];
} else {
    echo json_encode(['ok' => false]); exit;
}
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    echo json_encode(['ok' => false]); exit;
}
session_write_close();

$convs = chatConversacionesDe($myRol, $myId);
$result = [];
foreach ($convs as $c) {
    $result[] = [
        'id'           => (int)$c['id'],
        'other_rol'    => $c['other_rol'],
        'other_nombre' => $c['other_nombre'],
        'last_preview' => mb_strimwidth($c['last_preview'] ?? '', 0, 55, '…'),
        'last_at'      => $c['last_message_at'] ?? null,
        'unread'       => (int)$c['unread_count'],
    ];
}
echo json_encode(['ok' => true, 'convs' => $result]);
