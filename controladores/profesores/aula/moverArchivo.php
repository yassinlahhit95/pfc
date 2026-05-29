<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idArchivo = intval($_GET['id'] ?? 0);
$idCarpeta = intval($_GET['carpeta'] ?? 0);
$idModulo  = intval($_GET['modulo'] ?? 0);

if ($idArchivo > 0) {
    $archivo = obtenerArchivoPorId($idArchivo);
    if ($archivo && $archivo['idProfesor'] == $_SESSION['idProfesor']) {
        moverArchivoAula($idArchivo, $idCarpeta ?: null);
        $_SESSION['exito'] = "Archivo movido.";
        $idModulo = $idModulo ?: $archivo['idModulo'];
    }
}

header("Location: ../../../vistas/profesores/aula/modulo.php?id=$idModulo");
exit;
