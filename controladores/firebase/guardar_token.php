<?php
session_start();
require_once "../../modelos/conectar.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['token'], $data['userId'], $data['userRole'])) {
    $token = $data['token'];
    $userId = (int)$data['userId'];
    $userRole = $data['userRole'];

    $conexion = obtenerConexion();
    $tabla = "";
    $columnaId = "";

    switch ($userRole) {
        case 'estudiante': $tabla = "estudiantes"; $columnaId = "idEstudiante"; break;
        case 'profesor': $tabla = "profesores"; $columnaId = "idProfesor"; break;
        case 'admin': $tabla = "directores"; $columnaId = "idDirector"; break;
    }

    if ($tabla != "") {
        $stmt = mysqli_prepare($conexion, "UPDATE $tabla SET fcm_token = ? WHERE $columnaId = ?");
        mysqli_stmt_bind_param($stmt, "si", $token, $userId);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conexion)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'error' => 'Rol inválido']);
    }
    mysqli_close($conexion);
} else {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
}
?>
