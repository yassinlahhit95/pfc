<?php
session_start();
require_once "../../modelos/directores.php";

if (isset($_POST['guardarDirector'])) {
    unset($_SESSION['errores'], $_SESSION['datos_director']);

    $id = $_POST['idDirector'] ?? '';
    $nombre = trim($_POST['nombreDirector'] ?? '');
    $email = trim($_POST['emailDirector'] ?? '');
    $dni = trim($_POST['dniDirector'] ?? '');
    $fechaAlta = trim($_POST['fechaAltaDirector'] ?? '');
    
    $errores = [];

    if (empty($id)) $errores['general'] = "ID del director no válido";
    if (empty($nombre)) $errores['nombreDirector'] = "El nombre es obligatorio";
    if (empty($email)) $errores['emailDirector'] = "El email es obligatorio";
    if (empty($dni)) $errores['dniDirector'] = "El DNI es obligatorio";
    if (empty($fechaAlta)) $errores['fechaAltaDirector'] = "La fecha de alta es obligatoria";

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_director'] = $_POST;
        header("Location: ../../vistas/directores/modificarDirectores.php?id=$id");
        exit;
    }

    if (actualizarDirector($id, $nombre, $email, $dni, $fechaAlta)) {
        $_SESSION['exito'] = "Director actualizado correctamente";
    } else {
        $_SESSION['error'] = "Error al actualizar el director";
    }

    header("Location: ../../vistas/directores/verDirectores.php");
    exit;
}
?>
