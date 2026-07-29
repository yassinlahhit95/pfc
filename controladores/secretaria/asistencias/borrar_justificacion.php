<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";

$_back = "../../../vistas/secretaria/asistencias/justificaciones.php";

$idJustificacion = (int)($_POST['idJustificacion'] ?? $_GET['idJustificacion'] ?? 0);

if (!$idJustificacion) {
    header("Location: $_back"); exit;
}

$ok = borrarJustificacionFalta($idJustificacion);

$_SESSION[$ok ? 'exito' : 'errores'] = $ok
    ? "Justificación eliminada y asistencia restaurada."
    : "No se pudo eliminar la justificación.";
header("Location: $_back"); exit;
