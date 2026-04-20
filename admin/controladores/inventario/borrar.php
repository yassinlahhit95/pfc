<?php
session_start();
require_once "../../../modelos/inventario.php";

$id = null;
if (isset($_POST['idArticulo'])) {
    $id = $_POST['idArticulo'];
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
}

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