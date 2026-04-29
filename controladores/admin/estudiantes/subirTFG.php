<?php
session_start();
require_once "../../../modelos/estudiantes.php";
if (isset($_POST['subirTFG'])) {
    $id = $_POST['idEstudiante'];
    $archivo = $_FILES['archivoTFG'];
    if (empty($id)) {
        $_SESSION['error'] = "ID obligatorio";
    } else if (!empty($archivo['error'])) {
        $_SESSION['error'] = "Error archivo";
    } else {
        $timestamp = date('d-m-Y_H-i-s');
        $nombreArchivo = "TFG_" . $id . "_" . $timestamp . ".pdf";
        if (move_uploaded_file($archivo['tmp_name'], "../../uploads/pfc/" . $nombreArchivo)) {
            if (actualizarTFG($id, $nombreArchivo)) {
                $_SESSION['exito'] = "Ok";
                header("Location: /pfc/vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=$id");
                exit;
            } else {
                $_SESSION['error'] = "Error BD";
            }
        } else {
            $_SESSION['error'] = "Error mover archivo";
        }
    }
    header("Location: /pfc/vistas/admin/estudiantes/verDetallesEstudiantes.php?idEstudiante=$id");
    exit;
}
header("Location: /pfc/vistas/admin/estudiantes/verEstudiantes.php");
exit;


