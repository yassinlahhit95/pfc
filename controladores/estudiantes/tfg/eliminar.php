<?php
session_start();
require_once "../../../modelos/tfg.php";

if (isset($_POST['idEstudiante'])) {
    $id = $_POST['idEstudiante'];
    
    if (eliminarArchivoTFG($id)) {
        $_SESSION['exito'] = "TFG eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el TFG.";
    }
}

header("Location: /pfc/vistas/estudiantes/tfg/lista.php");
exit;
?>