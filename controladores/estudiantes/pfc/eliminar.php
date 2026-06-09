<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

// Solo un estudiante autenticado puede eliminar SU propio TFG
if (empty($_SESSION['idEstudiante'])) { header("Location: ../../../vistas/login.php"); exit; }
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = "La sesión ha caducado. Recarga la página e inténtalo de nuevo.";
    header("Location: ../../../vistas/estudiantes/pfc/subir.php"); exit;
}

// Seguridad: SIEMPRE el id de la sesión, nunca el del formulario (evita IDOR)
$idEstudiante  = $_SESSION['idEstudiante'];
$datosTFG      = obtenerTFGporEstudiante($idEstudiante);
$nombreArchivo = $datosTFG['archivoTFG'] ?? '';

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
