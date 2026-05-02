<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";

if (isset($_POST['idEstudiante'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    
    $datosTFG = obtenerTFGporEstudiante($idEstudiante);
    $nombreArchivo = $datosTFG['archivoTFG'];
    
    $resultado = eliminarTFG($idEstudiante);
    if ($resultado) {
        $rutaArchivo = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivo;
        if (!empty($nombreArchivo) && file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
        $_SESSION['exito'] = "Listo! TFG eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Vaya, ha habido un error al eliminar el TFG.";
    }
}

header("Location: ../../../vistas/estudiantes/pfc/subir.php");
exit;
?>