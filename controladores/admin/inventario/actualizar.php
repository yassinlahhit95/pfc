<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['actualizarArticulo'])) {
    $idArticulo = trim($_POST['idArticulo'] ?? '');
    $nombreArticulo = trim($_POST['nombreArticulo'] ?? '');
    $numeroSerie = trim($_POST['numeroSerie'] ?? '');

    $hayError = false;

    $errores_campos = [];
    if (empty($nombreArticulo) || empty($numeroSerie)) {
        $_SESSION['error'] = "Faltan datos.";
        $hayError = true;
    }

    // Comprobamos duplicados
    if (!$hayError) {
        require_once __DIR__ . "/../../../modelos/conectar.php";
        $con = obtenerConexion();
        
        $sqlDup = "SELECT idDispositivo FROM dispositivos WHERE numeroSerie = '" . strtoupper($numeroSerie) . "' AND idDispositivo != $idArticulo";
        $resDup = mysqli_query($con, $sqlDup);
        if (mysqli_num_rows($resDup) > 0) {
            $errores_campos['numeroSerie'] = "Este número de serie ya está registrado por otro artículo.";
            $hayError = true;
        }
        mysqli_close($con);
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
            $_SESSION['error'] = "No se pudo actualizar el artículo.";
        }
    } else {
        $_SESSION['errores'] = $errores_campos;
        $_SESSION['datos_inventario'] = $_POST;
    }
    
    header("Location: ../../../vistas/admin/inventario/modificarArticulo.php?idArticulo=" . $idArticulo);
    exit;
}

header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;


