<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/EstudianteGuard.php';
require_once __DIR__ . '/../../../modelos/estudiantes.php';

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarPerfil'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/estudiantes/perfil/editar.php");
        exit;
    }
    $idEstudiante = $_SESSION['idEstudiante'];
    $nombre       = Security::sanitize($_POST['nombreEstudiante']);
    $email        = strtolower(trim($_POST['emailEstudiante']));
    $telefono     = trim($_POST['telefonoEstudiante']);

    $passwordActual = trim($_POST['current_password'] ?? '');
    $passwordNueva  = trim($_POST['new_password'] ?? '');

    $errores = [];

    if (empty($idEstudiante)) {
        header("Location: ../../../vistas/estudiantes/perfil/ver.php");
        exit;
    }

    if (empty($nombre)) $errores['nombreEstudiante'] = "El nombre es un campo obligatorio.";
    if (empty($email)) {
        $errores['emailEstudiante'] = "El correo electrónico es un campo obligatorio.";
    } elseif (!Security::validateEmail($email)) {
        $errores['emailEstudiante'] = "El formato del correo electrónico no es válido.";
    }

    if (!empty($passwordNueva)) {
        if (empty($passwordActual)) {
            $errores['current_password'] = "Debes introducir tu contraseña actual para poder cambiarla.";
        } else {
            $datosEstudiante = obtenerEstudiantePorId($idEstudiante);
            if (!password_verify($passwordActual, $datosEstudiante['password'])) {
                $errores['current_password'] = "La contraseña actual introducida es incorrecta.";
            } elseif (strlen($passwordNueva) < 6) {
                $errores['new_password'] = "La nueva contraseña debe tener al menos 6 caracteres.";
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
