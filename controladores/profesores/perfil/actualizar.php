<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['actualizarPerfil'])) {
    $idProfesor = $_POST['idProfesor'];
    $nombre = trim($_POST['nombreProfesor']);
    $email = strtolower(trim($_POST['emailProfesor']));
    $telefono = $_POST['telefonoProfesor'];

    if (empty($idProfesor)) {
        header("Location: /pfc/vistas/profesores/perfil/ver.php");
        exit;
    } else if (empty($nombre)) {
        $_SESSION['error'] = "Nombre vacio";
    } else if (empty($email)) {
        $_SESSION['error'] = "Email vacio";
    } else if (!is_numeric($telefono) || !preg_match('/^[0-9]{9}$/', $telefono)) {
        $_SESSION['error'] = "El teléfono debe ser numérico y tener exactamente 9 dígitos";
    } else if (actualizarPerfilProfesor($idProfesor, $nombre, $email, $telefono)) {
        $_SESSION['exito'] = "Perfil actualizado";
        header("Location: /pfc/vistas/profesores/perfil/ver.php");
        exit;
    } else {
        $_SESSION['error'] = "Error";
    }
    header("Location: /pfc/vistas/profesores/perfil/editar.php");
    exit;
}
header("Location: /pfc/vistas/profesores/perfil/ver.php");
exit;
?>
