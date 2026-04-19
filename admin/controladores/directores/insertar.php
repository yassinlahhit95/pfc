<?php
session_start();
require_once "../../modelos/directores.php";

if (isset($_POST['guardarDirector'])) {
    unset($_SESSION['errores'], $_SESSION['datos_director']);

    $nombre = trim($_POST['nombreDirector'] ?? '');
    $email = trim($_POST['emailDirector'] ?? '');
    $dni = trim($_POST['dniDirector'] ?? '');
    $fechaAlta = trim($_POST['fechaAltaDirector'] ?? '');
    $idEstado = $_POST['idEstado'] ?? 1;
    
    $errores = [];

    if (empty($nombre)) $errores['nombreDirector'] = "El nombre es obligatorio";
    if (empty($email)) $errores['emailDirector'] = "El email es obligatorio";
    if (empty($dni)) $errores['dniDirector'] = "El DNI es obligatorio";
    if (empty($fechaAlta)) $errores['fechaAltaDirector'] = "La fecha de alta es obligatoria";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_director'] = $_POST;
        header("Location: ../../vistas/directores/agregarDirectores.php");
        exit;
    }

    if (insertarDirector($nombre, $email, $dni, $fechaAlta, $idEstado)) {
        $_SESSION['exito'] = "Director creado correctamente";
    } else {
        $_SESSION['error'] = "Error al crear el director";
    }

    header("Location: ../../vistas/directores/verDirectores.php");
    exit;
}
?>
