<?php
session_start();
require_once __DIR__ . "/../../../modelos/tfg.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idEstudiante'])) {
    $idEstudiantePfc = trim($_POST['idEstudiante']);
    $nombreFicheroPfc = trim($_POST['nombreArchivo']);

    if (empty($idEstudiantePfc)) {
        $hayError = true;
        $_SESSION['error'] = "Vaya, el ID del estudiante no ha sido proporcionado.";
        header("Location: ../../../vistas/admin/pfc/verTFGs.php");
        exit;
    }

    $rutaDelArchivo = __DIR__ . "/../../../public/uploads/pfc/" . $nombreFicheroPfc;

    if (eliminarTFG($idEstudiantePfc)) {
        if (file_exists($rutaDelArchivo)) {
            unlink($rutaDelArchivo);
        }
        $_SESSION['exito'] = "Listo! TFG eliminado correctamente.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Vaya, hubo un problema al eliminar el registro de la base de datos.";
    }
}

header("Location: ../../../vistas/admin/pfc/verTFGs.php");
exit;
