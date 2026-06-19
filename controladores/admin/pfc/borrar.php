<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['idEstudiante'])) {
    $idEstudiante  = (int)trim($_POST['idEstudiante']);
    $nombreArchivo = basename(trim($_POST['nombreArchivo'] ?? ''));

    if (empty($idEstudiante)) {
        $_SESSION['errores'] = "El identificador del estudiante no es válido.";
        header("Location: ../../../vistas/admin/academico/calificacionesTFG.php");
        exit;
    }

    $rutaDelArchivo = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivo;
    $directorioBase = realpath(__DIR__ . "/../../../public/uploads/pfc");
    $rutaReal       = $nombreArchivo ? realpath($rutaDelArchivo) : false;

    if (eliminarTFG($idEstudiante)) {
        if ($rutaReal && $directorioBase && strpos($rutaReal, $directorioBase . DIRECTORY_SEPARATOR) === 0) {
            unlink($rutaReal);
        }
        $_SESSION['exito'] = "El TFG ha sido eliminado correctamente.";
    } else {
        $_SESSION['errores'] = "No se pudo eliminar el archivo del TFG.";
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/academico/calificacionesTFG.php");
exit;
