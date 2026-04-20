<?php
session_start();
require_once "../../../modelos/profesores.php";

// Usamos idProfesor directamente
if (isset($_POST['idProfesor'])) {
    $idDelProfesor = $_POST['idProfesor'];
    
    if (empty($idDelProfesor) || !ctype_digit($idDelProfesor)) {
        $_SESSION['error'] = "No se ha encontrado el ID del profesor";
        header("Location: ../../vistas/profesores/verProfesores.php");
        exit;
    }

    if (eliminarProfesor($idDelProfesor)) {
        $_SESSION['exito'] = "Profesor eliminado correctamente";
    } else {
        $_SESSION['error'] = "No se pudo eliminar el profesor";
    }
}

header("Location: ../../vistas/profesores/verProfesores.php");
exit;
?>
