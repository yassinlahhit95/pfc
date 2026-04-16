<?php
session_start();
require_once "../../modelos/estudiantes.php";

if (isset($_POST['idEstudiante'])) {
    $id = $_POST['idEstudiante'];
    
    if (is_numeric($id) && ctype_digit($id) && preg_match('/^[0-9]+$/', $id)) {
        $modelo = new estudiante();
        if ($modelo->eliminarEstudianteModelo($id)) {
            $_SESSION['exito'] = "Estudiante eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el estudiante";
        }
    } else {
        $_SESSION['error'] = "ID del estudiante no válido";
    }
}

header("Location: ../../vistas/estudiantes/verEstudiantes.php");
exit;
?>
