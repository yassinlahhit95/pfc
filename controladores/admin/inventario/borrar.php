<?php
session_start();
require_once "../../../modelos/inventario.php";

if (isset($_POST['idArticulo'])) {
    $id = $_POST['idArticulo'];
    if (eliminarArticulo($id)) {
        $_SESSION['exito'] = "Artículo eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el artículo.";
    }
}
header("Location: /pfc/vistas/admin/inventario/verInventario.php");
exit;
?>

