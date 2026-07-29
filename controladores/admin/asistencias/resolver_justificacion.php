<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";

$_back = "../../../vistas/admin/asistencias/justificaciones.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: $_back"); exit;
}

$idDirector      = (int)$_SESSION['idDirector'];
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

$con = obtenerConexion();
$res = mysqli_query($con, "SELECT * FROM justificaciones_falta WHERE idJustificacion = $idJustificacion");
$justificacion = mysqli_fetch_assoc($res);

if (!$justificacion) {
    $_SESSION['errores'] = "No existe esta justificación.";
    header("Location: $_back"); exit;
}

$ok = resolverJustificacionFalta(
    $idJustificacion,
    (int)$justificacion['idAsistencia'],
    $decision === 'aprobar',
    $idDirector,
    $motivoRechazo,
    $justificacion['estadoOriginal'],
    'director'
);

$_SESSION[$ok ? 'exito' : 'errores'] = $ok
    ? ($decision === 'aprobar' ? "Justificación aprobada." : "Justificación rechazada.")
    : "No se pudo procesar la justificación.";
header("Location: $_back"); exit;
