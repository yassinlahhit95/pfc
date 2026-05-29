<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../../vistas/login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";

$idSesion = $_POST['idSesion'] ?? 0;
$titulo = $_POST['titulo'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$fechaSesion = $_POST['fechaSesion'] ?? '';
$horaSesion = $_POST['horaSesion'] ?? '';
$enlaceReunion = $_POST['enlaceReunion'] ?? '';
$plataforma = $_POST['plataforma'] ?? '';

$errores = [];
if (!$idSesion) $errores[] = "Sesión no especificada";
if (!$titulo) $errores[] = "Título requerido";
if (!$fechaSesion) $errores[] = "Fecha requerida";
if (!$horaSesion) $errores[] = "Hora requerida";

if ($errores) {
    $_SESSION['errores'] = implode(', ', $errores);
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . ($_POST['idModulo'] ?? 0));
    exit;
}

$sesion = obtenerSesionPorId($idSesion);
if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    $_SESSION['errores'] = "No tienes permiso para editar esta sesión";
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

$ok = actualizarSesionViva($idSesion, $titulo, $descripcion, $fechaSesion, $horaSesion, $enlaceReunion, $plataforma);

if ($ok) {
    $_SESSION['exito'] = "Sesión actualizada";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $sesion['idModulo']);
} else {
    $_SESSION['errores'] = "Error al actualizar la sesión";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $sesion['idModulo']);
}
?>
