<?php
session_start();
require_once __DIR__ . "/../../../modelos/directores.php";

if (isset($_POST['actualizarDirector'])) {
    $idDirector = trim($_POST['idDirector']);
    $nombre = trim($_POST['nombreDirector']);
    $email = trim($_POST['emailDirector']);
    $dni = trim($_POST['dniDirector']);
    $telefono = trim($_POST['telefonoDirector']);

    $directorOriginal = obtenerDirectorPorId($idDirector);
    $fechaAlta = $directorOriginal['fechaAltaDirector'];

    $fechaNacimiento = trim($_POST['fechaNacimientoDirector'] ?? '2000-01-01');
    $direccion = trim($_POST['direccionDirector'] ?? '');
    $ciudad = trim($_POST['ciudadDirector'] ?? '');
    $codigoPostal = trim($_POST['codigoPostalDirector'] ?? '');
    $observaciones = trim($_POST['observacionesDirector'] ?? '');

    $hayError = false;

    $errores_campos = [];
    if (empty($nombre)) {
        $hayError = true;
        $errores_campos['nombreDirector'] = "Nombre obligatorio.";
    } 
    if (empty($email)) {
        $hayError = true;
        $errores_campos['emailDirector'] = "Email obligatorio.";
    } else if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email)) {
        $hayError = true;
        $errores_campos['emailDirector'] = "Email no válido.";
    } 
    if (empty($dni)) {
        $hayError = true;
        $errores_campos['dniDirector'] = "DNI obligatorio.";
    } 
    if (empty($telefono)) {
        $hayError = true;
        $errores_campos['telefonoDirector'] = "Teléfono obligatorio.";
    } else if (!is_numeric($telefono)) {
        $hayError = true;
        $errores_campos['telefonoDirector'] = "Teléfono numérico.";
    }

    // Comprobamos duplicados
    if (!$hayError) {
        if (checkDirectorExistente($dni, $email, $idDirector)) {
            $errores_campos['dniDirector'] = "El DNI o Email ya están registrados por otro director.";
            $hayError = true;
        }
    }

    if (!$hayError) {
        $resultado = actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
        if ($resultado) {
            $_SESSION['exito'] = "Director actualizado.";
            header("Location: ../../../vistas/admin/directores/verDirectores.php");
            exit;
        } else {
            $_SESSION['error'] = "No se pudo actualizar el director.";
        }
    } else {
        $_SESSION['errores'] = $errores_campos;
        $_SESSION['datos_director'] = $_POST;
    }

    header("Location: ../../../vistas/admin/directores/modificarDirectores.php?idDirector=$idDirector");
    exit;
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;


