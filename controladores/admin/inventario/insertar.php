<?php
session_start();
require_once "../../../modelos/inventario.php";

if (isset($_POST['guardarArticulo'])) {
    $nombre = trim($_POST['nombreArticulo']);
    $nSerie = trim($_POST['numeroSerie']);

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre del dispositivo es obligatorio.";
        header("Location: /pfc/vistas/admin/inventario/verInventario.php");
    } else if (empty($nSerie)) {
        $_SESSION['error'] = "El número de serie es obligatorio.";
        header("Location: /pfc/vistas/admin/inventario/verInventario.php");
    } else {
        if (insertarArticulo($nombre, $nSerie)) {
            $_SESSION['exito'] = "Dispositivo registrado correctamente";
        } else {
            $_SESSION['error'] = "Error al registrar el dispositivo";
        }
        header("Location: /pfc/vistas/admin/inventario/verInventario.php");
    }
    exit;
}

header("Location: /pfc/vistas/admin/inventario/verInventario.php");
exit;
?>