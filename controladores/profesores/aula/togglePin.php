<?php
// Fijar / desfijar (pin) un archivo o carpeta. Solo el profesor propietario.
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idProfesor = $_SESSION['idProfesor'];
$tipo       = $_GET['tipo'] ?? '';          // archivo | carpeta
$id         = intval($_GET['id'] ?? 0);
$idModulo   = intval($_GET['modulo'] ?? 0);
$carpeta    = intval($_GET['carpeta'] ?? 0);

if ($id > 0 && in_array($tipo, ['archivo', 'carpeta'])) {
    if ($tipo === 'archivo') {
        $item = obtenerArchivoPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            togglePinArchivoAula($id);
            $idModulo = $idModulo ?: $item['idModulo'];
        }
    } else {
        $item = obtenerCarpetaAulaPorId($id);
        if ($item && $item['idProfesor'] == $idProfesor) {
            togglePinCarpetaAula($id);
            $idModulo = $idModulo ?: $item['idModulo'];
        }
    }
}

$destino = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if ($carpeta) $destino .= "&carpeta=$carpeta";
header("Location: $destino");
exit;
