<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";

if (isset($_GET['id'])) {
    $idEstudiante = trim($_GET['id']);
    
    $resultado = eliminarArchivoTFG($idEstudiante);
    if ($resultado) {
        $_SESSION['exito'] = "Archivo eliminado.";
    } else {
        $_SESSION['error'] = "Error al eliminar el archivo.";
    }
}

header("Location: ../../../vistas/profesores/pfc/lista.php");
exit;
?>
