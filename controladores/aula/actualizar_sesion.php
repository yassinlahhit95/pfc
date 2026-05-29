<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../vistas/login.php");
    exit;
}

require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../include/Logger.php";

$idProfesor = $_SESSION['idProfesor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

Security::validateCSRFToken($_POST['csrf_token'] ?? '') or die('CSRF validation failed');

$idSesion = (int)($_POST['id'] ?? 0);
$titulo = Security::sanitize($_POST['titulo'] ?? '');
$descripcion = Security::sanitize($_POST['descripcion'] ?? '');
$fechaSesion = $_POST['fechaSesion'] ?? '';
$horaSesion = $_POST['horaSesion'] ?? '';
$enlaceReunion = $_POST['enlaceReunion'] ?? '';
$plataforma = Security::sanitize($_POST['plataforma'] ?? '');

$sesion = obtenerSesionPorId($idSesion);

if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

$errores = [];

if (empty($titulo)) $errores[] = 'El título es requerido';
if (empty($fechaSesion)) $errores[] = 'La fecha es requerida';
if (empty($horaSesion)) $errores[] = 'La hora es requerida';
if (empty($enlaceReunion)) $errores[] = 'El enlace es requerido';
if (empty($plataforma)) $errores[] = 'La plataforma es requerida';

$validacionFecha = validarFechaHoraSesion($fechaSesion, $horaSesion);
if ($validacionFecha) $errores[] = $validacionFecha;

$validacionURL = validarEnlaceReunion($enlaceReunion);
if ($validacionURL) $errores[] = $validacionURL;

if (!empty($errores)) {
    $_SESSION['errores'] = implode('<br>', $errores);
    header("Location: ../../vistas/profesores/aula/editar.php?id=$idSesion");
    Logger::warning('Validación fallida en actualizar_sesion', ['profesor' => $idProfesor, 'sesion' => $idSesion]);
    exit;
}

$actualizado = actualizarSesionViva($idSesion, $titulo, $descripcion, $fechaSesion, $horaSesion, $enlaceReunion, $plataforma);

if ($actualizado) {
    $_SESSION['exito'] = 'Sesión actualizada exitosamente';
    Logger::activity('SESION_ACTUALIZADA', $idProfesor, ['idSesion' => $idSesion, 'titulo' => $titulo]);
    header("Location: ../../vistas/profesores/aula/sesiones.php");
} else {
    $_SESSION['errores'] = 'Error al actualizar la sesión. Intenta de nuevo.';
    Logger::error('Error actualizando sesión', ['profesor' => $idProfesor, 'sesion' => $idSesion]);
    header("Location: ../../vistas/profesores/aula/editar.php?id=$idSesion");
}
?>
