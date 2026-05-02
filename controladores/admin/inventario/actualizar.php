<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['actualizarArticulo'])) {
    $idArticulo = trim($_POST['idArticulo'] ?? '');
    $nombreArticulo = trim($_POST['nombreArticulo'] ?? '');
    $numeroSerie = trim($_POST['numeroSerie'] ?? '');

    $hayError = false;

    if (empty($nombreArticulo) || empty($numeroSerie)) {
        $_SESSION['error'] = "Faltan datos.";
        $hayError = true;
    }

    if (!$hayError) {
        // Obtenemos el artÃ­culo para mantener su estado actual
        $datosArticuloActual = obtenerArticuloPorId($idArticulo);
        $estadoActual = $datosArticuloActual['estado'] ?? 'Disponible';

        if (actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoActual)) {
            $_SESSION['exito'] = "Artículo actualizado.";
            header("Location: ../../../vistas/admin/inventario/verInventario.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar.";
        }
    }
    
    header("Location: ../../../vistas/admin/inventario/modificarArticulo.php?idArticulo=" . $idArticulo);
    exit;
}

header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
