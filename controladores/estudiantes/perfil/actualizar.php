<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['actualizarPerfil'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    $nombre = trim($_POST['nombreEstudiante']);
    $email = strtolower(trim($_POST['emailEstudiante']));
    $telefono = trim($_POST['telefonoEstudiante']);
    
    // Captura de datos de seguridad
    $passwordActual = $_POST['current_password'];
    $passwordNueva = $_POST['new_password'];

    $hayError = false;

    if (empty($idEstudiante)) {
        header("Location: ../../../vistas/estudiantes/perfil/ver.php");
        exit;
    }

    if (empty($nombre) || empty($email)) {
        $_SESSION['error'] = "Vaya, el nombre y el correo electrÃ³nico son obligatorios.";
        $hayError = true;
    }

    // Proceso de cambio de contraseÃ±a
    if (!$hayError && !empty($passwordNueva)) {
        if (empty($passwordActual)) {
            $_SESSION['error'] = "Vaya, por seguridad debes ingresar tu contraseÃ±a actual.";
            $hayError = true;
        } else {
            $datosEstudiante = obtenerEstudiantePorId($idEstudiante);
            
            if ($datosEstudiante['password'] == $passwordActual) {
                actualizarPasswordEstudiante($idEstudiante, $passwordNueva);
            } else {
                $_SESSION['error'] = "Vaya, la contraseÃ±a actual no es correcta.";
                $hayError = true;
            }
        }
    }

    if (!$hayError) {
        $resultado = actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono);
        
        if ($resultado) {
            $_SESSION['exito'] = "Listo! Los datos se han guardado correctamente.";
            header("Location: ../../../vistas/estudiantes/perfil/ver.php");
            exit;
        } else {
            $_SESSION['error'] = "Vaya, ha habido un error al guardar los datos.";
        }
    }

    header("Location: ../../../vistas/estudiantes/perfil/editar.php");
    exit;
}

header("Location: ../../../vistas/estudiantes/perfil/ver.php");
exit;
?>