<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/modulos.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new modulo($conexion);

if (isset($_POST['guardarModulo'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores']);
    unset($_SESSION['datos_modulo']);

    $nombre = trim($_POST['nombreModulo']);
    $idCiclo = $_POST['idCiclo'];
    $horasMaximas = $_POST['horasMaximas'];
    $errores = [];

    if (!isset($_POST['nombreModulo']) || empty($nombre)) {
        $errores['nombreModulo'] = "El nombre del módulo es obligatorio";
    }

    if (!isset($_POST['idCiclo']) || empty($idCiclo)) {
        $errores['idCiclo'] = "Debes seleccionar un ciclo";
    }

    if (!isset($_POST['horasMaximas']) || empty($horasMaximas) || $horasMaximas <= 0) {
        $errores['horasMaximas'] = "Las horas máximas deben ser un número positivo";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_modulo'] = $_POST;
        $url = ($accion == 'insertar') ? "agregarModulos.php" : "modificarModulos.php?id=" . $_POST['idModulo'];
        header("Location: ../vistas/modulos/" . $url);
        exit;
    }

    $datos = [
        'nombreModulo' => $nombre,
        'idCiclo' => $idCiclo,
        'horasMaximas' => $horasMaximas
    ];

    if ($accion == 'insertar') {
        $modelo->insertarModuloModelo($datos);
        $_SESSION['exito'] = "Módulo creado";
    } else {
        $datos['idModulo'] = $_POST['idModulo'];
        $modelo->actualizarModuloModelo($datos);
        $_SESSION['exito'] = "Módulo actualizado";
    }

    header("Location: ../vistas/modulos/verModulos.php");
    exit;
}

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
        $id = $_POST['idModulo'];
        $modelo->eliminarModuloModelo($id);
        $_SESSION['exito'] = "Módulo borrado";
        header("Location: ../vistas/modulos/verModulos.php");
        exit;
    }
}
?>
