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
        $_SESSION['error'] = "El nombre es obligatorio.";
    } elseif (empty($email)) {
        $hayError = true;
        $_SESSION['error'] = "El email es obligatorio.";
    } elseif (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email)) {
        $hayError = true;
        $_SESSION['error'] = "El formato del email no es válido.";
    } elseif (empty($dni)) {
        $hayError = true;
        $_SESSION['error'] = "El DNI es obligatorio.";
    } elseif (empty($telefono)) {
        $hayError = true;
        $_SESSION['error'] = "El teléfono es obligatorio.";
    } elseif (!is_numeric($telefono)) {
        $hayError = true;
        $_SESSION['error'] = "El teléfono debe ser numérico.";
    }

    if (!$hayError) {
        $resultado = actualizarDirector($idDirector, $nombre, $email, $dni, $telefono, $fechaAlta, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $observaciones);
        if ($resultado) {
            $_SESSION['exito'] = "Director actualizado correctamente.";
            header("Location: ../../../vistas/admin/directores/verDirectores.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
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
