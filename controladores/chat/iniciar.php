<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/chat.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['idAdmin'])) {
    $myRol = 'admin';
    $myId  = (int)$_SESSION['idAdmin'];
    $back  = '../../vistas/admin/chat/index.php';
} elseif (!empty($_SESSION['idProfesor'])) {
    $myRol = 'profesor';
    $myId  = (int)$_SESSION['idProfesor'];
    $back  = '../../vistas/profesores/chat/index.php';
} elseif (!empty($_SESSION['idTutor'])) {
    $myRol = 'tutor';
    $myId  = (int)$_SESSION['idTutor'];
    $back  = '../../vistas/tutores/mensajes/chat.php';
} elseif (!empty($_SESSION['idEstudiante'])) {
    $myRol = 'estudiante';
    $myId  = (int)$_SESSION['idEstudiante'];
    $back  = '../../vistas/estudiantes/chat/index.php';
} else {
    header('Location: ../../vistas/login.php');
    exit;
}

if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    header("Location: $back");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $back");
    exit;
}

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    header("Location: $back");
    exit;
}

$targetRol = trim($_POST['target_rol'] ?? '');
$targetId  = (int)($_POST['target_id'] ?? 0);

$validRoles = ['admin', 'profesor', 'estudiante', 'tutor'];
if (!in_array($targetRol, $validRoles, true) || $targetId <= 0) {
    header("Location: $back");
    exit;
}

if ($targetRol === $myRol && $targetId === $myId) {
    header("Location: $back");
    exit;
}

$convId = chatEncontrarOCrear($myRol, $myId, $targetRol, $targetId);

$convUrl = (strpos($back, 'index.php') !== false) 
            ? str_replace('index.php', 'conversacion.php', $back)
            : str_replace('chat.php', 'conversacion.php', $back);
header("Location: {$convUrl}?id=$convId");
exit;
