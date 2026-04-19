<?php
session_start();
require_once "../../modelos/inventario.php";

if (isset($_POST['guardarArticulo'])) {
    $nombre = trim($_POST['nombreArticulo'] ?? '');
    $nSerie = trim($_POST['numeroSerie'] ?? '');
    
    $errores = [];
    if (empty($nombre)) $errores['nombreArticulo'] = "El nombre es obligatorio.";
    if (empty($nSerie)) $errores['numeroSerie'] = "El número de serie es obligatorio.";

    if (count($errores) > 0) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_inventario'] = $_POST;
        header("Location: ../../vistas/inventario/verInventario.php");
        exit;
    }

    if (insertarArticulo($nombre, $nSerie)) {
        $_SESSION['exito'] = "Dispositivo registrado correctamente";
    } else {
        $_SESSION['error'] = "Error al registrar el dispositivo";
    }
}

header("Location: ../../vistas/inventario/verInventario.php");
exit;
?>
