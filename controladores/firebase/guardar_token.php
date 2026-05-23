<?php
ob_start();
session_start();
require_once __DIR__ . "/../../modelos/directores.php";
require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";

ob_clean();
header('Content-Type: application/json');

$tokenFCM = $_REQUEST['token'] ?? '';
$idUsuario = (int)($_REQUEST['userId'] ?? 0);
$rolUsuario = $_REQUEST['userRole'] ?? '';

if (!empty($tokenFCM) && $idUsuario > 0 && !empty($rolUsuario)) {

    $resultado = false;

    switch ($rolUsuario) {
        case 'estudiante':
            $resultado = actualizarTokenFCMEstudiante($idUsuario, $tokenFCM);
            break;
        case 'profesor':
            $resultado = actualizarTokenFCMProfesor($idUsuario, $tokenFCM);
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
