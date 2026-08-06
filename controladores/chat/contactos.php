<?php
require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../include/Security.php';
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
Security::initSession();
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../modelos/chat.php';

header('Cache-Control: no-store');
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
// La búsqueda se aplica a nivel de base de datos dentro de chatContactosPosibles().
$q        = trim($_GET['q'] ?? '');
$contacts = chatContactosPosibles($myRol, $myId, $q);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
echo json_encode(['ok' => true, 'contacts' => $contacts]);
