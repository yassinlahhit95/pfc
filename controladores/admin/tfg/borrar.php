<?php
session_start();
require_once "../../../modelos/tfg.php";

if (isset($_POST['idTFG'])) {
    $id_tfg = $_POST['idTFG'];
    $nombre_archivo = $_POST['nombreArchivo'];

    if (empty($id_tfg)) {
        $_SESSION['error'] = "ID del TFG no proporcionado.";
        header("Location: /pfc/vistas/admin/tfg/verTFGs.php");
        exit;
    }

    $ruta_archivo = "../../../public/uploads/tfg/" . $nombre_archivo;

    if (eliminarTFG($id_tfg)) {
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
