<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['actualizarPerfil'])) {
    $idEstudianteEnviado = $_POST['idEstudiante'];
    $nombreEnviado = trim($_POST['nombreEstudiante']);
    $emailEnviado = strtolower(trim($_POST['emailEstudiante']));
    $telefonoEnviado = $_POST['telefonoEstudiante'];
    
    // Captura de datos de seguridad
    $passwordActualIngresada = $_POST['current_password'];
    $passwordNuevaIngresada = $_POST['new_password'];

    $hayError = false;

    // Comprobaciones iniciales
    if (empty($idEstudianteEnviado)) {
        header("Location: /pfc/vistas/estudiantes/perfil/ver.php");
        exit;
    }

    if (empty($nombreEnviado)) {
        $_SESSION['error'] = strtoupper("EL NOMBRE NO PUEDE ESTAR VACÍO.");
        $hayError = true;
    } else if (empty($emailEnviado)) {
        $_SESSION['error'] = strtoupper("EL CORREO ELECTRÓNICO NO PUEDE ESTAR VACÍO.");
        $hayError = true;
    }

    // Proceso de cambio de contraseña
    if ($hayError == false && !empty($passwordNuevaIngresada)) {
        if (empty($passwordActualIngresada)) {
            $_SESSION['error'] = strtoupper("POR SEGURIDAD, INGRESE SU CONTRASEÑA ACTUAL.");
            $hayError = true;
        } else {
            // Verificar contra la base de datos
            $datosDelEstudiante = obtenerEstudiantePorId($idEstudianteEnviado);
            
            if ($datosDelEstudiante['password'] == $passwordActualIngresada) {
                // Actualización de clave permitida
                actualizarPasswordEstudiante($idEstudianteEnviado, $passwordNuevaIngresada);
            } else {
                $_SESSION['error'] = strtoupper("LA CONTRASEÑA ACTUAL NO ES CORRECTA.");
                $hayError = true;
            }
        }
    }

    // Si todo va bien, actualizamos los datos básicos
    if ($hayError == false) {
        $seActualizoCorrectamente = actualizarPerfilEstudiante($idEstudianteEnviado, $nombreEnviado, $emailEnviado, $telefonoEnviado);
        
        if ($seActualizoCorrectamente == true) {
            $_SESSION['exito'] = strtoupper("LOS DATOS SE HAN GUARDADO CORRECTAMENTE.");
            header("Location: /pfc/vistas/estudiantes/perfil/ver.php");
            exit;
        } else {
            $_SESSION['error'] = strtoupper("ERROR AL GUARDAR EN LA BASE DE DATOS.");
        }
    }

    header("Location: /pfc/vistas/estudiantes/perfil/editar.php");
    exit;
}

header("Location: /pfc/vistas/estudiantes/perfil/ver.php");
exit;
?>

