<?php
session_start();
require_once "../modelos/conexion.php";
require_once "../modelos/profesores.php";

$con = new Conexion();
$conexion = $con->conectar();
$modelo = new profesor($conexion);

if (isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    // Limpiar errores anteriores
    unset($_SESSION['error_nombre']);
    unset($_SESSION['error_email']);
    unset($_SESSION['error_dni']);
    unset($_SESSION['datos_profesor']);

    if ($accion == 'insertar' || $accion == 'actualizar') {
        
        $nombre = trim($_POST['nombreProfesor']);
        $email = trim($_POST['emailProfesor']);
        $dni = trim($_POST['dniProfesor']);
        $telefono = trim($_POST['telefonoProfesor']);
        $direccion = trim($_POST['direccionProfesor']);

        $error = false;

        // Validaciones básicas
        if ($nombre == "") {
            $_SESSION['error_nombre'] = "Escribe el nombre del profesor";
            $error = true;
        }
        if ($email == "") {
            $_SESSION['error_email'] = "Escribe el correo";
            $error = true;
        }
        if ($dni == "") {
            $_SESSION['error_dni'] = "Escribe el DNI";
            $error = true;
        }

        if ($error == true) {
            $_SESSION['datos_profesor'] = $_POST;
            if ($accion == 'insertar') {
                header("Location: ../vistas/profesores/agregarProfesores.php");
            } else {
                header("Location: ../vistas/profesores/modificarProfesores.php?id=" . $_POST['idProfesor']);
            }
            exit;
        }

        $datos = [
            'nombre' => $nombre,
            'email' => $email,
            'dni' => $dni,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'idEstado' => 1
        ];

        if ($accion == 'insertar') {
            $modelo->insertarProfesorModelo($datos);
            $_SESSION['exito'] = "Profesor guardado";
        } else {
            $datos['id'] = $_POST['idProfesor'];
            $modelo->actualizarProfesorModelo($datos);
            $_SESSION['exito'] = "Profesor actualizado";
        }

        header("Location: ../vistas/profesores/verProfesores.php");
        exit;
    }

    if ($accion == 'eliminar') {
        $id = $_POST['idProfesor'];
        $modelo->eliminarProfesorModelo($id);
        $_SESSION['exito'] = "Profesor eliminado";
        header("Location: ../vistas/profesores/verProfesores.php");
        exit;
    }
}
?>