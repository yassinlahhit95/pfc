<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

if (!FeatureGuard::check('feature_subida_tfg')) {
    $_SESSION['errores'] = "La entrega del TFG está cerrada en este momento.";
    header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
}

// Seguridad: SIEMPRE el id de la sesión, nunca el del formulario (evita IDOR)
$idEstudiante  = $_SESSION['idEstudiante'];
$datosTFG      = obtenerTFGporEstudiante($idEstudiante);
$nombreArchivo = is_array($datosTFG) ? ($datosTFG['archivoTFG'] ?? '') : '';

if (eliminarTFG($idEstudiante)) {
    $rutaArchivo = __DIR__ . "/../../../public/uploads/pfc/" . $nombreArchivo;
    if (!empty($nombreArchivo) && file_exists($rutaArchivo)) {
        unlink($rutaArchivo);
    }
    $_SESSION['exito'] = "TFG eliminado.";
} else {
    $_SESSION['errores'] = "Error al eliminar el TFG.";
}

header("Location: ../../../vistas/estudiantes/pfc/subir.php");
exit;
