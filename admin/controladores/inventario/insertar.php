<?php
session_start();
require_once "../../modelos/inventario.php";

if (isset($_POST['guardarArticulo'])) {
    $nombre = trim($_POST['nombreArticulo'] ?? '');
    $nSerie = trim($_POST['numeroSerie'] ?? '');
    
    if (empty($nombre) || empty($nSerie)) {
        $_SESSION['error'] = "El nombre y el número de serie son obligatorios";
    } else {
        $modelo = new inventario();
        if ($modelo->insertarArticuloModelo($nombre, $nSerie)) {
            $_SESSION['exito'] = "Dispositivo registrado correctamente";
        } else {
            $_SESSION['error'] = "Error al registrar el dispositivo";
        }
    }
}

header("Location: ../../vistas/inventario/verInventario.php");
exit;
?>
