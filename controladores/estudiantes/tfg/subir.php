<?php
session_start();
require_once "../../../modelos/tfg.php";
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['subirTFG'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $archivo = $_FILES['archivoTFG'];
    
    if (empty($idEstudiante)) {
        $_SESSION['error'] = "ID de estudiante obligatorio.";
    } else if ($archivo['error'] != 0) {
        $_SESSION['error'] = "Error al subir el archivo.";
    } else {
        $estudiante = obtenerEstudiantePorId($idEstudiante);
        $nombreLimpio = str_replace(' ', '_', $estudiante['nombreEstudiante']);
        $fechaActual = date('d-m-Y');
        $nombreArchivo = "TFG_" . $nombreLimpio . "_" . $fechaActual . ".pdf";
        
        $rutaDestino = "../../../public/uploads/tfg/" . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreArchivo)) {
                $_SESSION['exito'] = "TFG subido correctamente.";
                header("Location: /pfc/vistas/estudiantes/tfg/lista.php");
                exit;
            } else {
                $_SESSION['error'] = "Error al actualizar en la base de datos.";
            }
        } else {
            $_SESSION['error'] = "No se pudo mover el archivo al servidor.";
        }
    }
    header("Location: /pfc/vistas/estudiantes/tfg/lista.php");
    exit;
}
header("Location: /pfc/vistas/estudiantes/dashboard.php");
exit;
?>