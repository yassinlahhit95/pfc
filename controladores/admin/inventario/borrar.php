<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['idArticulo'])) {
    $idArticulo = trim($_POST['idArticulo']);
    if (eliminarArticulo($idArticulo)) {
        $_SESSION['exito'] = "Artículo eliminado.";
    } else {
        $_SESSION['errores'] = "Error al eliminar.";
    }
}
header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
?>
