<?php
// Mueve un archivo a otra carpeta. Solo el profesor propietario. POST + CSRF.
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/Security.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = "La sesión ha caducado. Recarga la página e inténtalo de nuevo.";
    header("Location: ../../../vistas/profesores/aula/recursos.php?id=" . intval($_POST['modulo'] ?? 0));
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
