<?php
session_start();
require_once __DIR__ . "/../../../modelos/directores.php";

if (isset($_POST['guardarDirector'])) {
    $nombre = trim($_POST['nombreDirector']);
    $email = trim($_POST['emailDirector']);
    $dni = trim($_POST['dniDirector']);
    $telefono = trim($_POST['telefonoDirector']);
    $fechaAlta = date('Y-m-d');
    
    $fechaNacimiento = trim($_POST['fechaNacimientoDirector'] ?? '2000-01-01');
    $direccion = trim($_POST['direccionDirector']);
    $ciudad = trim($_POST['ciudadDirector']);
    $codigoPostal = trim($_POST['codigoPostalDirector']);
    $observaciones = trim($_POST['observacionesDirector']);

    $lista_de_errores = [];

    if (empty($nombre)) {
        $lista_de_errores['nombreDirector'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $lista_de_errores['emailDirector'] = "El email es obligatorio.";
    } else if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email)) {
        $lista_de_errores['emailDirector'] = "El formato del email no es vÃ¡lido.";
    }
    if (empty($dni)) {
        $lista_de_errores['dniDirector'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $lista_de_errores['telefonoDirector'] = "El teléfono es obligatorio.";
    } else if (!is_numeric($telefono)) {
        $lista_de_errores['telefonoDirector'] = "El teléfono debe ser numérico.";
    }

    // Comprobamos duplicados
    if (empty($lista_de_errores)) {
        if (checkDirectorExistente($dni, $email)) {
            $lista_de_errores['dniDirector'] = "El DNI o Email ya están registrados.";
        }
    }

    if (empty($lista_de_errores)) {
        $resultado = insertarDirector($nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
        if ($resultado) {
            $_SESSION['exito'] = "Director registrado correctamente.";
            header("Location: ../../../vistas/admin/directores/verDirectores.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo registrar el director.";
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_director'] = $_POST;
    }

    header("Location: ../../../vistas/admin/directores/agregarDirectores.php");
    exit;
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
?>
