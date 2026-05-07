<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['subirTFG'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    $archivoTFG = $_FILES['archivoTFG'];
    
    $listaErrores = [];

    if (empty($idEstudiante)) {
        $listaErrores['idEstudiante'] = "ID de estudiante obligatorio.";
    } else if (!empty($archivoTFG['error'])) {
        if ($archivoTFG['error'] == 4) {
            $listaErrores['archivoTFG'] = "Debes seleccionar un archivo.";
        } else {
            $listaErrores['archivoTFG'] = "Error al subir el archivo (Código: " . $archivoTFG['error'] . ").";
        }
    } else {
        $ext = strtolower(pathinfo($archivoTFG['name'], PATHINFO_EXTENSION));
        $permitidos = ['pdf', 'doc', 'docx'];
        
        if (!in_array($ext, $permitidos)) {
            $listaErrores['archivoTFG'] = "Solo se permiten archivos PDF o Word (.doc, .docx).";
        }
    }

    if (empty($listaErrores)) {
        $datosEstudiante = obtenerEstudiantePorId($idEstudiante);
        $nombreLimpio = str_replace(' ', '_', $datosEstudiante['nombreEstudiante']);
        $timestamp = date('d-m-Y_H-i-s');
        $extOriginal = pathinfo($archivoTFG['name'], PATHINFO_EXTENSION);
        $nombreArchivo = "TFG_" . $nombreLimpio . "_" . $timestamp . "." . $extOriginal;
        
        $rutaDestino = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivo;

        if (move_uploaded_file($archivoTFG['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreArchivo)) {
                $_SESSION['exito'] = "TFG subido correctamente.";
                header("Location: ../../../vistas/estudiantes/pfc/subir.php");
                exit;
            } else {
                $_SESSION['error'] = "Error al actualizar la base de datos.";
            }
        } else {
            $_SESSION['error'] = "Error al guardar el archivo en el servidor.";
        }
    } else {
        $_SESSION['errores'] = $listaErrores;
    }
    
    header("Location: ../../../vistas/estudiantes/pfc/subir.php");
    exit;
}

header("Location: ../../../vistas/estudiantes/inicio/dashboard.php");
exit;
?>
