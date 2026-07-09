<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_eventos');
require_once __DIR__ . "/../../../modelos/eventos.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/eventos/gestionEventos.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/eventos/gestionEventos.php"); exit;
}

$idEvento    = (int)($_POST['idEvento'] ?? 0);
$titulo      = Security::sanitize($_POST['tituloEvento'] ?? '');
$descripcion = Security::sanitize($_POST['descripcionEvento'] ?? '');
$fecha       = Security::sanitize($_POST['fechaEvento'] ?? '');
$hora        = Security::sanitize($_POST['horaEvento'] ?? '');
$ubicacion   = Security::sanitize($_POST['ubicacionEvento'] ?? '');

$errores = [];
if ($idEvento <= 0)  $errores[] = "Evento no válido.";
if (empty($titulo))  $errores[] = "El título es obligatorio.";
if (empty($fecha))   $errores[] = "La fecha es obligatoria.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/eventos/modificarEvento.php?id=$idEvento");
    exit;
}

$ok = actualizarEvento($idEvento, $titulo, $descripcion, $fecha, $hora, $ubicacion);

if ($ok) {
    $_SESSION['exito'] = "Evento actualizado correctamente.";
} else {
    $_SESSION['errores'] = "Error al actualizar el evento.";
}
header("Location: ../../../vistas/secretaria/eventos/gestionEventos.php");
exit;
