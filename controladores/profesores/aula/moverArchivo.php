<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/aula.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/profesores/aula/recursos.php");
    exit;
}

$idArchivo = intval($_POST['id'] ?? 0);
$idCarpeta = intval($_POST['carpeta'] ?? 0);    // destino (0 = raíz)
$idModulo  = intval($_POST['modulo'] ?? 0);
$regresar  = intval($_POST['regresar'] ?? 0);   // carpeta actual a la que volver

if ($idArchivo > 0) {
    $archivo = obtenerArchivoPorId($idArchivo);
    if ($archivo && $archivo['idProfesor'] == $_SESSION['idProfesor']) {
        moverArchivoAula($idArchivo, $idCarpeta ?: null);
        $_SESSION['exito'] = "Archivo movido.";
        $idModulo = $idModulo ?: $archivo['idModulo'];
    }
}

$destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if ($regresar) $destino .= "&carpeta=$regresar";
header("Location: $destino");
exit;
