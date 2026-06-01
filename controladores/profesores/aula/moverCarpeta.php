<?php
session_start();
require_once __DIR__ . "/../../../modelos/aula.php";

if (empty($_SESSION['idProfesor'])) { header("Location: ../../../vistas/login.php"); exit; }

$idProfesor = $_SESSION['idProfesor'];
$id        = intval($_GET['id'] ?? 0);              // carpeta a mover
$destino   = intval($_GET['destino'] ?? 0) ?: null; // nuevo padre (0 = raíz)
$idModulo  = intval($_GET['modulo'] ?? 0);

if ($id > 0) {
    $carpeta = obtenerCarpetaAulaPorId($id);
    if ($carpeta && $carpeta['idProfesor'] == $idProfesor) {
        if (moverCarpetaAula($id, $destino)) {
            $_SESSION['exito'] = "Carpeta movida.";
        } else {
            $_SESSION['errores'] = "No se pudo mover la carpeta a ese destino.";
        }
        $idModulo = $idModulo ?: $carpeta['idModulo'];
    }
}

$dest = "../../../vistas/profesores/aula/recursos.php?id=$idModulo";
if ($destino) $dest .= "&carpeta=$destino";
header("Location: $dest");
exit;
