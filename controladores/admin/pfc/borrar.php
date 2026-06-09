<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$hayError = false;

if (isset($_POST['idEstudiante'])) {
    $idEstudiantePfc = (int)trim($_POST['idEstudiante']);
    $nombreFicheroPfc = basename(trim($_POST['nombreArchivo'] ?? ''));

    if (empty($idEstudiantePfc)) {
        $hayError = true;
        $_SESSION['errores'] = "Falta ID estudiante.";
        header("Location: ../../../vistas/admin/academico/calificacionesTFG.php");
        exit;
    }

    $rutaDelArchivo = __DIR__ . "/../../../public/uploads/pfc/" . $nombreFicheroPfc;
    $directorioBase = realpath(__DIR__ . "/../../../public/uploads/pfc");
    $rutaReal = $nombreFicheroPfc ? realpath($rutaDelArchivo) : false;

    if (eliminarTFG($idEstudiantePfc)) {
        if ($rutaReal && $directorioBase && strpos($rutaReal, $directorioBase . DIRECTORY_SEPARATOR) === 0) {
            unlink($rutaReal);
        }
        $_SESSION['exito'] = "TFG eliminado.";
    } else {
        $hayError = true;
        $_SESSION['errores'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/academico/calificacionesTFG.php");
exit;
?>
