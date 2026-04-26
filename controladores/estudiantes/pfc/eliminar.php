<?php
session_start();
require_once "../../../modelos/tfg.php";

if (isset($_POST['idEstudiante'])) {
    $id = $_POST['idEstudiante'];
    
    // Obtenemos los datos antes de borrar de la base de datos para tener el nombre del archivo
    $tfg = obtenerTFGporEstudiante($id);
    $nombreArchivo = $tfg['archivoTFG'];
    
    if (eliminarTFG($id)) {
        $rutaArchivo = "../../../public/uploads/pfc/" . $nombreArchivo;
        if (!empty($nombreArchivo) && file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
        $_SESSION['exito'] = "TFG eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el TFG.";
    }
}

header("Location: /pfc/vistas/estudiantes/pfc/lista.php");
exit;
?>
