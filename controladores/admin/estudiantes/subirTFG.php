<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['subirTFG'])) {
    $idEstudiante = trim($_POST['idEstudiante'] ?? '');
    $archivo = $_FILES['archivoTFG'] ?? null;
    
    $errores = [];

    if (empty($idEstudiante)) {
        $errores['idEstudiante'] = "Falta ID estudiante.";
    } elseif (!$archivo || !empty($archivo['error'])) {
        $errores['archivoTFG'] = "Error en archivo.";
    }

    if (empty($errores)) {
        $timestamp = date('d-m-Y_H-i-s');
        $nombreArchivo = "TFG_" . $idEstudiante . "_" . $timestamp . ".pdf";

        $rutaDestino = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            if (actualizarTFG($idEstudiante, $nombreArchivo)) {
                $_SESSION['exito'] = "TFG subido.";
                header("Location: ../../../vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=$idEstudiante");
                exit;
            }
            $_SESSION['errores'] = "Error al actualizar.";
        } else {
            $_SESSION['errores'] = "Error al guardar archivo.";
        }
    } else {
        $_SESSION['errores'] = $errores;
    }
    
    header("Location: ../../../vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=$idEstudiante");
    exit;
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
