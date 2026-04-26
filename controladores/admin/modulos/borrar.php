<?php
session_start();
require_once "../../../modelos/modulos.php";

if (isset($_POST['idModulo'])) {
    $id = $_POST['idModulo'];
    if (eliminarModulo($id)) {
        $_SESSION['exito'] = "Módulo eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el módulo.";
    }
}
header("Location: /pfc/vistas/admin/modulos/verModulos.php");
exit;
?>

