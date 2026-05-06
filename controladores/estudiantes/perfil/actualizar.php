<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['actualizarPerfil'])) {
    $idEst = trim($_POST['idEstudiante']);
    $nom = trim($_POST['nombreEstudiante']);
    $eml = strtolower(trim($_POST['emailEstudiante']));
    $tel = trim($_POST['telefonoEstudiante']);

    $pwdAct = $_POST['current_password'];
    $pwdNva = $_POST['new_password'];

    $errs = [];

    if (empty($idEst)) {
        header("Location: ../../../vistas/estudiantes/perfil/ver.php");
        exit;
    }

    if (empty($nom)) $errs['nombreEstudiante'] = "El nombre es obligatorio.";
    if (empty($eml)) $errs['emailEstudiante'] = "El correo es obligatorio.";
    else if (!filter_var($eml, FILTER_VALIDATE_EMAIL)) $errs['emailEstudiante'] = "Formato de correo inválido.";

    if (!empty($pwdNva)) {
        if (empty($pwdAct)) {
            $errs['current_password'] = "Ingresa tu contraseña actual.";
        } else {
            $est = obtenerEstudiantePorId($idEst);
            if ($est['password'] !== $pwdAct) {
                $errs['current_password'] = "Contraseña actual incorrecta.";
            } else if (strlen($pwdNva) < 6) {
                $errs['new_password'] = "Mínimo 6 caracteres.";
            }
        }
    }

    if (!empty($errs)) {
        $_SESSION['errores'] = $errs;
        $_SESSION['datos_perfil'] = $_POST;
        header("Location: ../../../vistas/estudiantes/perfil/editar.php");
        exit;
    }

    if (!empty($pwdNva)) {
        actualizarPasswordEstudiante($idEst, $pwdNva);
    }

    $res = actualizarPerfilEstudiante($idEst, $nom, $eml, $tel);

    if ($res) {
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
