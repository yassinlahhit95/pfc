<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idArchivo = intval($_GET['id'] ?? 0);
$regresar  = intval($_GET['modulo'] ?? 0);

if ($idArchivo > 0) {
    $archivo = obtenerArchivoPorId($idArchivo);
    if ($archivo && $archivo['idProfesor'] == $_SESSION['idProfesor']) {
        borrarArchivoAula($idArchivo);
        $_SESSION['exito'] = "Archivo movido a la papelera.";
        $regresar = $regresar ?: $archivo['idModulo'];
    }
}
$destino = "../../../vistas/profesores/aula/recursos.php?id=$regresar";
if (!empty($archivo['idCarpeta'])) $destino .= "&carpeta=" . $archivo['idCarpeta'];
header("Location: $destino");
exit;
