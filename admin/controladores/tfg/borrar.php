<?php
session_start();
require_once "../../../modelos/tfg.php";

if (isset($_POST['idEstudiante'])) {
    $id = $_POST['idEstudiante'];
    if (eliminarArchivoTFG($id)) {
        $_SESSION['exito'] = "Archivo TFG eliminado.";
    } else {
        $_SESSION['error'] = "No se pudo eliminar el archivo.";
    }
}

header("Location: ../../vistas/tfg/verTFGs.php");
exit;
?>
