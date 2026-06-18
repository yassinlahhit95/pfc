<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
$idProfesor = $_SESSION['idProfesor'];

require_once __DIR__ . "/../../../modelos/aula.php";

$idSesion = (int)($_POST['idSesion'] ?? 0);
$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$fechaSesion = $_POST['fechaSesion'] ?? '';
$horaSesion = $_POST['horaSesion'] ?? '';
$enlaceReunion = $_POST['enlaceReunion'] ?? '';
$plataforma = $_POST['plataforma'] ?? '';

$errores = [];
if (!$idSesion) $errores[] = "No se ha especificado la sesión.";
if (!$titulo) $errores[] = "El título es obligatorio.";
if (!$fechaSesion) $errores[] = "La fecha es obligatoria.";
if (!$horaSesion) $errores[] = "La hora es obligatoria.";

// Validar fecha y hora
if ($fechaSesion && $horaSesion) {
    $errFecha = validarFechaHoraSesion($fechaSesion, $horaSesion);
    if ($errFecha) $errores[] = $errFecha;
}

// Validar enlace de reunión
if ($enlaceReunion) {
    $errEnlace = validarEnlaceReunion($enlaceReunion);
    if ($errEnlace) $errores[] = $errEnlace;
}

if ($errores) {
    $_SESSION['errores'] = implode('<br>', $errores);
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . (int)($_POST['idModulo'] ?? 0));
    exit;
}

$sesion = obtenerSesionPorId($idSesion);
if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    $_SESSION['errores'] = "No tienes permiso para editar esta sesión.";
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

$ok = actualizarSesionViva($idSesion, $titulo, $descripcion, $fechaSesion, $horaSesion, $enlaceReunion, $plataforma);

if ($ok) {
    // Notificar a estudiantes sobre cambios en la sesión
    notificarEstudiantesPorModulo(
        $sesion['idModulo'],
        'sesion_modificada',
        'Sesión actualizada: ' . $titulo,
        'La sesión ha sido actualizada. Nueva fecha: ' . date('d/m/Y H:i', strtotime($fechaSesion . ' ' . $horaSesion)),
        $idSesion,
        'sesion'
    );

    $_SESSION['exito'] = "Sesión actualizada y se notificó a los estudiantes";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $sesion['idModulo']);
} else {
    $_SESSION['errores'] = "Error al actualizar la sesión. Inténtalo de nuevo.";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $sesion['idModulo']);
}
?>
