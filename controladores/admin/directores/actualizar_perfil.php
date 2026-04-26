<?php
session_start();
require_once "../../../modelos/directores.php";

if (isset($_POST['actualizarPerfilBtn'])) {
    $idDirector = $_POST['idDirector'];
    $nombre = trim($_POST['nombreDirector']);
    $email = strtolower(trim($_POST['emailDirector']));
    $tel = $_POST['telefonoDirector'];
    
    // Contraseñas
    $passActual = $_POST['current_password'];
    $passNueva = $_POST['new_password'];

    $hayError = false;

    if (empty($idDirector)) {
        header("Location: /pfc/vistas/admin/dashboard.php");
        exit;
    }

    if (empty($nombre)) {
        $_SESSION['error'] = "Oye, el nombre no puede estar vacio.";
        $hayError = true;
    } else if (empty($email)) {
        $_SESSION['error'] = "Falta el correo electronico, es obligatorio.";
        $hayError = true;
    }

    // Cambiar la contraseña si han puesto algo
    if ($hayError == false && !empty($passNueva)) {
        if (empty($passActual)) {
            $_SESSION['error'] = "Para cambiar la clave tienes que poner la antigua por seguridad.";
            $hayError = true;
        } else {
            // Miramos si la actual es correcta
            $datos = obtenerDirectorPorId($idDirector);
            
            if ($datos['password'] == $passActual) {
                actualizarPasswordDirector($idDirector, $passNueva);
            } else {
                $_SESSION['error'] = "La contraseña actual no es correcta, revisala.";
                $hayError = true;
            }
        }
    }

    // Si no hay fallos, guardamos
    if ($hayError == false) {
        $ok = actualizarPerfilDirector($idDirector, $nombre, $email, $tel);
        
        if ($ok == true) {
            $_SESSION['exito'] = "Genial! Tus datos se han actualizado correctamente.";
            header("Location: /pfc/vistas/admin/directores/perfil.php");
            exit;
        } else {
            $_SESSION['error'] = "Error raro al guardar en la base de datos...";
        }
    }

    header("Location: /pfc/vistas/admin/directores/perfil.php");
    exit;
}

header("Location: /pfc/vistas/admin/dashboard.php");
exit;
?>
