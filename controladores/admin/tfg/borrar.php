<?php
session_start();
require_once "../../../modelos/tfg.php";

if (isset($_POST['idEstudiante'])) {
    $id_estudiante = $_POST['idEstudiante'];
    $nombre_archivo = $_POST['nombreArchivo'];

    if (empty($id_estudiante)) {
        $_SESSION['error'] = "ID del estudiante no proporcionado.";
        header("Location: /pfc/vistas/admin/tfg/verTFGs.php");
        exit;
    }

    $ruta_archivo = "../../../public/uploads/tfg/" . $nombre_archivo;

    if (eliminarTFG($id_estudiante)) {
        if (file_exists($ruta_archivo)) {
            unlink($ruta_archivo);
        }
        $_SESSION['exito'] = "TFG eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el registro de la base de datos.";
    }
}

header("Location: /pfc/vistas/admin/tfg/verTFGs.php");
exit;
