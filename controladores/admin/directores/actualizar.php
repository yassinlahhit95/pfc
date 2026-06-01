<?php
session_start();
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../../include/Security.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

if (isset($_POST['actualizarDirector'])) {
    $idDirector = $_POST['idDirector'];
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
    } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $avisos['emailDirector'] = "Email no válido.";
    }
    if (empty($dni)) $avisos['dniDirector'] = "DNI obligatorio.";
    if (empty($telefono)) {
        $avisos['telefonoDirector'] = "El teléfono es obligatorio.";
    } elseif (!preg_match('/^[0-9]{9}$/', $telefono)) {
        $avisos['telefonoDirector'] = "El teléfono debe tener exactamente 9 dígitos.";
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
