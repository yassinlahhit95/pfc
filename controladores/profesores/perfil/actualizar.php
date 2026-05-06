<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['actualizarPerfil'])) {
    $idProf = trim($_POST['idProfesor']);
    $nom = trim($_POST['nombreProfesor']);
    $eml = strtolower(trim($_POST['emailProfesor']));
    $tel = trim($_POST['telefonoProfesor']);
    
    $pwdAct = trim($_POST['current_password']);
    $pwdNva = trim($_POST['new_password']);

    $errs = [];

    if (empty($idProf)) {
        header("Location: ../../../vistas/profesores/perfil/ver.php");
        exit;
    }

    if (empty($nom)) $errs['nombreProfesor'] = "El nombre es obligatorio.";
    if (empty($eml)) $errs['emailProfesor'] = "El correo es obligatorio.";
    else if (!filter_var($eml, FILTER_VALIDATE_EMAIL)) $errs['emailProfesor'] = "Formato inválido.";
    if (!empty($tel) && !is_numeric($tel)) $errs['telefonoProfesor'] = "Debe ser un número.";

    if (!empty($pwdNva)) {
        if (empty($pwdAct)) {
            $errs['current_password'] = "Ingresa la contraseña actual.";
        } else {
            $prof = obtenerProfesorPorId($idProf);
            if (!$prof || $prof['password'] !== $pwdAct) {
                $errs['current_password'] = "Contraseña actual incorrecta.";
            } else if (strlen($pwdNva) < 6) {
                $errs['new_password'] = "Mínimo 6 caracteres.";
            }
        }
    }

    if (!empty($errs)) {
        $_SESSION['errores'] = $errs;
        $_SESSION['datos_perfil'] = $_POST;
        header("Location: ../../../vistas/profesores/perfil/editar.php");
        exit;
    }

    if (!empty($pwdNva)) {
        actualizarPasswordProfesor($idProf, $pwdNva);
    }

    $res = actualizarPerfilProfesor($idProf, $nom, $eml, $tel);
    
    if ($res) {
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
