<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['subirTFG'])) {
    $idEstudiante = trim($_POST['idEstudiante'] ?? '');
    $archivo = $_FILES['archivoTFG'] ?? null;
    
    $hayError = false;

    if (empty($idEstudiante)) {
        $_SESSION['error'] = "Falta ID estudiante.";
        $hayError = true;
    } elseif (!$archivo || !empty($archivo['error'])) {
        $_SESSION['error'] = "Error en archivo.";
        $hayError = true;
    }

    if (!$hayError) {
        $timestamp = date('d-m-Y_H-i-s');
        $nombreArchivo = "TFG_" . $idEstudiante . "_" . $timestamp . ".pdf";
        
        // Usar __DIR__ para la ruta de subida
        $rutaDestino = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreArchivo)) {
                $_SESSION['exito'] = "TFG subido.";
                header("Location: ../../../vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=$idEstudiante");
                exit;
            } else {
                $_SESSION['error'] = "Error al actualizar.";
            }
        } else {
            $_SESSION['error'] = "Error al guardar archivo.";
        }
    }
    
    header("Location: ../../../vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
