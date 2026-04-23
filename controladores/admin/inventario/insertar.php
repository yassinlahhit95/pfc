<?php
session_start();
require_once "../../../modelos/inventario.php";

if (isset($_POST['guardarArticulo'])) {
    $nombre = trim($_POST['nombreArticulo']);
    $serie = trim($_POST['numeroSerie']);
    $estado = $_POST['estadoArticulo'];

    $lista_de_errores = [];

    if (empty($nombre)) {
        $lista_de_errores['nombreArticulo'] = "El nombre es obligatorio.";
    }
    if (empty($serie)) {
        $lista_de_errores['numeroSerie'] = "El número de serie es obligatorio.";
    }

    if (empty($lista_de_errores)) {
        $resultado = insertarArticulo($nombre, $serie, $estado);
        if ($resultado) {
            $_SESSION['exito'] = "Artículo añadido correctamente.";
            header("Location: /pfc/vistas/admin/inventario/verInventario.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_inventario'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/inventario/verInventario.php");
    exit;
}

header("Location: /pfc/vistas/admin/inventario/verInventario.php");
exit;
