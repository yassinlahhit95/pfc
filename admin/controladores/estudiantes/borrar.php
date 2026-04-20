<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['idEstudiante'])) {
    $idDelEstudiante = $_POST['idEstudiante'];
    
    if (empty($idDelEstudiante) || !ctype_digit($idDelEstudiante)) {
        $_SESSION['error'] = "ID de estudiante no válido.";
        header("Location: ../../vistas/estudiantes/verEstudiantes.php");
        exit;
    }

    if (eliminarEstudiante($idDelEstudiante)) {
        $_SESSION['mensaje'] = "Estudiante eliminado con éxito.";
    } else {
        $_SESSION['error'] = "No se ha podido eliminar el estudiante.";
    }
}

header("Location: ../../vistas/estudiantes/verEstudiantes.php");
exit;
?>
