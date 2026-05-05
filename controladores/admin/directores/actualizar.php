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
        require_once __DIR__ . "/../../../modelos/conectar.php";
        $con = obtenerConexion();
        
        $sqlDni = "SELECT idDirector FROM directores WHERE dniDirector = '" . mysqli_real_escape_string($con, $dni) . "' AND idDirector != $idDirector";
        $resDni = mysqli_query($con, $sqlDni);
        if (mysqli_num_rows($resDni) > 0) {
            $errores_campos['dniDirector'] = "Este DNI ya está registrado por otro director.";
            $hayError = true;
        }

        $sqlEmail = "SELECT idDirector FROM directores WHERE emailDirector = '" . mysqli_real_escape_string($con, $email) . "' AND idDirector != $idDirector";
        $resEmail = mysqli_query($con, $sqlEmail);
        if (mysqli_num_rows($resEmail) > 0) {
            $errores_campos['emailDirector'] = "Este Email ya está registrado por otro director.";
            $hayError = true;
        }
        mysqli_close($con);
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
