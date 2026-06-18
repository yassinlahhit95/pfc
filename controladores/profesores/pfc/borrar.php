<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['idEstudiante'])) {
    $idEstudiante = (int)$_POST['idEstudiante'];

    if (!estudiantePerteneceAProfesor($idEstudiante, $_SESSION['idProfesor'])) {
        $_SESSION['errores'] = "No tienes permiso sobre este estudiante.";
        header("Location: ../../../vistas/profesores/pfc/lista.php"); exit;
    }

    if ($idEstudiante && eliminarArchivoTFG($idEstudiante)) {
        $_SESSION['exito'] = "Archivo eliminado.";
    } else {
        $_SESSION['errores'] = "Error al eliminar el archivo.";
    }
}

header("Location: ../../../vistas/profesores/pfc/lista.php");
exit;
?>
