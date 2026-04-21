<?php
session_start();
require_once "../../../modelos/tfg.php";

if (isset($_POST['guardarTFG'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $titulo = trim($_POST['tituloTFG']);

    $nombreArchivo = null;

    // Lógica de subida si hay archivo nuevo
    if (isset($_FILES['archivoTFG']) && $_FILES['archivoTFG']['error'] === UPLOAD_ERR_OK) {
        $directorioSubida = "../../uploads/tfg/";
        if (!is_dir($directorioSubida)) {
            mkdir($directorioSubida, 0777, true);
        }

        $nombreOriginal = $_FILES['archivoTFG']['name'];
        $nombreArchivo = time() . "_" . $nombreOriginal;
        move_uploaded_file($_FILES['archivoTFG']['tmp_name'], $directorioSubida . $nombreArchivo);
    }

    if (actualizarDatosTFG($idEstudiante, $titulo, $nombreArchivo)) {
        $_SESSION['exito'] = "Datos del TFG guardados correctamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar los datos en la BD.";
    }
}

header("Location: /pfc/vistas/admin/tfg/verTFGs.php");
exit;
?>