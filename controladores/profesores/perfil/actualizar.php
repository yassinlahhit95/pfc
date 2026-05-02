<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['actualizarPerfil'])) {
    $idProfesorRecibido = trim($_POST['idProfesor']);
    $nombreRecibido = trim($_POST['nombreProfesor']);
    $emailRecibido = strtolower(trim($_POST['emailProfesor']));
    $telefonoRecibido = trim($_POST['telefonoProfesor']);
    
    // Campos de contraseña
    $passwordActualRecibida = trim($_POST['current_password'] ?? '');
    $passwordNuevaRecibida = trim($_POST['new_password'] ?? '');

    // Validaciones básicas de perfil
    if (empty($idProfesorRecibido)) {
        header("Location: ../../../vistas/profesores/perfil/ver.php");
        exit;
    }

    if (empty($nombreRecibido)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        $hayError = true;
    } else if (empty($emailRecibido)) {
        $_SESSION['error'] = "El correo electrónico es obligatorio.";
        $hayError = true;
    } else if (!is_numeric($telefonoRecibido)) {
        $_SESSION['error'] = "El teléfono debe ser un número.";
        $hayError = true;
    }

    // Lógica de cambio de contraseña
    if (!$hayError && !empty($passwordNuevaRecibida)) {
        if (empty($passwordActualRecibida)) {
            $_SESSION['error'] = "Debes ingresar la contraseña actual.";
            $hayError = true;
        } else {
            $datosProfesorActual = obtenerProfesorPorId($idProfesorRecibido);
            
            if ($datosProfesorActual && $datosProfesorActual['password'] === $passwordActualRecibida) {
                actualizarPasswordProfesor($idProfesorRecibido, $passwordNuevaRecibida);
            } else {
                $_SESSION['error'] = "La contraseña actual es incorrecta.";
                $hayError = true;
            }
        }
    }

    // Si no hay errores, actualizamos el resto del perfil
    if (!$hayError) {
        $resultadoActualizacion = actualizarPerfilProfesor($idProfesorRecibido, $nombreRecibido, $emailRecibido, $telefonoRecibido);
        
        if ($resultadoActualizacion) {
            $_SESSION['exito'] = "Perfil actualizado.";
            header("Location: ../../../vistas/profesores/perfil/ver.php");
            exit;
        } else {
            $_SESSION['error'] = "Error al actualizar los datos.";
            $hayError = true;
        }
    }

    header("Location: ../../../vistas/profesores/perfil/editar.php");
    exit;
}

header("Location: ../../../vistas/profesores/perfil/ver.php");
exit;
