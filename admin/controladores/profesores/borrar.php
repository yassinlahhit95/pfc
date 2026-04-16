<?php
session_start();
require_once "../../modelos/profesores.php";

if (isset($_POST['idProfesor'])) {
    $id = $_POST['idProfesor'];
    
    if (is_numeric($id) && ctype_digit($id) && preg_match('/^[0-9]+$/', $id)) {
        $modelo = new profesor();
        if ($modelo->eliminarProfesoresModelo($id)) {
            $_SESSION['exito'] = "Profesor eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el profesor";
        }
    } else {
        $_SESSION['error'] = "ID del profesor no válido";
    }
}

header("Location: ../../vistas/profesores/verProfesores.php");
exit;
?>
