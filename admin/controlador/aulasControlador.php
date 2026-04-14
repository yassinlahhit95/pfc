<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/aulas.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new aula($conexion);

if (isset($_POST['guardarAula'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['errores'], $_SESSION['datos_aulas']);

    if ($accion == 'insertar') {
        $errores = [];
        $nombre = trim($_POST['nombreAula'] ?? '');

        if (empty($nombre)) {
            $errores['nombreAula'] = "El nombre del aula es obligatorio.";
        }

        if (count($errores) > 0) {
            $_SESSION['errores'] = $errores;
            $_SESSION['datos_aulas'] = $_POST;
            header("Location: ../vistas/aulas/verAulas.php");
            exit;
        }

        $datos = ['nombreAula' => $nombre];
        if ($modelo->insertarAulasModelo($datos)) {
            $_SESSION['exito'] = "Aula guardada correctamente.";
        }
        header("Location: ../vistas/aulas/verAulas.php");
        exit;
    }
}

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $id = $_POST['idAula'];
    if ($modelo->eliminarAulasModelo($id)) {
        $_SESSION['exito'] = "Aula eliminada correctamente.";
    }
    header("Location: ../vistas/aulas/verAulas.php");
    exit;
}
?>