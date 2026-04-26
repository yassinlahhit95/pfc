<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['actualizarPerfil'])) {
    $idProfesorRecibido = $_POST['idProfesor'];
    $nombreRecibido = trim($_POST['nombreProfesor']);
    $emailRecibido = strtolower(trim($_POST['emailProfesor']));
    $telefonoRecibido = $_POST['telefonoProfesor'];
    
    // Campos de contraseña
    $passwordActualRecibida = $_POST['current_password'];
    $passwordNuevaRecibida = $_POST['new_password'];

    $errorDetectado = false;

    // Validaciones básicas de perfil
    if (empty($idProfesorRecibido)) {
        header("Location: /pfc/vistas/profesores/perfil/ver.php");
        exit;
    }

    if (empty($nombreRecibido)) {
        $_SESSION['error'] = strtoupper("EL NOMBRE NO PUEDE ESTAR VACÍO.");
        $errorDetectado = true;
    } else if (empty($emailRecibido)) {
        $_SESSION['error'] = strtoupper("EL CORREO ELECTRÓNICO NO PUEDE ESTAR VACÍO.");
        $errorDetectado = true;
    } else if (!is_numeric($telefonoRecibido)) {
        $_SESSION['error'] = strtoupper("EL TELÉFONO DEBE SER UN NÚMERO.");
        $errorDetectado = true;
    }

    // Lógica de cambio de contraseña (si se proporcionan datos)
    if ($errorDetectado == false && !empty($passwordNuevaRecibida)) {
        if (empty($passwordActualRecibida)) {
            $_SESSION['error'] = strtoupper("DEBE INGRESAR LA CONTRASEÑA ACTUAL PARA VALIDAR EL CAMBIO.");
            $errorDetectado = true;
        } else {
            // Obtener datos actuales para verificar la clave
            $datosProfesorActual = obtenerProfesorPorId($idProfesorRecibido);
            
            if ($datosProfesorActual['password'] == $passwordActualRecibida) {
                // La clave actual es correcta, procedemos a actualizarla
                actualizarPasswordProfesor($idProfesorRecibido, $passwordNuevaRecibida);
            } else {
                $_SESSION['error'] = strtoupper("LA CONTRASEÑA ACTUAL ES INCORRECTA.");
                $errorDetectado = true;
            }
        }
    }

    // Si no hay errores, actualizamos el resto del perfil
    if ($errorDetectado == false) {
        $resultadoActualizacion = actualizarPerfilProfesor($idProfesorRecibido, $nombreRecibido, $emailRecibido, $telefonoRecibido);
        
        if ($resultadoActualizacion == true) {
            $_SESSION['exito'] = strtoupper("PERFIL ACTUALIZADO CON ÉXITO.");
            header("Location: /pfc/vistas/profesores/perfil/ver.php");
            exit;
        } else {
            $_SESSION['error'] = strtoupper("ERROR AL ACTUALIZAR LOS DATOS EN EL SISTEMA.");
        }
    }

    header("Location: /pfc/vistas/profesores/perfil/editar.php");
    exit;
}

header("Location: /pfc/vistas/profesores/perfil/ver.php");
exit;
?>
