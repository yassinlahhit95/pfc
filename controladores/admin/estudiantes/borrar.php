<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['idEstudiante'])) {
    $id = $_POST['idEstudiante'];
    if (eliminarEstudiante($id)) {
        $_SESSION['exito'] = "Estudiante eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el estudiante.";
    }
}
header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
