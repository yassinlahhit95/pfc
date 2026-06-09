<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../../vistas/login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";

$idSesion = $_GET['id'] ?? $_POST['idSesion'] ?? 0;

if (!$idSesion) {
    $_SESSION['errores'] = "Sesión no especificada";
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

$sesion = obtenerSesionPorId($idSesion);
if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    $_SESSION['errores'] = "No tienes permiso para eliminar esta sesión";
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

$idModulo = $sesion['idModulo'];
$ok = borrarSesionViva($idSesion);

if ($ok) {
    $_SESSION['exito'] = "Sesión eliminada";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $idModulo);
} else {
    $_SESSION['errores'] = "Error al eliminar la sesión";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $idModulo);
}
?>
