<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['idArticulo'])) {
    $idArticulo = trim($_POST['idArticulo']);
    if (eliminarArticulo($idArticulo)) {
        $_SESSION['exito'] = "Listo! ArtÃ­culo eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Vaya, ha ocurrido un error al intentar eliminar el artÃ­culo.";
    }
}
header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
