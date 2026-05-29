<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idCarpeta = intval($_GET['id'] ?? 0);
$idModulo  = intval($_GET['modulo'] ?? 0);

if ($idCarpeta > 0) {
    $carpeta = obtenerCarpetaAulaPorId($idCarpeta);
    if ($carpeta && $carpeta['idProfesor'] == $_SESSION['idProfesor']) {
        borrarCarpetaAula($idCarpeta);
        $_SESSION['exito'] = "Carpeta eliminada. Los archivos pasan a sin carpeta.";
        $idModulo = $idModulo ?: $carpeta['idModulo'];
    }
}
header("Location: ../../../vistas/profesores/aula/modulo.php?id=$idModulo");
exit;
