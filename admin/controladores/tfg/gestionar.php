<?php
session_start();
require_once "../../modelos/tfg.php";

if (isset($_POST['guardarTFG'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $titulo = trim($_POST['tituloTFG'] ?? '');
    
    $nombreArchivo = null;

    // Lógica de subida si hay archivo nuevo
    if (isset($_FILES['archivoTFG']) && $_FILES['archivoTFG']['error'] === UPLOAD_ERR_OK) {
        $directorioSubida = "../../uploads/tfg/";
        if (!is_dir($directorioSubida)) { mkdir($directorioSubida, 0777, true); }

        $nombreOriginal = $_FILES['archivoTFG']['name'];
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            $nombreArchivo = "TFG_" . $idEstudiante . "_" . time() . ".pdf";
            move_uploaded_file($_FILES['archivoTFG']['tmp_name'], $directorioSubida . $nombreArchivo);
        } else {
            $_SESSION['error'] = "Solo se permiten archivos PDF.";
            header("Location: ../../vistas/tfg/verTFGs.php");
            exit;
        }
    }

    if (actualizarDatosTFG($idEstudiante, $titulo, $nombreArchivo)) {
        $_SESSION['exito'] = "TFG actualizado correctamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar el TFG.";
    }
}

header("Location: ../../vistas/tfg/verTFGs.php");
exit;
?>
