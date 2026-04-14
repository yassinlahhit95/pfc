<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/inventario.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new inventario($conexion);

if (isset($_POST['guardarArticulo'])) {
    $accion = $_POST['accion'];
    unset($_SESSION['errores'], $_SESSION['datos_inventario']);

    if ($accion == 'insertar') {
        $errores = [];
        $nombre = trim($_POST['nombreArticulo'] ?? '');
        $cantidad = trim($_POST['cantidadTotal'] ?? '');

        if (empty($nombre)) {
            $errores['nombreArticulo'] = "El nombre del recurso es obligatorio.";
        }
        if (empty($cantidad)) {
            $errores['cantidadTotal'] = "La cantidad inicial es obligatoria.";
        } else if (!is_numeric($cantidad)) {
            $errores['cantidadTotal'] = "La cantidad debe ser un número.";
        }

        if (count($errores) > 0) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_inventario'] = $_POST;
            header("Location: ../vistas/inventario/verInventario.php");
            exit;
        }

        $datos = ['nombreArticulo' => $nombre, 'cantidadTotal' => $cantidad];
        if ($modelo->insertarArticuloModelo($datos)) {
            $_SESSION['exito'] = "Artículo guardado en el inventario.";
        }
        header("Location: ../vistas/inventario/verInventario.php");
        exit;
    }
}

if (isset($_POST['guardarPrestamo'])) {
    $accion = $_POST['accion'];
    unset($_SESSION['errores'], $_SESSION['datos_inventario']);

    if ($accion == 'prestar') {
        $errores = [];
        $idArt = $_POST['idArticulo'] ?? '';
        $idEst = $_POST['idEstudiante'] ?? '';
        $fecha = trim($_POST['fechaPrestamo'] ?? '');

        if (empty($idArt)) { $errores['idArticulo'] = "Debe seleccionar un recurso."; }
        if (empty($idEst)) { $errores['idEstudiante'] = "Debe seleccionar un estudiante."; }
        if (empty($fecha)) { $errores['fechaPrestamo'] = "La fecha es obligatoria."; }

        if (count($errores) > 0) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_inventario'] = $_POST;
            header("Location: ../vistas/inventario/gestionarPrestamos.php");
            exit;
        }

        // Convertir fecha simple DD-MM-YYYY a BD si es necesario
        // Pero el modelo parece esperar Y-M-D si usamos date input, o hace su propia conversion.
        // El controlador original hacia: 
        // $partes = explode("-", $fecha);
        // $fechaBD = $partes[2]."-".$partes[1]."-".$partes[0];
        
        // Asumiendo que siguen usando el formato DD-MM-YYYY en el input de texto
        $fechaBD = $fecha;
        if (preg_match("/^\d{2}-\d{2}-\d{4}$/", $fecha)) {
            $partes = explode("-", $fecha);
            $fechaBD = $partes[2]."-".$partes[1]."-".$partes[0];
        }

        if ($modelo->realizarPrestamoModelo($idArt, $idEst, $fechaBD)) {
            $_SESSION['exito'] = "Préstamo realizado correctamente.";
        }
        header("Location: ../vistas/inventario/gestionarPrestamos.php");
        exit;
    }
}

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    // Devolver equipo
    if ($accion == 'devolver') {
        $idPres = $_POST['idPrestamo'];
        if ($modelo->devolverPrestamoModelo($idPres)) {
            $_SESSION['exito'] = "Equipo devuelto correctamente.";
        }
        header("Location: ../vistas/inventario/gestionarPrestamos.php");
        exit;
    }

    if ($accion == 'eliminar') {
        $id = $_POST['idArticulo'];
        if ($modelo->eliminarArticuloModelo($id)) {
            $_SESSION['exito'] = "Artículo eliminado del inventario.";
        }
        header("Location: ../vistas/inventario/verInventario.php");
        exit;
    }
}
?>