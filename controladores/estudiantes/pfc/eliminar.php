<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (!FeatureGuard::check('feature_subida_tfg')) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'La entrega del TFG está cerrada en este momento.']); exit; }
    $_SESSION['errores'] = "La entrega del TFG está cerrada en este momento.";
    header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
}

if (!Security::validateCSRFToken()) {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Solicitud inválida']); exit; }
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
    if ($isAjax) { echo json_encode(['ok'=>true,'msg'=>'TFG eliminado']); exit; }
    $_SESSION['exito'] = "TFG eliminado.";
} else {
    if ($isAjax) { echo json_encode(['ok'=>false,'msg'=>'Error al eliminar el TFG']); exit; }
    $_SESSION['errores'] = "Error al eliminar el TFG.";
}

header("Location: ../../../vistas/estudiantes/pfc/subir.php");
exit;
