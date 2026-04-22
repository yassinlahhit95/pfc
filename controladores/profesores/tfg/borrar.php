<?php
session_start();
require_once "../../../modelos/tfg.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (eliminarArchivoTFG($id)) {
        $_SESSION['exito'] = "Archivo eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el archivo.";
    }
}
header("Location: /pfc/vistas/profesores/tfg/lista.php");
exit;
?>
