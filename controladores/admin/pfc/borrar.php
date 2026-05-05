<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";

$hayError = false;

if (isset($_POST['idEstudiante'])) {
    $idEstudiantePfc = trim($_POST['idEstudiante']);
    $nombreFicheroPfc = trim($_POST['nombreArchivo']);

    if (empty($idEstudiantePfc)) {
        $hayError = true;
        $_SESSION['error'] = "Falta ID estudiante.";
        header("Location: ../../../vistas/admin/pfc/verTFGs.php");
        exit;
    }

    $rutaDelArchivo = __DIR__ . "/../../../public/uploads/pfc/" . $nombreFicheroPfc;

    if (eliminarTFG($idEstudiantePfc)) {
        if (file_exists($rutaDelArchivo)) {
            unlink($rutaDelArchivo);
        }
        $_SESSION['exito'] = "TFG eliminado.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/pfc/verTFGs.php");
exit;
