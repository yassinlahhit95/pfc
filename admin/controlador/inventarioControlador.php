<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/inventario.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new inventario($conexion);

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['error_nombre']);

    // Guardar equipo
    if ($accion == 'insertar') {
        $nombre = trim($_POST['nombreArticulo']);
        if ($nombre == "") {
            $_SESSION['error_nombre'] = "Nombre obligatorio";
            header("Location: ../vistas/inventario/verInventario.php");
            exit;
        }
        $datos = ['nombreArticulo' => $nombre, 'cantidadTotal' => 1];
        $modelo->insertarArticuloModelo($datos);
        $_SESSION['exito'] = "Equipo guardado";
        header("Location: ../vistas/inventario/verInventario.php");
        exit;
    }

    // Prestar equipo
    if ($accion == 'prestar') {
        $idArt = $_POST['idArticulo'];
        $idEst = $_POST['idEstudiante'];
        $fecha = $_POST['fechaPrestamo'];

        // Convertir fecha simple DD-MM-YYYY a BD
        $partes = explode("-", $fecha);
        $fechaBD = $partes[2]."-".$partes[1]."-".$partes[0];

        $modelo->realizarPrestamoModelo($idArt, $idEst, $fechaBD);
        $_SESSION['exito'] = "Préstamo realizado";
        header("Location: ../vistas/inventario/gestionarPrestamos.php");
        exit;
    }

    // Devolver equipo
    if ($accion == 'devolver') {
        $idPres = $_POST['idPrestamo'];
        $modelo->devolverPrestamoModelo($idPres);
        $_SESSION['exito'] = "Equipo devuelto";
        header("Location: ../vistas/inventario/gestionarPrestamos.php");
        exit;
    }

    if ($accion == 'eliminar') {
        $id = $_POST['idArticulo'];
        $modelo->eliminarArticuloModelo($id);
        header("Location: ../vistas/inventario/verInventario.php");
        exit;
    }
}
?>