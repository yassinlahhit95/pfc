<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once "../../../modelos/estudiantes.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarPerfil'])) {
    $idEstudiante = $_SESSION['idEstudiante'];
    $nombre       = Security::sanitize($_POST['nombreEstudiante']);
    $email        = strtolower(trim($_POST['emailEstudiante']));
    $telefono     = trim($_POST['telefonoEstudiante']);

    $passwordActual = trim($_POST['current_password'] ?? '');
    $passwordNueva  = trim($_POST['new_password'] ?? '');

    $errores = '';

    if (empty($idEstudiante)) {
        header("Location: ../../../vistas/estudiantes/perfil/ver.php");
        exit;
    }

    if (empty($nombre)) $errores = "El nombre es un campo obligatorio.";
    if (empty($email)) $errores = "El correo electrónico es un campo obligatorio.";
    elseif (!Security::validateEmail($email)) $errores = "El formato del correo electrónico no es válido.";

    if (!empty($passwordNueva)) {
        if (empty($passwordActual)) {
            $errores = "Debes introducir tu contraseña actual para poder cambiarla.";
        } else {
            $datosEstudiante = obtenerEstudiantePorId($idEstudiante);
            if (!password_verify($passwordActual, $datosEstudiante['password'])) {
                $errores = "La contraseña actual introducida es incorrecta.";
            } elseif (strlen($passwordNueva) < 6) {
                $errores = "La nueva contraseña debe tener al menos 6 caracteres.";
            }
        }
    }

    if ($errores) {
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
        $_SESSION['exito'] = "El perfil ha sido actualizado correctamente.";
        header("Location: ../../../vistas/estudiantes/perfil/ver.php");
        exit;
    } else {
        $_SESSION['errores'] = "No se pudieron guardar los cambios del perfil.";
        $_SESSION['datos_perfil'] = $_POST;
        header("Location: ../../../vistas/estudiantes/perfil/editar.php");
        exit;
    }
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/estudiantes/perfil/ver.php");
exit;
