<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['actualizarArticulo'])) {
    $idArticulo = trim($_POST['idArticulo']);
    $nombreArticulo = trim($_POST['nombreArticulo']);
    $numeroSerie = trim($_POST['numeroSerie']);

    $errores = '';
    if (empty($nombreArticulo)) $errores = "El nombre es obligatorio.";
    elseif (empty($numeroSerie)) $errores = "El número de serie es obligatorio.";
    elseif (checkArticuloExistente($numeroSerie, $idArticulo)) $errores = "Este número de serie ya está registrado por otro artículo.";

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_inventario'] = $_POST;
    } else {
        $datosArticuloActual = obtenerArticuloPorId($idArticulo);
        $estadoActual = $datosArticuloActual['estado'] ?? 'Disponible';

        if (actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoActual)) {
            $_SESSION['exito'] = "Artículo actualizado.";
            header("Location: ../../../vistas/admin/inventario/verInventario.php");
            exit;
        }
        $_SESSION['errores'] = "No se puede actualizar el artículo.";
    }
    
    header("Location: ../../../vistas/admin/inventario/modificarArticulo.php?idArticulo=" . $idArticulo);
    exit;
}

header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
?>
