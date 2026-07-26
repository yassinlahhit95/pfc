<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (!isset($_POST['calificarTFG'])) {
    header("Location: ../../../vistas/profesores/pfc/lista.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/pfc/lista.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);

if (!estudiantePerteneceAProfesor($idEstudiante, $_SESSION['idProfesor'])) {
    $_SESSION['errores'] = "No tienes permiso sobre este estudiante.";
    header("Location: ../../../vistas/profesores/pfc/lista.php"); exit;
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$nota          = str_replace(',', '.', trim($_POST['nota']));
$observaciones = trim($_POST['observaciones']);
$errores       = '';

if (!is_numeric($nota)) {
    $errores = "La nota debe ser un número.";
} elseif ($nota < 0 || $nota > 10) {
    $errores = "La nota debe estar entre 0 y 10.";
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (!$errores) {
    $resultado = guardarCalificacionTFG($idEstudiante, $nota, $observaciones);

    if ($resultado) {
        if (!empty($_POST['notificarEstudiante'])) {
            require_once __DIR__ . "/../../comunes/notificaciones_grades.php";
            require_once __DIR__ . "/../../firebase/firebase_helper.php";
            require_once __DIR__ . "/../../../modelos/notificaciones.php";
            enviarEmailCalificacionTFG($idEstudiante);
            crearNotificacion($idEstudiante, 'estudiante', 'grade_tfg',
                'Tu TFG ha sido calificado con un ' . $nota . ' sobre 10.',
                '../../../vistas/estudiantes/pfc/lista.php');
            $tokenEstudiante = obtenerTokenUsuario($idEstudiante, 'estudiante');
            if ($tokenEstudiante) {
                enviarNotificacionFirebase($tokenEstudiante, "Calificación TFG", "Tu TFG ha sido calificado con un " . $nota . " sobre 10.", 'grade_tfg');
            }
        }
        $_SESSION['exito'] = "Calificación del TFG guardada correctamente.";
    } else {
        $_SESSION['errores'] = "Error al guardar la calificación.";
    }
} else {
    $_SESSION['errores'] = $errores;
}

$origen = $_POST['origen'] ?? '';
if ($origen === 'calificacionesTFG') {
    header("Location: ../../../vistas/profesores/calificaciones/tfg.php");
} else {
    header("Location: ../../../vistas/profesores/pfc/lista.php");
}
exit;
