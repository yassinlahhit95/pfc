<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/ciclos.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new ciclo($conexion);

if (isset($_POST['guardarCiclo'])) {
    $accion = $_POST['accion'];

    unset($_SESSION['errores']);
    unset($_SESSION['datos_ciclo']);

    $nombre = trim($_POST['nombreCiclo']);
    $descripcion = trim($_POST['descripcionCiclo']);
    $errores = [];

    if (!isset($_POST['nombreCiclo']) || empty($nombre)) {
        $errores['nombreCiclo'] = "El nombre del ciclo es obligatorio";
    }

    if (!isset($_POST['descripcionCiclo']) || empty($descripcion)) {
        $errores['descripcionCiclo'] = "La descripción es obligatoria";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_ciclo'] = $_POST;
        $url = ($accion == 'insertar') ? "agregarCiclos.php" : "modificarCiclos.php?id=" . $_POST['idCiclo'];
        header("Location: ../vistas/ciclos/" . $url);
        exit;
    }

    $datos = [
        'nombreCiclo' => $nombre,
        'descripcionCiclo' => $descripcion
    ];

    if ($accion == 'insertar') {
        $modelo->insertarCicloModelo($datos);
        $_SESSION['exito'] = "Ciclo creado";
    } else {
        $datos['idCiclo'] = $_POST['idCiclo'];
        $modelo->actualizarCicloModelo($datos);
        $_SESSION['exito'] = "Ciclo actualizado";
    }

    header("Location: ../vistas/ciclos/verCiclos.php");
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
        $id = $_POST['idCiclo'];
        
        // Fulfill requirement: "When a Cycle is deleted, all its related Modules and their Retos must also be deleted automatically"
        // We delete the cycle (which cascades to modules and modulo_reto links)
        // Then we can optionally delete orphaned retos.
        
        $modelo->eliminarCicloModelo($id);
        
        // Cleanup orphaned retos (those not linked to any module anymore)
        $conexion->query("DELETE FROM retos WHERE idReto NOT IN (SELECT idReto FROM modulo_reto)");
        
        $_SESSION['exito'] = "Ciclo y sus componentes borrados";
        header("Location: ../vistas/ciclos/verCiclos.php");
        exit;
    }
}
?>
