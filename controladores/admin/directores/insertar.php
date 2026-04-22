<?php
session_start();
require_once "../../../modelos/directores.php";

if (isset($_POST['guardarDirector'])) {
    $nombreDirector = trim($_POST['nombreDirector']);
    $emailDirector = strtolower(trim($_POST['emailDirector']));
    $dniDirector = strtoupper(trim($_POST['dniDirector']));
    $telefonoDirector = trim($_POST['telefonoDirector']);
    $fechaAlta = $_POST['fechaAltaDirector'];

    if (empty($nombreDirector)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
    } else if (empty($emailDirector)) {
        $_SESSION['error'] = "El email es obligatorio.";
    } else if (empty($dniDirector)) {
        $_SESSION['error'] = "El DNI es obligatorio.";
    } else if (!is_numeric($telefonoDirector) && !empty($telefonoDirector)) {
        $_SESSION['error'] = "El teléfono debe ser numérico.";
    } else if (insertarDirector($nombreDirector, $emailDirector, $dniDirector, $telefonoDirector, $fechaAlta)) {
        $_SESSION['exito'] = "Director registrado con éxito.";
        header("Location: /pfc/vistas/admin/directores/verDirectores.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al guardar.";
    }
    header("Location: /pfc/vistas/admin/directores/agregarDirectores.php");
    exit;
}

header("Location: /pfc/vistas/admin/directores/verDirectores.php");
exit;
?>