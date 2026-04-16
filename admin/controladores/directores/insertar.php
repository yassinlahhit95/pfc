<?php
session_start();
require_once "../../modelos/directores.php";

if (isset($_POST['guardarDirector'])) {
    unset($_SESSION['errores']);
    unset($_SESSION['datos_director']);

    $nombre = trim($_POST['nombreDirector'] ?? '');
    $email = trim($_POST['emailDirector'] ?? '');
    $dni = trim($_POST['dniDirector'] ?? '');
    $telefono = trim($_POST['telefonoDirector'] ?? '');
    $direccion = trim($_POST['direccionDirector'] ?? '');
    $ciudad = trim($_POST['ciudadDirector'] ?? '');
    $cp = trim($_POST['codigoPostalDirector'] ?? '');
    $fechaAlta = trim($_POST['fechaAltaDirector'] ?? '');
    $idEstado = $_POST['idEstado'] ?? 1;
    
    $errores = [];

    if (empty($nombre)) $errores['nombreDirector'] = "El nombre es obligatorio";
    if (empty($email)) $errores['emailDirector'] = "El email es obligatorio";
    if (empty($dni)) $errores['dniDirector'] = "El DNI es obligatorio";
    if (empty($telefono)) $errores['telefonoDirector'] = "El teléfono es obligatorio";
    if (empty($direccion)) $errores['direccionDirector'] = "La dirección es obligatoria";
    if (empty($ciudad)) $errores['ciudadDirector'] = "La ciudad es obligatoria";
    if (empty($cp)) $errores['codigoPostalDirector'] = "El CP es obligatorio";
    if (empty($fechaAlta)) $errores['fechaAltaDirector'] = "La fecha de alta es obligatoria";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_director'] = $_POST;
        header("Location: ../../vistas/directores/agregarDirectores.php");
        exit;
    }

    $modelo = new director();
    if ($modelo->insertarDirectoresModelo($nombre, $email, $ciudad, $cp, $direccion, $telefono, $dni, $fechaAlta, $idEstado)) {
        $_SESSION['exito'] = "Director creado correctamente";
    } else {
        $_SESSION['error'] = "Error al crear el director";
    }

    header("Location: ../../vistas/directores/verDirectores.php");
    exit;
}
?>
