<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idCarpeta  = intval($_POST['idCarpeta'] ?? 0);
$nombre     = trim($_POST['nombre'] ?? '');
$color      = trim($_POST['color'] ?? '#0ea5e9');
$idModulo   = intval($_POST['idModulo'] ?? 0);

if ($idCarpeta > 0 && !empty($nombre)) {
    $carpeta = obtenerCarpetaAulaPorId($idCarpeta);
    if ($carpeta && $carpeta['idProfesor'] == $_SESSION['idProfesor']) {
        actualizarCarpetaAula($idCarpeta, $nombre, $color);
        $_SESSION['exito'] = "Carpeta actualizada.";
    }
}
header("Location: ../../../vistas/profesores/aula/modulo.php?id=$idModulo");
exit;
