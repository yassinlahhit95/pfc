<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['idProfesor'])) {
    $id = $_POST['idProfesor'];
    if (eliminarProfesor($id)) {
        $_SESSION['exito'] = "Profesor eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el profesor.";
    }
}
header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
exit;
?>

