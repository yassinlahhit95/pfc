<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../vistas/login.php");
    exit;
}

require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../include/Logger.php";

$idProfesor = $_SESSION['idProfesor'];
$idSesion = (int)($_GET['id'] ?? 0);

$sesion = obtenerSesionPorId($idSesion);

if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    header("Location: ../../vistas/profesores/aula/sesiones.php");
    exit;
}

$borrado = borrarSesionViva($idSesion);

if ($borrado) {
    $_SESSION['exito'] = 'Sesión eliminada exitosamente';
    Logger::activity('SESION_ELIMINADA', $idProfesor, ['idSesion' => $idSesion, 'titulo' => $sesion['titulo']]);
} else {
    $_SESSION['errores'] = 'Error al eliminar la sesión. Intenta de nuevo.';
    Logger::error('Error eliminando sesión', ['profesor' => $idProfesor, 'sesion' => $idSesion]);
}

header("Location: ../../vistas/profesores/aula/sesiones.php");
?>
