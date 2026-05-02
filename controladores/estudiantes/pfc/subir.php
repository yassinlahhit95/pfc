<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['subirTFG'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    $archivoTFG = $_FILES['archivoTFG'];
    
    $hayError = false;

    if (empty($idEstudiante)) {
        $_SESSION['error'] = "ID de estudiante obligatorio.";
        $hayError = true;
    } else if (!empty($archivoTFG['error'])) {
        $_SESSION['error'] = "Error al subir el archivo.";
        $hayError = true;
    }

    if (!$hayError) {
        $datosEstudiante = obtenerEstudiantePorId($idEstudiante);
        $nombreLimpio = str_replace(' ', '_', $datosEstudiante['nombreEstudiante']);
        $timestamp = date('d-m-Y_H-i-s');
        $nombreArchivo = "TFG_" . $nombreLimpio . "_" . $timestamp . ".pdf";
        
        $rutaDestino = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivo;

        if (move_uploaded_file($archivoTFG['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreArchivo)) {
                $_SESSION['exito'] = "TFG subido.";
                header("Location: ../../../vistas/estudiantes/pfc/subir.php");
                exit;
            } else {
                $_SESSION['error'] = "Error al actualizar la base de datos.";
            }
        } else {
            $_SESSION['error'] = "Error al guardar el archivo.";
        }
    }
    
    header("Location: ../../../vistas/estudiantes/pfc/subir.php");
    exit;
}

header("Location: ../../../vistas/estudiantes/dashboard.php");
exit;
?>