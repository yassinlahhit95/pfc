<?php
ob_start();
session_start();
require_once __DIR__ . "/../../modelos/directores.php";
require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/tutores.php";

ob_clean();
header('Content-Type: application/json');

$tokenFCM = $_REQUEST['token'] ?? '';
$idUsuario = (int)($_REQUEST['userId'] ?? 0);
$rolUsuario = $_REQUEST['userRole'] ?? '';

// Verificar que la sesión coincide con la identidad reclamada antes de guardar el token FCM
$sessionOk = (
    ($rolUsuario === 'admin'      && !empty($_SESSION['idAdmin'])      && (int)$_SESSION['idAdmin']      === $idUsuario) ||
    ($rolUsuario === 'profesor'   && !empty($_SESSION['idProfesor'])   && (int)$_SESSION['idProfesor']   === $idUsuario) ||
    ($rolUsuario === 'tutor'      && !empty($_SESSION['idTutor'])      && (int)$_SESSION['idTutor']      === $idUsuario) ||
    ($rolUsuario === 'estudiante' && !empty($_SESSION['idEstudiante']) && (int)$_SESSION['idEstudiante'] === $idUsuario)
);

if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    echo json_encode(['success' => false, 'error' => 'Acción bloqueada']);
    exit;
}

if (!empty($tokenFCM) && $idUsuario > 0 && !empty($rolUsuario) && $sessionOk) {

    $resultado = false;

    switch ($rolUsuario) {
        case 'estudiante':
            $resultado = actualizarTokenFCMEstudiante($idUsuario, $tokenFCM);
            break;
        case 'profesor':
            $resultado = actualizarTokenFCMProfesor($idUsuario, $tokenFCM);
            break;
        case 'tutor':
            $resultado = actualizarTokenFCMTutor($idUsuario, $tokenFCM);
            break;
        case 'admin':
            $resultado = actualizarTokenFCMDirector($idUsuario, $tokenFCM);
            break;
    }

    if ($resultado) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No se pudo actualizar']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}
?>
