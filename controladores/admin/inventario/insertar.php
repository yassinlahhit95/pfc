<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['guardarArticulo'])) {
    $nombre = trim($_POST['nombreArticulo'] ?? '');
    $numeroSerie = trim($_POST['numeroSerie'] ?? '');

    $hayError = false;
    $errores = [];

    if (empty($nombre)) {
        $errores['nombreArticulo'] = "El nombre es obligatorio.";
        $hayError = true;
    }
    if (empty($numeroSerie)) {
        $errores['numeroSerie'] = "El nÃºmero de serie es obligatorio.";
        $hayError = true;
    }

    if (!$hayError) {
        if (insertarArticulo($nombre, $numeroSerie)) {
            $_SESSION['exito'] = "Listo! ArtÃ­culo aÃ±adido correctamente.";
            header("Location: ../../../vistas/admin/inventario/verInventario.php");
            exit;
        } else {
            $_SESSION['error'] = "Vaya, ha ocurrido un error al guardar el artÃ­culo.";
        }
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_inventario'] = $_POST;
    }

    header("Location: ../../../vistas/admin/inventario/verInventario.php");
    exit;
}

header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
