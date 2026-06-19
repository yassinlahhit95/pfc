<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
$idProfesor = $_SESSION['idProfesor'];

require_once __DIR__ . "/../../../modelos/aula.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idSesion = $_GET['id'] ?? $_POST['idSesion'] ?? 0;

if (!$idSesion) {
    $_SESSION['errores'] = "No se ha especificado la sesión a eliminar.";
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

$sesion = obtenerSesionPorId($idSesion);
if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    $_SESSION['errores'] = "No tienes permiso para eliminar esta sesión.";
    header("Location: ../../../vistas/profesores/aula/modulos.php");
    exit;
}

$idModulo = $sesion['idModulo'];
$ok       = borrarSesionViva($idSesion);

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($ok) {
    $_SESSION['exito'] = "La sesión ha sido eliminada correctamente.";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $idModulo);
} else {
    $_SESSION['errores'] = "Error al eliminar la sesión. Inténtalo de nuevo.";
    header("Location: ../../../vistas/profesores/aula/modulo.php?id=" . $idModulo);
}
exit;
