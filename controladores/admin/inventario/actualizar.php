<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['actualizarArticulo'])) {
    $idArticulo = trim($_POST['idArticulo']);
    $nombreArticulo = trim($_POST['nombreArticulo']);
    $numeroSerie = trim($_POST['numeroSerie']);

    $errores_campos = [];
    if (empty($nombreArticulo) || empty($numeroSerie)) {
        $errores_campos['datos'] = "Faltan datos.";
    }

    if (empty($errores_campos)) {
        if (checkArticuloExistente($numeroSerie, $idArticulo)) {
            $errores_campos['numeroSerie'] = "Este número de serie ya está registrado por otro artículo.";
        }
    }

    if (empty($errores_campos)) {
        $datosArticuloActual = obtenerArticuloPorId($idArticulo);
        $estadoActual = $datosArticuloActual['estado'] ?? 'Disponible';

        if (actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoActual)) {
            $_SESSION['exito'] = "Artículo actualizado.";
            header("Location: ../../../vistas/admin/inventario/verInventario.php");
            exit;
        }
        $_SESSION['error'] = "No se puede actualizar el artículo.";
    } else {
        $_SESSION['errores'] = $errores_campos;
        $_SESSION['datos_inventario'] = $_POST;
    }
    
    header("Location: ../../../vistas/admin/inventario/modificarArticulo.php?idArticulo=" . $idArticulo);
    exit;
}

header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
?>
