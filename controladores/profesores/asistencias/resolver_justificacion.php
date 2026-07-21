<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";

$_back = "../../../vistas/profesores/asistencias/justificaciones.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

$idProfesor      = (int)$_SESSION['idProfesor'];
$idJustificacion = (int)($_POST['idJustificacion'] ?? 0);
$decision        = $_POST['decision'] ?? '';
$motivoRechazo   = trim($_POST['motivoRechazo'] ?? '');

if (!in_array($decision, ['aprobar', 'rechazar'], true)) {
    header("Location: $_back"); exit;
}
if ($decision === 'rechazar' && $motivoRechazo === '') {
    $_SESSION['errores'] = "Indica el motivo del rechazo.";
    header("Location: $_back"); exit;
}

$justificacion = justificacionPerteneceAProfesor($idJustificacion, $idProfesor);
if (!$justificacion) {
    $_SESSION['errores'] = "No tienes permiso sobre esta justificación.";
    header("Location: $_back"); exit;
}
if ($justificacion['estado'] !== 'pendiente') {
    $_SESSION['errores'] = "Esta justificación ya ha sido resuelta.";
    header("Location: $_back"); exit;
}

$ok = resolverJustificacionFalta(
    $idJustificacion,
    (int)$justificacion['idAsistencia'],
    $decision === 'aprobar',
    $idProfesor,
    $motivoRechazo
);

$_SESSION[$ok ? 'exito' : 'errores'] = $ok
    ? ($decision === 'aprobar' ? "Justificación aprobada." : "Justificación rechazada.")
    : "No se pudo procesar la justificación. Inténtalo de nuevo.";
header("Location: $_back"); exit;
