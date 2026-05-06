<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";

$hayError = false;

if (isset($_POST['idModulo'])) {
    $idModuloParaBorrar = trim($_POST['idModulo']);
    if (eliminarModulo($idModuloParaBorrar)) {
        $_SESSION['exito'] = "Módulo eliminado.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>
