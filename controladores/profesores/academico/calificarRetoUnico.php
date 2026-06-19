<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/retos.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
$idReto       = (int)($_POST['idReto'] ?? 0);
$idCiclo      = (int)($_POST['idCiclo'] ?? 0);
$nota         = trim($_POST['nota'] ?? '');
$nota         = str_replace(',', '.', $nota);

if ($idEstudiante && $idReto && !retoPerteneceAProfesor($idReto, $_SESSION['idProfesor'])) {
    $_SESSION['errores'] = "No tienes permiso para calificar este reto.";
    header("Location: ../../../vistas/profesores/academico/calificacionesRetos.php?idReto={$idReto}&idCiclo={$idCiclo}");
    exit;
}

if ($idEstudiante && $idReto) {
    if ($nota === '') {
        eliminarCalificacionReto($idEstudiante, $idReto);
        $_SESSION['exito'] = "La nota ha sido eliminada.";
    } elseif (!is_numeric($nota) || $nota < 0 || $nota > 10) {
        $_SESSION['errores'] = "La nota debe ser un número entre 0 y 10.";
    } else {
        if (calificarReto($idEstudiante, $idReto, floatval($nota))) {
            $_SESSION['exito'] = "La nota ha sido guardada correctamente.";
        } else {
            $_SESSION['errores'] = "No se pudo guardar la nota. Inténtalo de nuevo.";
        }
    }
} else {
    $_SESSION['errores'] = "Los datos del formulario no son válidos.";
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (isset($_SESSION['exito'])) {
    header("Location: ../../../vistas/profesores/academico/calificacionesRetos.php?idReto={$idReto}&idCiclo={$idCiclo}");
} else {
    header("Location: ../../../vistas/profesores/academico/evaluarReto.php?idEstudiante={$idEstudiante}&idReto={$idReto}&idCiclo={$idCiclo}");
}
exit;
