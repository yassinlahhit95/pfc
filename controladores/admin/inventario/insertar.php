<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['guardarArticulo'])) {
    $nombre = trim($_POST['nombreArticulo']);
    $numeroSerie = trim($_POST['numeroSerie']);

    $errores = [];

    if (empty($nombre)) {
        $errores['nombreArticulo'] = "El nombre es obligatorio.";
    }
    if (empty($numeroSerie)) {
        $errores['numeroSerie'] = "El número de serie es obligatorio.";
    }

    if (empty($errores)) {
        if (checkArticuloExistente($numeroSerie)) {
            $errores['numeroSerie'] = "Este número de serie ya está registrado.";
        }
    }

    if (empty($errores)) {
        if (insertarArticulo($nombre, $numeroSerie)) {
            $_SESSION['exito'] = "Artículo añadido.";
            header("Location: ../../../vistas/admin/inventario/verInventario.php");
            exit;
        }
        $_SESSION['errores'] = "No se pudo añadir el artículo.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_inventario'] = $_POST;
    }

    header("Location: ../../../vistas/admin/inventario/agregarArticulo.php");
    exit;
}

header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
?>
