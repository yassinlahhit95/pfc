<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/directores.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new director($conexion);

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    unset($_SESSION['error_nombre']);
    unset($_SESSION['error_email']);
    unset($_SESSION['datos_director']);

    if ($accion == 'insertar' || $accion == 'actualizar') {
        
        $nombre = trim($_POST['nombreDirector']);
        $email = trim($_POST['emailDirector']);
        $dni = trim($_POST['dniDirector']);
        $ciudad = trim($_POST['ciudadDirector']);
        $cp = trim($_POST['codigoPostalDirector']);

        $error = false;

        if ($nombre == "") {
            $_SESSION['error_nombre'] = "Nombre obligatorio";
            $error = true;
        }
        if ($email == "") {
            $_SESSION['error_email'] = "Email obligatorio";
            $error = true;
        }

        if ($error == true) {
            $_SESSION['datos_director'] = $_POST;
            if ($accion == 'insertar') {
                header("Location: ../vistas/directores/agregarDirectores.php");
            } else {
                header("Location: ../vistas/directores/modificarDirectores.php?id=" . $_POST['idDirector']);
            }
            exit;
        }

        $datos = [
            'nombre' => $nombre,
            'email' => $email,
            'dni' => $dni,
            'ciudad' => $ciudad,
            'cp' => $cp,
            'direccion' => $_POST['direccionDirector'],
            'idEstado' => 1
        ];

        if ($accion == 'insertar') {
            $modelo->insertarDirectorModelo($datos);
            $_SESSION['exito'] = "Director creado";
        } else {
            $datos['id'] = $_POST['idDirector'];
            $modelo->actualizarDirectorModelo($datos);
            $_SESSION['exito'] = "Director actualizado";
        }

        header("Location: ../vistas/directores/verDirectores.php");
        exit;
    }

    if ($accion == 'eliminar') {
        $id = $_POST['idDirector'];
        $modelo->eliminarDirectorModelo($id);
        $_SESSION['exito'] = "Director borrado";
        header("Location: ../vistas/directores/verDirectores.php");
        exit;
    }
}
?>