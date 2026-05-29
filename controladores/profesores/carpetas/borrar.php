<?php
session_start();
require_once __DIR__ . "/../../../modelos/ejercicios.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idCarpeta  = intval($_GET['id'] ?? 0);
$idProfesor = $_SESSION['idProfesor'];

if ($idCarpeta > 0) {
    $carpeta = obtenerCarpetaPorId($idCarpeta);
    if ($carpeta && $carpeta['idProfesor'] == $idProfesor) {
        borrarCarpeta($idCarpeta);
        $_SESSION['exito'] = "Carpeta eliminada.";
    }
}
header("Location: ../../../vistas/profesores/ejercicios/panel.php");
exit;
