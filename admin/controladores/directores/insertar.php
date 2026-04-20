<?php
session_start();
require_once "../../../modelos/directores.php";

if (isset($_POST['guardarDirector'])) {
    $nombre = trim($_POST['nombreDirector']);
    $email = trim($_POST['emailDirector']);
    $dni = trim($_POST['dniDirector']);
    $fechaAlta = trim($_POST['fechaAltaDirector']);

    // Regex
    $regexEmail = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/";

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        header("Location: ../../vistas/directores/agregarDirectores.php");
    } else if (empty($email)) {
        $_SESSION['error'] = "El email es obligatorio.";
        header("Location: ../../vistas/directores/agregarDirectores.php");
    } else if (!preg_match($regexEmail, $email)) {
        $_SESSION['error'] = "El formato del email no es válido.";
        header("Location: ../../vistas/directores/agregarDirectores.php");
    } else if (empty($dni)) {
        $_SESSION['error'] = "El DNI es obligatorio.";
        header("Location: ../../vistas/directores/agregarDirectores.php");
    } else if (empty($fechaAlta)) {
        $_SESSION['error'] = "La fecha de alta es obligatoria.";
        header("Location: ../../vistas/directores/agregarDirectores.php");
    } else if (!preg_match($regexFecha, $fechaAlta)) {
        $_SESSION['error'] = "La fecha debe tener formato YYYY-MM-DD.";
        header("Location: ../../vistas/directores/agregarDirectores.php");
    } else {
        if (insertarDirector($nombre, $email, $dni, $fechaAlta)) {
            $_SESSION['exito'] = "Director creado correctamente";
            header("Location: ../../vistas/directores/verDirectores.php");
        } else {
            $_SESSION['error'] = "Error al crear el director";
            header("Location: ../../vistas/directores/agregarDirectores.php");
        }
    }
    exit;
}

header("Location: ../../vistas/directores/verDirectores.php");
exit;
?>