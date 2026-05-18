<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['actualizarPerfil'])) {
    $idEstudiante = trim($_POST['idEstudiante']);
    $nombre = trim($_POST['nombreEstudiante']);
    $email = strtolower(trim($_POST['emailEstudiante']));
    $telefono = trim($_POST['telefonoEstudiante']);

    $passwordActual = trim($_POST['current_password'] ?? '');
    $passwordNueva = trim($_POST['new_password'] ?? '');

    $errores = [];

    if (empty($idEstudiante)) {
        header("Location: ../../../vistas/estudiantes/perfil/ver.php");
        exit;
    }

    if (empty($nombre)) $errores['nombreEstudiante'] = "El nombre es obligatorio.";
    if (empty($email)) $errores['emailEstudiante'] = "El correo es obligatorio.";
    else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) $errores['emailEstudiante'] = "Formato de correo inválido.";

    if (!empty($passwordNueva)) {
        if (empty($passwordActual)) {
            $errores['current_password'] = "Ingresa tu contraseña actual.";
        } else {
            $datosEstudiante = obtenerEstudiantePorId($idEstudiante);
            if (!password_verify($passwordActual, $datosEstudiante['password'])) {
                $errores['current_password'] = "Contraseña actual incorrecta.";
            } else if (strlen($passwordNueva) < 6) {
                $errores['new_password'] = "Mínimo 6 caracteres.";
            }
        }
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_perfil'] = $_POST;
        header("Location: ../../../vistas/estudiantes/perfil/editar.php");
        exit;
    }

    if (!empty($passwordNueva)) {
        actualizarPasswordEstudiante($idEstudiante, $passwordNueva);
    }

    $resultado = actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono);

    if ($resultado) {
        $_SESSION['exito'] = "Perfil actualizado correctamente.";
        header("Location: ../../../vistas/estudiantes/perfil/ver.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al guardar los datos.";
        $_SESSION['datos_perfil'] = $_POST;
        header("Location: ../../../vistas/estudiantes/perfil/editar.php");
        exit;
    }
}

header("Location: ../../../vistas/estudiantes/perfil/ver.php");
exit;
?>
