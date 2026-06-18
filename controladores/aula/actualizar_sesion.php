<?php
require_once __DIR__ . "/../../include/ProfesorGuard.php";

require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../include/Logger.php";

$idProfesor = $_SESSION['idProfesor'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = "Solicitud inválida (error de seguridad). Por favor, intenta de nuevo.";
    header("Location: ../../vistas/profesores/aula/editar.php?id=" . (int)($_POST['idSesion'] ?? 0));
    exit;
}

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

if (empty($titulo)) $errores[] = 'El título es obligatorio.';
if (empty($fechaSesion)) $errores[] = 'La fecha es obligatoria.';
if (empty($horaSesion)) $errores[] = 'La hora es obligatoria.';
if (empty($enlaceReunion)) $errores[] = 'El enlace de reunión es obligatorio.';
if (empty($plataforma)) $errores[] = 'La plataforma es obligatoria.';

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
    $_SESSION['errores'] = 'Error al actualizar la sesión. Inténtalo de nuevo.';
    Logger::error('Error actualizando sesión', ['profesor' => $idProfesor, 'sesion' => $idSesion]);
    header("Location: ../../vistas/profesores/aula/editar.php?id=$idSesion");
}
?>
