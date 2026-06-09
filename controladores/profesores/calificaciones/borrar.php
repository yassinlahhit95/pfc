<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/calificaciones.php";

$hayError = false;

if (isset($_GET['id'])) {
    $idCalificacion = trim($_GET['id']);
    if (eliminarCalificacion($idCalificacion)) {
        $_SESSION['exito'] = "Calificación eliminada.";
    } else {
        $hayError = true;
        $_SESSION['errores'] = "Error al eliminar.";
    }
} else {
    $hayError = true;
    $_SESSION['errores'] = "Falta ID.";
}

header("Location: ../../../vistas/profesores/calificaciones/lista.php");
exit;
?>
