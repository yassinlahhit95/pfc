<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/retos.php";

if (!isset($_POST['guardarNotasReto'])) {
    header("Location: ../../../vistas/profesores/academico/calificacionesRetos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$idReto  = (int)($_POST['idReto']  ?? 0);
$idCiclo = (int)($_POST['idCiclo'] ?? 0);

if (!$idReto || !retoPerteneceAProfesor($idReto, $_SESSION['idProfesor'])) {
    $_SESSION['errores'] = "No tienes permiso para calificar este reto.";
    header("Location: ../../../vistas/profesores/academico/calificacionesRetos.php"); exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idModulo       = (int)($_POST['idModulo'] ?? 0);
$idsEstudiantes = $_POST['estudiantes'] ?? [];
$notas          = $_POST['notas'] ?? [];
$hayError       = false;

foreach ($idsEstudiantes as $indice => $idEstudiante) {
    $idEstudiante = trim($idEstudiante);
    $nota         = trim($notas[$indice]);

    if (!empty($nota)) {
        if (!is_numeric($nota) || $nota < 0 || $nota > 10) {
            $hayError = true;
        }
    }

    if (!$hayError) {
        if (empty($nota)) {
            eliminarCalificacionReto($idEstudiante, $idReto);
        } else {
            if (!calificarReto($idEstudiante, $idReto, $nota)) {
                $hayError = true;
            }
        }
    }

    if ($hayError) break;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($hayError) {
    $_SESSION['errores'] = "Las notas deben ser números entre 0 y 10.";
} else {
    $_SESSION['exito'] = "Calificaciones guardadas.";
}

header("Location: ../../../vistas/profesores/academico/calificacionesRetos.php?idCiclo=$idCiclo&idReto=$idReto");
exit;
