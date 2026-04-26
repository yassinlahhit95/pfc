<?php
session_start();
require_once "../../../modelos/directores.php";

if (isset($_POST['actualizarPerfilBtn'])) {
    $idDirectorRecibido = $_POST['idDirector'];
    $nombreRecibido = trim($_POST['nombreDirector']);
    $emailRecibido = strtolower(trim($_POST['emailDirector']));
    $telefonoRecibido = $_POST['telefonoDirector'];
    
    // Contraseñas
    $passwordActual = $_POST['current_password'];
    $passwordNueva = $_POST['new_password'];

    $hayError = false;

    if (empty($idDirectorRecibido)) {
        header("Location: /pfc/vistas/admin/dashboard.php");
        exit;
    }

    if (empty($nombreRecibido)) {
        $_SESSION['error'] = strtoupper("EL NOMBRE ES OBLIGATORIO.");
        $hayError = true;
    } else if (empty($emailRecibido)) {
        $_SESSION['error'] = strtoupper("EL EMAIL ES OBLIGATORIO.");
        $hayError = true;
    }

    // Lógica para cambiar la clave
    if ($hayError == false && !empty($passwordNueva)) {
        if (empty($passwordActual)) {
            $_SESSION['error'] = strtoupper("PARA CAMBIAR LA CLAVE DEBE PONER LA ACTUAL.");
            $hayError = true;
        } else {
            // Verificar clave actual
            $datosAdmin = obtenerDirectorPorId($idDirectorRecibido);
            
            if ($datosAdmin['password'] == $passwordActual) {
                actualizarPasswordDirector($idDirectorRecibido, $passwordNueva);
            } else {
                $_SESSION['error'] = strtoupper("LA CONTRASEÑA ACTUAL NO COINCIDE.");
                $hayError = true;
            }
        }
    }

    // Guardar cambios del perfil
    if ($hayError == false) {
        $seGuardo = actualizarPerfilDirector($idDirectorRecibido, $nombreRecibido, $emailRecibido, $telefonoRecibido);
        
        if ($seGuardo == true) {
            $_SESSION['exito'] = strtoupper("MIS DATOS SE HAN ACTUALIZADO.");
            header("Location: /pfc/vistas/admin/directores/perfil.php");
            exit;
        } else {
            $_SESSION['error'] = strtoupper("ERROR AL GUARDAR EN LA BASE DE DATOS.");
        }
    }

    header("Location: /pfc/vistas/admin/directores/perfil.php");
    exit;
}

header("Location: /pfc/vistas/admin/dashboard.php");
exit;
?>
