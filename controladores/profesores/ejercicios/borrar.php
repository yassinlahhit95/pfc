<?php
session_start();
require_once __DIR__ . "/../../../modelos/ejercicios.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idEjercicio = intval($_GET['id'] ?? 0);
if ($idEjercicio > 0) {
    $ej = obtenerEjercicioPorId($idEjercicio);
    if ($ej && $ej['idProfesor'] == $_SESSION['idProfesor']) {
        borrarEjercicio($idEjercicio);
        $_SESSION['exito'] = "Ejercicio eliminado.";
    }
}
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
