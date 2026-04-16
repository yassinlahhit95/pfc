<?php
session_start();
require_once "../../modelos/inventario.php";

if (isset($_GET['id'])) {
    $_POST['idArticulo'] = $_GET['id'];
    $modelo = new inventario();
    if ($modelo->eliminarArticuloModelo()) {
        $_SESSION['exito'] = "Dispositivo eliminado";
    } else {
        $_SESSION['error'] = "No se pudo eliminar el dispositivo";
    }
}

header("Location: ../../vistas/inventario/verInventario.php");
exit;
?>
