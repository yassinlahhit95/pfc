<?php
session_start();
require_once "../../../modelos/inventario.php";
if (isset($_POST['guardarArticulo'])) {
    $nombre = $_POST['nombreArticulo'];
    $nSerie = $_POST['numeroSerie'];
    if (empty($nombre)) {
        $_SESSION['error'] = "Nombre obligatorio";
    } else if (empty($nSerie)) {
        $_SESSION['error'] = "Serie obligatoria";
    } else if (insertarArticulo($nombre, $nSerie)) {
        $_SESSION['exito'] = "Ok";
        header("Location: /pfc/vistas/admin/inventario/verInventario.php");
        exit;
    } else {
        $_SESSION['error'] = "Error BD";
    }
    header("Location: /pfc/vistas/admin/inventario/verInventario.php");
    exit;
}
header("Location: /pfc/vistas/admin/inventario/verInventario.php");
exit;

