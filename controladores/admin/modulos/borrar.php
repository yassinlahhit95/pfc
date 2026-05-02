<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idModulo'])) {
    $idModuloParaBorrar = trim($_POST['idModulo']);
    if (eliminarModulo($idModuloParaBorrar)) {
        $_SESSION['exito'] = "Listo! MÃ³dulo eliminado correctamente.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Vaya, no se pudo eliminar el mÃ³dulo.";
    }
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
