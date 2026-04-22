<?php
session_start();
require_once "../../../modelos/directores.php";
if (isset($_POST['guardarDirector'])) {
    $id = $_POST['idDirector'];
    $nombre = $_POST['nombreDirector'];
    $email = strtolower($_POST['emailDirector']);
    $dni = strtoupper($_POST['dniDirector']);
    $fechaAlta = $_POST['fechaAltaDirector'];
    if (empty($nombre)) {
        $_SESSION['error'] = "Nombre vacio";
    } else if (empty($email)) {
        $_SESSION['error'] = "Email vacio";
    } else if (empty($dni)) {
        $_SESSION['error'] = "DNI vacio";
    } else if (actualizarDirector($id, $nombre, $email, $dni, $fechaAlta)) {
        $_SESSION['exito'] = "OK";
        header("Location: /pfc/vistas/admin/directores/verDirectores.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
    header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?id=$id");
    exit;
}
header("Location: /pfc/vistas/admin/directores/verDirectores.php");
exit;

