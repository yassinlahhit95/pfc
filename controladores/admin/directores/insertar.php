<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
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

    $avisos = [];
    if (empty($nombre)) $avisos['nombreDirector'] = "Falta el nombre";
    if (empty($email)) {
        $avisos['emailDirector'] = "El email es obligatorio.";
    } elseif (!Security::validateEmail($email)) {
        $avisos['emailDirector'] = "El formato del email no es válido.";
    }
    if (empty($dni)) $avisos['dniDirector'] = "El DNI es obligatorio.";
    if (empty($telefono)) {
        $avisos['telefonoDirector'] = "El teléfono es obligatorio.";
    } elseif (!Security::validatePhone($telefono)) {
        $avisos['telefonoDirector'] = "El teléfono debe tener 9 dígitos y comenzar por 6, 7, 8 o 9.";
    }
    if (!empty($codigoPostal) && !is_numeric($codigoPostal)) {
        $avisos['codigoPostalDirector'] = "Código postal no válido";
    }

    if (empty($avisos) && checkDirectorExistente($dni, $email)) {
        $avisos['dniDirector'] = "El DNI o Email ya están registrados.";
    }

    if (!empty($avisos)) {
        $_SESSION['errores'] = $avisos;
        $_SESSION['datos_director'] = $_POST;
        header("Location: ../../../vistas/admin/directores/agregarDirectores.php");
        exit;
    }

    if (insertarDirector($nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones)) {
        $_SESSION['exito'] = mensajeExitoConCredenciales("Director registrado correctamente.");
        header("Location: ../../../vistas/admin/directores/verDirectores.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudo registrar el director.";
    header("Location: ../../../vistas/admin/directores/agregarDirectores.php");
    exit;
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
?>
