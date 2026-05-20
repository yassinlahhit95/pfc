<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['idEstudiante'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    
    if (eliminarEstudiante($idEstudiante)) {
        $_SESSION['exito'] = "Estudiante eliminado.";
    } else {
        $_SESSION['errores'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
