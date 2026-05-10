<?php
ob_start();
session_start();
require_once __DIR__ . "/../../modelos/directores.php";
require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";

ob_clean();
header('Content-Type: application/json');

$datosRecibidos = json_decode(file_get_contents('php://input'), true);

if (isset($datosRecibidos['token'], $datosRecibidos['userId'], $datosRecibidos['userRole'])) {
    $tokenFCM = trim($datosRecibidos['token']);
    $idUsuario = (int)trim($datosRecibidos['userId']);
    $rolUsuario = trim($datosRecibidos['userRole']);

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
        echo json_encode(['success' => false, 'error' => 'No se pudo actualizar el token']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}
?>
