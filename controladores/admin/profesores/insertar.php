<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['guardarProfesor'])) {
    $nombre = trim($_POST['nombreProfesor']);
    $email = trim($_POST['emailProfesor']);
    $dni = trim($_POST['dniProfesor']);
    $telefono = trim($_POST['telefonoProfesor']);
    $direccion = trim($_POST['direccionProfesor']);
    
    $especialidad = trim($_POST['especialidad'] ?? '');
    $fechaNacimiento = $_POST['fechaNacimientoProfesor'] ?? '1980-01-01';
    $fechaAlta = date('Y-m-d');
    $ciudad = trim($_POST['ciudadProfesor'] ?? '');
    $codigoPostal = trim($_POST['codigoPostalProfesor'] ?? '');
    $observaciones = trim($_POST['observacionesProfesor'] ?? '');

    $lista_de_errores = array();

    if (empty($nombre)) {
        $lista_de_errores['nombreProfesor'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $lista_de_errores['emailProfesor'] = "El email es obligatorio.";
    } else if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email)) {
        $lista_de_errores['emailProfesor'] = "El formato del email no es válido.";
    }
    if (empty($dni)) {
        $lista_de_errores['dniProfesor'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $lista_de_errores['telefonoProfesor'] = "El teléfono es obligatorio.";
    } else if (!is_numeric($telefono)) {
        $lista_de_errores['telefonoProfesor'] = "El teléfono debe ser numérico.";
    }
    if (empty($direccion)) {
        $lista_de_errores['direccionProfesor'] = "La dirección es obligatoria.";
    }

    if (empty($lista_de_errores)) {
        // Signature: insertarProfesor($nombre, $email, $telefono, $dni, $direccion, $especialidad, $fechaNacimiento, $fechaAlta, $ciudad, $codigoPostal, $observaciones)
        $resultado = insertarProfesor($nombre, $email, $telefono, $dni, $direccion, $especialidad, $fechaNacimiento, $fechaAlta, $ciudad, $codigoPostal, $observaciones);
        if ($resultado) {
            $_SESSION['exito'] = "Profesor registrado correctamente.";
            header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al guardar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_profesor'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/profesores/agregarProfesores.php");
    exit;
}

header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
exit;
?>