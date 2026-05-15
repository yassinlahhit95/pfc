<?php
session_start();
require_once __DIR__ . "/../../../modelos/directores.php";

if (isset($_POST['guardarDirector'])) {
    $nombre = trim($_POST['nombreDirector']);
    $email = trim($_POST['emailDirector']);
    $dni = trim($_POST['dniDirector']);
    $telefono = trim($_POST['telefonoDirector']);
    $fechaAlta = date('Y-m-d');
    
    $fechaNacimiento = trim($_POST['fechaNacimientoDirector'] ?? '1995-08-12');
    $direccion = trim($_POST['direccionDirector']);
    $ciudad = trim($_POST['ciudadDirector']);
    $codigoPostal = trim($_POST['codigoPostalDirector']);
    $observaciones = trim($_POST['observacionesDirector']);

    $errores = [];

    if (empty($nombre)) {
        $errores['nombreDirector'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $errores['emailDirector'] = "El email es obligatorio.";
    } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errores['emailDirector'] = "El formato del email no es válido.";
    }
    if (empty($dni)) {
        $errores['dniDirector'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $errores['telefonoDirector'] = "El teléfono es obligatorio.";
    } elseif (!preg_match('/^[0-9]{9}$/', $telefono)) {
        $errores['telefonoDirector'] = "El teléfono debe tener exactamente 9 dígitos.";
    }
    if (!empty($codigoPostal) && !is_numeric($codigoPostal)) {
        $errores['codigoPostalDirector'] = "El código postal debe ser numérico.";
    }

    if (empty($errores)) {
        if (checkDirectorExistente($dni, $email)) {
            $errores['dniDirector'] = "El DNI o Email ya están registrados.";
        }
    }

    if (empty($errores)) {
        $resultado = insertarDirector($nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
        if ($resultado) {
            $_SESSION['exito'] = "Director registrado correctamente.";
            header("Location: ../../../vistas/admin/directores/verDirectores.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo registrar el director.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_director'] = $_POST;
    }

    header("Location: ../../../vistas/admin/directores/agregarDirectores.php");
    exit;
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
?>
