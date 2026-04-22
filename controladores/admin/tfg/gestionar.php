<?php
session_start();
require_once "../../../modelos/tfg.php";
if (isset($_POST['guardarTFG'])) {
    $id = $_POST['idEstudiante'];
    $titulo = $_POST['tituloTFG'];
    $archivo = $_FILES['archivoTFG'];
    $nombreArchivo = "";
    if ($archivo['error'] === 0) {
        $nombreArchivo = time() . "_" . $archivo['name'];
        move_uploaded_file($archivo['tmp_name'], "../../uploads/tfg/" . $nombreArchivo);
    }
    if (actualizarDatosTFG($id, $titulo, $nombreArchivo)) {
        $_SESSION['exito'] = "Ok";
        header("Location: /pfc/vistas/admin/tfg/verTFGs.php");
        exit;
    } else {
        $_SESSION['error'] = "Error BD";
    }
    header("Location: /pfc/vistas/admin/tfg/verTFGs.php");
    exit;
}
header("Location: /pfc/vistas/admin/tfg/verTFGs.php");
exit;

