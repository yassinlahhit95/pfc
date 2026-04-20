<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['actualizarPerfil'])) {
    $id = $_POST['idEstudiante'];
    $nombre = trim($_POST['nombreEstudiante']);
    $email = trim($_POST['emailEstudiante']);
    $telefono = trim($_POST['telefonoEstudiante']);

    if (empty($id)) {
        header("Location: ../../vistas/perfil/ver.php");
    } else if (empty($nombre)) {
        $_SESSION['error'] = "El nombre es obligatorio.";
        header("Location: ../../vistas/perfil/editar.php");
    } else if (empty($email)) {
        $_SESSION['error'] = "El email es obligatorio.";
        header("Location: ../../vistas/perfil/editar.php");
    } else {
        if (actualizarPerfilEstudiante($id, $nombre, $email, $telefono)) {
            $_SESSION['exito'] = "Perfil actualizado correctamente.";
            header("Location: ../../vistas/perfil/ver.php");
        } else {
            $_SESSION['error'] = "Error al actualizar el perfil.";
            header("Location: ../../vistas/perfil/editar.php");
        }
    }
    exit;
}

header("Location: ../../vistas/perfil/ver.php");
exit;
?>