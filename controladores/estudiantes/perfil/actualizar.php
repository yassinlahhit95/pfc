<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['actualizarPerfil'])) {
    $idEstudiante = $_POST['idEstudiante'];
    $nombre = trim($_POST['nombreEstudiante']);
    $email = strtolower(trim($_POST['emailEstudiante']));
    $telefono = $_POST['telefonoEstudiante'];

    if (empty($idEstudiante)) {
        header("Location: /pfc/vistas/estudiantes/perfil/ver.php");
        exit;
    } else if (empty($nombre)) {
        $_SESSION['error'] = "Nombre vacio";
    } else if (empty($email)) {
        $_SESSION['error'] = "Email vacio";
    } else if (!is_numeric($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
        $_SESSION['error'] = "El teléfono debe ser numérico y tener exactamente 9 dígitos";
    } else if (actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono)) {
        $_SESSION['exito'] = "Perfil actualizado";
        header("Location: /pfc/vistas/estudiantes/perfil/ver.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
    header("Location: /pfc/vistas/estudiantes/perfil/editar.php");
    exit;
}
header("Location: /pfc/vistas/estudiantes/perfil/ver.php");
exit;
?>
