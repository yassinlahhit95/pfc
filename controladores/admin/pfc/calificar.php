<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['calificarTFG'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/academico/calificacionesTFG.php");
        exit;
    }
    $idEstudiante  = (int)($_POST['idEstudiante'] ?? 0);
    $nota          = str_replace(',', '.', trim($_POST['nota']));
    $observaciones = trim($_POST['observaciones']);

    $errores = '';
    if (!is_numeric($nota)) {
        $errores = "La nota debe ser un valor numérico.";
    } elseif ($nota < 0 || $nota > 10) {
        $errores = "La nota debe estar comprendida entre 0 y 10.";
    }

    if (!$errores) {
        if (guardarCalificacionTFG($idEstudiante, $nota, $observaciones)) {
            if (!empty($_POST['notificarEstudiante'])) {
                require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
                require_once __DIR__ . "/../../firebase/firebase_helper.php";
                enviarEmailCalificacionTFG($idEstudiante);
                $tokenEstudiante = obtenerTokenUsuario($idEstudiante, 'estudiante');
                if ($tokenEstudiante) {
                    enviarNotificacionFirebase($tokenEstudiante, "Calificación TFG", "Tu TFG ha sido calificado con un " . $nota . " sobre 10.");
                }
            }
            $_SESSION['exito'] = "La calificación del TFG ha sido guardada correctamente.";
        } else {
            $_SESSION['errores'] = "Ocurrió un error al intentar guardar la calificación.";
        }
    } else {
        $_SESSION['errores'] = $errores;
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/academico/evaluarTFG.php?idEstudiante=" . (int)($idEstudiante ?? 0));
exit;
