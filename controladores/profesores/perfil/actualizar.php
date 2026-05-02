<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['actualizarPerfil'])) {
    $idProfesorRecibido = trim($_POST['idProfesor']);
    $nombreRecibido = trim($_POST['nombreProfesor']);
    $emailRecibido = strtolower(trim($_POST['emailProfesor']));
    $telefonoRecibido = trim($_POST['telefonoProfesor']);
    
    // Campos de contraseÃ±a
    $passwordActualRecibida = trim($_POST['current_password'] ?? '');
    $passwordNuevaRecibida = trim($_POST['new_password'] ?? '');

    // Validaciones bÃ¡sicas de perfil
    if (empty($idProfesorRecibido)) {
        header("Location: ../../../vistas/profesores/perfil/ver.php");
        exit;
    }

    if (empty($nombreRecibido)) {
        $_SESSION['error'] = "Vaya, el nombre no puede estar vacÃ­o.";
        $hayError = true;
    } else if (empty($emailRecibido)) {
        $_SESSION['error'] = "Vaya, el correo electrÃ³nico no puede estar vacÃ­o.";
        $hayError = true;
    } else if (!is_numeric($telefonoRecibido)) {
        $_SESSION['error'] = "Vaya, el telÃ©fono debe ser un nÃºmero.";
        $hayError = true;
    }

    // LÃ³gica de cambio de contraseÃ±a
    if (!$hayError && !empty($passwordNuevaRecibida)) {
        if (empty($passwordActualRecibida)) {
            $_SESSION['error'] = "Vaya, debes ingresar la contraseÃ±a actual para validar el cambio.";
            $hayError = true;
        } else {
            $datosProfesorActual = obtenerProfesorPorId($idProfesorRecibido);
            
            if ($datosProfesorActual && $datosProfesorActual['password'] === $passwordActualRecibida) {
                actualizarPasswordProfesor($idProfesorRecibido, $passwordNuevaRecibida);
            } else {
                $_SESSION['error'] = "Vaya, la contraseÃ±a actual es incorrecta.";
                $hayError = true;
            }
        }
    }

    // Si no hay errores, actualizamos el resto del perfil
    if (!$hayError) {
        $resultadoActualizacion = actualizarPerfilProfesor($idProfesorRecibido, $nombreRecibido, $emailRecibido, $telefonoRecibido);
        
        if ($resultadoActualizacion) {
            $_SESSION['exito'] = "Listo! Perfil actualizado con Ã©xito.";
            header("Location: ../../../vistas/profesores/perfil/ver.php");
            exit;
        } else {
            $_SESSION['error'] = "Vaya, no se pudieron actualizar los datos.";
            $hayError = true;
        }
    }

    header("Location: ../../../vistas/profesores/perfil/editar.php");
    exit;
}

header("Location: ../../../vistas/profesores/perfil/ver.php");
exit;
