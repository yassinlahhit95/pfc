<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['guardarArticulo'])) {
    $nombre = trim($_POST['nombreArticulo']);
    $numeroSerie = trim($_POST['numeroSerie']);

    $errores = '';

    if (empty($nombre)) {
        $errores = "El nombre es obligatorio.";
    }
    if (empty($numeroSerie)) {
        $errores = "El número de serie es obligatorio.";
    }

    if (!$errores) {
        if (checkArticuloExistente($numeroSerie)) {
            $errores = "Este número de serie ya está registrado.";
        }
    }

    if (!$errores) {
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
