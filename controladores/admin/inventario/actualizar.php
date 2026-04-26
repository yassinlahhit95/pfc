<?php
session_start();
require_once "../../../modelos/inventario.php";

if (isset($_POST['actualizarArticulo'])) {
    $idArticuloRecibido = $_POST['idArticulo'];
    $nombreNuevo = trim($_POST['nombreArticulo']);
    $serieNueva = trim($_POST['numeroSerie']);

    if (empty($nombreNuevo) || empty($serieNueva)) {
        $_SESSION['error'] = strtoupper("TODOS LOS CAMPOS SON OBLIGATORIOS.");
    } else {
        // Obtenemos el artículo para mantener su estado actual
        $datosArticuloActual = obtenerArticuloPorId($idArticuloRecibido);
        $estadoActual = $datosArticuloActual['estado'];

        $resultado = actualizarArticulo($idArticuloRecibido, $nombreNuevo, $serieNueva, $estadoActual);
        
        if ($resultado == true) {
            $_SESSION['exito'] = strtoupper("ARTÍCULO ACTUALIZADO CORRECTAMENTE.");
            header("Location: /pfc/vistas/admin/inventario/verInventario.php");
            exit;
        } else {
            $_SESSION['error'] = strtoupper("ERROR AL ACTUALIZAR EN LA BASE DE DATOS.");
        }
    }
    
    header("Location: /pfc/vistas/admin/inventario/modificarArticulo.php?idArticulo=" . $idArticuloRecibido);
    exit;
}

header("Location: /pfc/vistas/admin/inventario/verInventario.php");
exit;
?>

