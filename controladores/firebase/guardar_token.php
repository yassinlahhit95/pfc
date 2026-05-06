<?php
session_start();
require_once __DIR__ . "/../../modelos/directores.php";
require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";

header('Content-Type: application/json');

// Obtenemos los datos del cuerpo de la petición (JSON)
$datosRecibidos = json_decode(file_get_contents('php://input'), true);

if (isset($datosRecibidos['token'], $datosRecibidos['userId'], $datosRecibidos['userRole'])) {
    $tokenFCM = trim($datosRecibidos['token']);
    $idUsuario = (int)trim($datosRecibidos['userId']);
    $rolUsuario = trim($datosRecibidos['userRole']);

    $resultado = false;

    // Llamamos al modelo correspondiente según el rol
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
