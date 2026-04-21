<?php
session_start();
require_once "../../../modelos/directores.php";

if (isset($_POST['guardarDirector'])) {
    $id = $_POST['idDirector'];
    $nombre = trim($_POST['nombreDirector']);
    $email = trim($_POST['emailDirector']);
    $dni = trim($_POST['dniDirector']);
    $fechaAlta = trim($_POST['fechaAltaDirector']);

    // Regex
    $regexEmail = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $regexFecha = "/^\d{4}-\d{2}-\d{2}$/";

    if (empty($id)) {
        header("Location: /pfc/vistas/admin/directores/verDirectores.php");
    } else if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?id=$id");
    } else if (empty($email)) {
        $_SESSION['error'] = "El email es obligatorio.";
        header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?id=$id");
    } else if (!preg_match($regexEmail, $email)) {
        $_SESSION['error'] = "El formato del email no es válido.";
        header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?id=$id");
    } else if (empty($dni)) {
        $_SESSION['error'] = "El DNI es obligatorio.";
        header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?id=$id");
    } else if (empty($fechaAlta)) {
        $_SESSION['error'] = "La fecha de alta es obligatoria.";
        header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?id=$id");
    } else if (!preg_match($regexFecha, $fechaAlta)) {
        $_SESSION['error'] = "La fecha no es válida.";
        header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?id=$id");
    } else {
        if (actualizarDirector($id, $nombre, $email, $dni, $fechaAlta)) {
            $_SESSION['exito'] = "Director actualizado correctamente";
            header("Location: /pfc/vistas/admin/directores/verDirectores.php");
        } else {
            $_SESSION['error'] = "Error al actualizar el director";
            header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?id=$id");
        }
    }
    exit;
}

header("Location: /pfc/vistas/admin/directores/verDirectores.php");
exit;
?>