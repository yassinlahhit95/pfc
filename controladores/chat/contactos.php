<?php
require_once __DIR__ . '/../../config/Config.php';
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

$q = strtolower(trim($_GET['q'] ?? ''));
$contacts = chatContactosPosibles($myRol, $myId);

if ($q !== '') {
    $contacts = array_values(array_filter($contacts, fn($c) =>
        str_contains(strtolower($c['nombre']), $q)
    ));
}

echo json_encode(['ok' => true, 'contacts' => $contacts]);
