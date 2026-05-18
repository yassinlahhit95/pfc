<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['actualizarPerfil'])) {
    $idProfesor = trim($_POST['idProfesor']);
    $nombre = trim($_POST['nombreProfesor']);
    $email = strtolower(trim($_POST['emailProfesor']));
    $telefono = trim($_POST['telefonoProfesor']);

    $passwordActual = trim($_POST['current_password'] ?? '');
    $passwordNueva = trim($_POST['new_password'] ?? '');

    $errores = [];

    if (empty($idProfesor)) {
        header("Location: ../../../vistas/profesores/perfil/ver.php");
        exit;
    }

    if (empty($nombre)) $errores['nombreProfesor'] = "El nombre es obligatorio.";
    if (empty($email)) $errores['emailProfesor'] = "El correo es obligatorio.";
    else if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) $errores['emailProfesor'] = "Formato inválido.";
    if (!empty($telefono) && !is_numeric($telefono)) $errores['telefonoProfesor'] = "Debe ser un número.";

    if (!empty($passwordNueva)) {
        if (empty($passwordActual)) {
            $errores['current_password'] = "Ingresa la contraseña actual.";
        } else {
            $datosProfesor = obtenerProfesorPorId($idProfesor);
            if (!$datosProfesor || !password_verify($passwordActual, $datosProfesor['password'])) {
                $errores['current_password'] = "Contraseña actual incorrecta.";
            } else if (strlen($passwordNueva) < 6) {
                $errores['new_password'] = "Mínimo 6 caracteres.";
            }
        }
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_perfil'] = $_POST;
        header("Location: ../../../vistas/profesores/perfil/editar.php");
        exit;
    }

    if (!empty($passwordNueva)) {
        actualizarPasswordProfesor($idProfesor, $passwordNueva);
    }

    $resultado = actualizarPerfilProfesor($idProfesor, $nombre, $email, $telefono);

    if ($resultado) {
        $_SESSION['exito'] = "Perfil actualizado correctamente.";
        header("Location: ../../../vistas/profesores/perfil/ver.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al actualizar los datos.";
        $_SESSION['datos_perfil'] = $_POST;
        header("Location: ../../../vistas/profesores/perfil/editar.php");
        exit;
    }
}

header("Location: ../../../vistas/profesores/perfil/ver.php");
exit;
?>
