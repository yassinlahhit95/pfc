<?php
session_start();
require_once __DIR__ . "/../../../modelos/calificaciones.php";

$hayError = false;

if (isset($_GET['id'])) {
    $idCalificacion = trim($_GET['id']);
    if (eliminarCalificacion($idCalificacion)) {
        $_SESSION['exito'] = "Listo! CalificaciÃ³n eliminada.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Vaya, no se pudo eliminar la calificaciÃ³n.";
    }
} else {
    $hayError = true;
    $_SESSION['error'] = "Vaya, no se especificÃ³ quÃ© calificaciÃ³n borrar.";
}

header("Location: ../../../vistas/profesores/calificaciones/lista.php");
exit;
