<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/ejercicios.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['calificar'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/profesores/ejercicios/panel.php"); exit;
    }
    $idEjercicio  = intval($_POST['idEjercicio'] ?? 0);
    $idEstudiante = intval($_POST['idEstudiante'] ?? 0);
    $nota         = floatval(str_replace(',', '.', $_POST['nota'] ?? ''));
    $comentario   = trim($_POST['comentario'] ?? '');

    $ej = obtenerEjercicioPorId($idEjercicio);
    if (!$ej || $ej['idProfesor'] != $_SESSION['idProfesor']) {
        header("Location: ../../../vistas/profesores/ejercicios/panel.php");
        exit;
    }

    if ($nota < 0 || $nota > 10) {
        $_SESSION['errores'] = "La nota debe estar entre 0 y 10.";
    } elseif (calificarEntrega($idEjercicio, $idEstudiante, $nota, $comentario)) {
        $_SESSION['exito'] = "La calificación ha sido guardada correctamente.";
    } else {
        $_SESSION['errores'] = "No se pudo guardar la calificación.";
    }
    header("Location: ../../../vistas/profesores/ejercicios/entregas.php?id=$idEjercicio");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
