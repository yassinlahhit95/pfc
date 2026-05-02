<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";

if (isset($_GET['id'])) {
    $idEstudiante = trim($_GET['id']);
    
    $resultado = eliminarArchivoTFG($idEstudiante);
    if ($resultado) {
        $_SESSION['exito'] = "Listo! El archivo ha sido eliminado.";
    } else {
        $_SESSION['error'] = "Vaya, no se ha podido eliminar el archivo.";
    }
}

header("Location: ../../../vistas/profesores/pfc/lista.php");
exit;
?>