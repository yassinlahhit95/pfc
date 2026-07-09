<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_eventos');
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/log.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/eventos/agregarEvento.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/eventos/agregarEvento.php"); exit;
}

$titulo      = Security::sanitize($_POST['tituloEvento'] ?? '');
$descripcion = Security::sanitize($_POST['descripcionEvento'] ?? '');
$fecha       = Security::sanitize($_POST['fechaEvento'] ?? '');
$hora        = Security::sanitize($_POST['horaEvento'] ?? '');
$ubicacion   = Security::sanitize($_POST['ubicacionEvento'] ?? '');

$errores = [];
if (empty($titulo)) $errores[] = "El título es obligatorio.";
if (empty($fecha))  $errores[] = "La fecha es obligatoria.";

if ($errores) {
    $_SESSION['errores'] = $errores;
    header("Location: ../../../vistas/secretaria/eventos/agregarEvento.php");
    exit;
}

$ok = insertarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion);

if ($ok) {
    registrarAccionSecretaria('insertar', 'eventos', null, $titulo);
    $_SESSION['exito'] = "Evento creado correctamente.";
} else {
    $_SESSION['errores'] = "Error al crear el evento.";
}
header("Location: ../../../vistas/secretaria/eventos/gestionEventos.php");
exit;
