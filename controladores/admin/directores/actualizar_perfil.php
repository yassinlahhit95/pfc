<?php
session_start();
require_once __DIR__ . "/../../../modelos/directores.php";

if (isset($_POST['actualizarPerfilBtn'])) {
    $idDirector = trim($_POST['idDirector']);
    $nombre = trim($_POST['nombreDirector']);
    $email = strtolower(trim($_POST['emailDirector']));
    $telefonoDirector = trim($_POST['telefonoDirector']);

    $passwordActual = trim($_POST['current_password']);
    $passwordNueva = trim($_POST['new_password']);

    $hayError = false;

    if (empty($idDirector)) {
        header("Location: ../../../vistas/admin/inicio/dashboard.php");
        exit;
    }

    if (empty($nombre)) {
        $_SESSION['error'] = "El nombre no puede estar vacío.";
        $hayError = true;
    } else if (empty($email)) {
        $_SESSION['error'] = "El correo electrónico es obligatorio.";
        $hayError = true;
    }

    if (!$hayError && !empty($passwordNueva)) {
        if (empty($passwordActual)) {
            $_SESSION['error'] = "Debe introducir la contraseña antigua para cambiarla.";
            $hayError = true;
        } else {
            $datos = obtenerDirectorPorId($idDirector);

            if ($datos['password'] == $passwordActual) {
                actualizarPasswordDirector($idDirector, $passwordNueva);
            } else {
                $_SESSION['error'] = "La contraseña actual no es correcta.";
                $hayError = true;
            }
        }
    }

    if (!$hayError) {
        $resultadoActualizacion = actualizarPerfilDirector($idDirector, $nombre, $email, $telefonoDirector);

        if ($resultadoActualizacion) {
            $_SESSION['exito'] = "Datos actualizados correctamente.";
            header("Location: ../../../vistas/admin/directores/perfil.php");
            exit;
        }
        $_SESSION['error'] = "Error al guardar en la base de datos.";
    }

    header("Location: ../../../vistas/admin/directores/perfil.php");
    exit;
}

header("Location: ../../../vistas/admin/inicio/dashboard.php");
exit;


