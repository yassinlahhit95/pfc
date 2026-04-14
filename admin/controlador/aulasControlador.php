<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/aulas.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new aula($conexion);

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['error_nombre']);

    if ($accion == 'insertar') {
        $nombre = trim($_POST['nombreAula']);

        if ($nombre == "") {
            $_SESSION['error_nombre'] = "El nombre es obligatorio";
            header("Location: ../vistas/aulas/verAulas.php");
            exit;
        }

        $datos = ['nombreAula' => $nombre];
        $modelo->insertarAulasModelo($datos);
        $_SESSION['exito'] = "Aula guardada";
        header("Location: ../vistas/aulas/verAulas.php");
        exit;
    }

    if ($accion == 'eliminar') {
        $id = $_POST['idAula'];
        $modelo->eliminarAulasModelo($id);
        $_SESSION['exito'] = "Aula eliminada";
        header("Location: ../vistas/aulas/verAulas.php");
        exit;
    }
}
?>