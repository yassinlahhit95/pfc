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

    if (empty($nombre)) {
        $hayError = true;
        $_SESSION['error'] = "Nombre obligatorio.";
    } elseif (empty($email)) {
        $hayError = true;
        $_SESSION['error'] = "Email obligatorio.";
    } elseif (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email)) {
        $hayError = true;
        $_SESSION['error'] = "Email no válido.";
    } elseif (empty($dni)) {
        $hayError = true;
        $_SESSION['error'] = "DNI obligatorio.";
    } elseif (empty($telefono)) {
        $hayError = true;
        $_SESSION['error'] = "Teléfono obligatorio.";
    } elseif (!is_numeric($telefono)) {
        $hayError = true;
        $_SESSION['error'] = "Teléfono numérico.";
    }

    if (!$hayError) {
        $resultado = actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
        if ($resultado) {
            $_SESSION['exito'] = "Director actualizado.";
            header("Location: ../../../vistas/admin/directores/verDirectores.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar.";
        }
    } else {
        $_SESSION['datos_director'] = $_POST;
    }

    header("Location: ../../../vistas/admin/directores/modificarDirectores.php?idDirector=$idDirector");
    exit;
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
?>
