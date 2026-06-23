<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/ejercicios.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header("Location: ../../../vistas/profesores/ejercicios/panel.php"); exit; }

$idEjercicio = intval($_POST['id'] ?? 0);
if ($idEjercicio > 0) {
    $ej = obtenerEjercicioPorId($idEjercicio);
    if ($ej && $ej['idProfesor'] == $_SESSION['idProfesor']) {
        borrarEjercicio($idEjercicio);
        $_SESSION['exito'] = "Ejercicio eliminado.";
    }
}
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
