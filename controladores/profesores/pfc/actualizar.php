<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarTFG'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/profesores/pfc/lista.php");
        exit;
    }
    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
    $tituloTFG    = trim($_POST['tituloTFG']);

    if (empty($idEstudiante)) {
        header("Location: ../../../vistas/profesores/pfc/lista.php");
        exit;
    }

    if (!estudiantePerteneceAProfesor($idEstudiante, $_SESSION['idProfesor'])) {
        $_SESSION['errores'] = "No tienes permiso sobre este estudiante.";
        header("Location: ../../../vistas/profesores/pfc/lista.php");
        exit;
    }

    if (empty($tituloTFG)) {
        $_SESSION['errores'] = "El título del TFG es un campo obligatorio.";
    } else {
        $resultado = actualizarDatosTFG($idEstudiante, $tituloTFG);
        if ($resultado) {
            $_SESSION['exito'] = "Los datos del TFG han sido actualizados correctamente.";
        } else {
            $_SESSION['errores'] = "No se pudo actualizar el TFG. Inténtalo de nuevo.";
        }
    }

    header("Location: ../../../vistas/profesores/pfc/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/profesores/pfc/lista.php");
exit;
