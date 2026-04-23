<?php
session_start();
require_once "../../../modelos/directores.php";

if (isset($_POST['actualizarDirector'])) {
    $id_director = $_POST['idDirector'];
    $nombre = trim($_POST['nombreDirector']);
    $email = trim($_POST['emailDirector']);
    $dni = trim($_POST['dniDirector']);
    $telefono = trim($_POST['telefonoDirector']);

    $lista_de_errores = [];

    if (empty($nombre)) {
        $lista_de_errores['nombreDirector'] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $lista_de_errores['emailDirector'] = "El email es obligatorio.";
    } else {
        if (!preg_match('/^[^@]+@[^@]+\.[^@]+$/', $email)) {
            $lista_de_errores['emailDirector'] = "El formato del email no es válido.";
        }
    }
    if (empty($dni)) {
        $lista_de_errores['dniDirector'] = "El DNI es obligatorio.";
    }
    if (empty($telefono)) {
        $lista_de_errores['telefonoDirector'] = "El teléfono es obligatorio.";
    }

    if (empty($lista_de_errores)) {
        $resultado = actualizarDirector($id_director, $nombre, $email, $telefono, $dni);
        if ($resultado) {
            $_SESSION['exito'] = "Director actualizado correctamente.";
            header("Location: /pfc/vistas/admin/directores/verDirectores.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
        }
    } else {
        $_SESSION['errores'] = $lista_de_errores;
        $_SESSION['datos_director'] = $_POST;
    }

    header("Location: /pfc/vistas/admin/directores/modificarDirectores.php?idDirector=$id_director");
    exit;
}

header("Location: /pfc/vistas/admin/directores/verDirectores.php");
exit;
