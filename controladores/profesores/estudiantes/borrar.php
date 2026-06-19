<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/estudiantes.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (!empty($_POST['idEstudiante'])) {
    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);

    if ($idEstudiante > 0) {
        if (!estudiantePerteneceAProfesor($idEstudiante, $_SESSION['idProfesor'])) {
            $_SESSION['errores'] = "No tienes permiso sobre este estudiante.";
            header("Location: ../../../vistas/profesores/estudiantes/lista.php"); exit;
        }
        $resultado = eliminarEstudiante($idEstudiante);
        if ($resultado) {
            $_SESSION['exito'] = "El estudiante ha sido eliminado correctamente.";
        } else {
            $_SESSION['errores'] = "No se pudo eliminar al estudiante.";
        }
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/profesores/estudiantes/lista.php");
exit;
