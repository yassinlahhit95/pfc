<?php
session_start();
require_once __DIR__ . "/../../../modelos/directores.php";

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

    $errores = [];
    if (empty($nombre)) {
        $errores['nombreDirector'] = "Nombre obligatorio.";
    }
    if (empty($email)) {
        $errores['emailDirector'] = "Email obligatorio.";
    } else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $errores['emailDirector'] = "Email no válido.";
    }
    if (empty($dni)) {
        $errores['dniDirector'] = "DNI obligatorio.";
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
        if (checkDirectorExistente($dni, $email, $idDirector)) {
            $errores['dniDirector'] = "El DNI o Email ya están registrados por otro director.";
        }
    }

    if (empty($errores)) {
        $resultado = actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
        if ($resultado) {
            $_SESSION['exito'] = "Director actualizado.";
            header("Location: ../../../vistas/admin/directores/verDirectores.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo actualizar el director.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_director'] = $_POST;
    }

    header("Location: ../../../vistas/admin/directores/modificarDirectores.php?idDirector=$idDirector");
    exit;
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
?>
