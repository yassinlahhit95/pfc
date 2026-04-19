<?php
session_start();
require_once "../../modelos/inventario.php";

$id = $_POST['idArticulo'] ?? $_GET['id'] ?? null;

if ($id) {
    if (borrarArticulo($id)) {
        $_SESSION['exito'] = "Dispositivo eliminado";
    } else {
        $_SESSION['error'] = "No se pudo eliminar el dispositivo";
    }
}

header("Location: ../../vistas/inventario/verInventario.php");
exit;
?>
