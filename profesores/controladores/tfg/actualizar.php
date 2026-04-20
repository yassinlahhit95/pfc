<?php
session_start();
require_once "../../../modelos/tfg.php";

if (isset($_POST['actualizarTFG'])) {
    $id = $_POST['idEstudiante'];
    $titulo = trim($_POST['tituloTFG']);

    if (empty($id)) {
        header("Location: ../../vistas/tfg/lista.php");
    } else if (empty($titulo)) {
        $_SESSION['error'] = "El título del TFG es obligatorio.";
        header("Location: ../../vistas/tfg/lista.php");
    } else {
        if (actualizarDatosTFG($id, $titulo)) {
            $_SESSION['exito'] = "Datos del TFG actualizados correctamente.";
        } else {
            $_SESSION['error'] = "Error al actualizar los datos del TFG.";
        }
    }
    exit;
}

header("Location: ../../vistas/tfg/lista.php");
exit;
?>