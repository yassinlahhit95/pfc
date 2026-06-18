<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/directores.php";

if (isset($_POST['actualizarDirector'])) {
    $idDirector = (int)($_POST['idDirector'] ?? 0);
    $nombre = trim($_POST['nombreDirector']);
    $email = trim($_POST['emailDirector']);
    $dni = trim($_POST['dniDirector']);
    $telefono = trim($_POST['telefonoDirector']);

    $directorOriginal = obtenerDirectorPorId($idDirector);
    $fechaAlta = $directorOriginal['fechaAltaDirector'];

    $fechaNacimiento = trim($_POST['fechaNacimientoDirector'] ?? '2000-01-01');
    $direccion = trim($_POST['direccionDirector']);
    $ciudad = trim($_POST['ciudadDirector']);
    $codigoPostal = trim($_POST['codigoPostalDirector']);
    $observaciones = trim($_POST['observacionesDirector']);

    $avisos = [];
    if (empty($nombre)) $avisos['nombreDirector'] = "Nombre obligatorio.";
    if (empty($email)) {
        $avisos['emailDirector'] = "Email obligatorio.";
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
        $avisos['codigoPostalDirector'] = "El código postal debe ser numérico.";
    }

    if (empty($avisos) && checkDirectorExistente($dni, $email, $idDirector)) {
        $avisos['dniDirector'] = "El DNI o Email ya están registrados por otro director.";
    }

    if (!empty($avisos)) {
        $_SESSION['errores'] = $avisos;
        $_SESSION['datos_director'] = $_POST;
        header("Location: ../../../vistas/admin/directores/modificarDirectores.php?idDirector=$idDirector");
        exit;
    }

    if (actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones)) {
        $_SESSION['exito'] = "Director actualizado.";
        header("Location: ../../../vistas/admin/directores/verDirectores.php");
        exit;
    }
    $_SESSION['errores'] = "No se pudo actualizar el director.";
    header("Location: ../../../vistas/admin/directores/modificarDirectores.php?idDirector=$idDirector");
    exit;
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
?>
