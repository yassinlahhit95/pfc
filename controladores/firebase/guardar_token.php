<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../modelos/directores.php";
require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/tutores.php";
require_once __DIR__ . "/../../modelos/secretarias.php";
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
ob_start();
session_start();
ob_clean();
header('Content-Type: application/json');

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!Security::validateCSRFToken(null, false)) {
    echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
    exit;
}

$tokenFCM   = $_REQUEST['token']    ?? '';
$idUsuario  = (int)($_REQUEST['userId']   ?? 0);
$rolUsuario = $_REQUEST['userRole'] ?? '';

// Verifica que la sesión coincide con la identidad reclamada antes de guardar el token FCM
$sessionOk = (
    ($rolUsuario === 'admin'      && !empty($_SESSION['idAdmin'])      && (int)$_SESSION['idAdmin']      === $idUsuario) ||
    ($rolUsuario === 'profesor'   && !empty($_SESSION['idProfesor'])   && (int)$_SESSION['idProfesor']   === $idUsuario) ||
    ($rolUsuario === 'tutor'      && !empty($_SESSION['idTutor'])      && (int)$_SESSION['idTutor']      === $idUsuario) ||
    ($rolUsuario === 'estudiante' && !empty($_SESSION['idEstudiante']) && (int)$_SESSION['idEstudiante'] === $idUsuario) ||
    ($rolUsuario === 'secretaria' && !empty($_SESSION['idSecretaria']) && (int)$_SESSION['idSecretaria'] === $idUsuario)
);

if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    echo json_encode(['ok' => false, 'msg' => 'Acción bloqueada']);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
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
        case 'secretaria':
            $resultado = actualizarTokenFCMSecretaria($idUsuario, $tokenFCM);
            break;
    }

    // ══════════════════════════════════════════════════════════════════════
    // RESPUESTA
    // ══════════════════════════════════════════════════════════════════════
    if ($resultado) {
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'No se pudo actualizar el token.']);
    }
} else {
    echo json_encode(['ok' => false, 'msg' => 'Datos incompletos o sesión no válida.']);
}
