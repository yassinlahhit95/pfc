<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (!empty($_POST['idEstudiante'])) {
    if (!Security::validateCSRFToken(null, false)) {
        if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/profesores/estudiantes/lista.php"); exit;
    }
    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);

    if ($idEstudiante > 0) {
        $_esTutor      = !empty($_SESSION['esTutor']);
        $_idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
        $_autorizado   = $_esTutor && $_idCicloTutor
            ? estudiantePerteneceACiclo($idEstudiante, $_idCicloTutor)
            : estudiantePerteneceAProfesor($idEstudiante, $_SESSION['idProfesor']);
        if (!$_autorizado) {
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
if ($isAjax) {
    header('Content-Type: application/json');
    $ok = empty($_SESSION['errores']);
    $msg = $ok ? ($_SESSION['exito'] ?? 'Estudiante eliminado correctamente') : (is_array($_SESSION['errores']) ? implode(', ', $_SESSION['errores']) : $_SESSION['errores']);
    unset($_SESSION['exito'], $_SESSION['errores']);
    echo json_encode(['ok' => $ok, 'msg' => $msg]);
    exit;
}
header("Location: ../../../vistas/profesores/estudiantes/lista.php");
exit;
