<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['actualizarPerfil'])) {
    $id = $_POST['idProfesor'];
    $n = trim($_POST['nombreProfesor']);
    $e = strtolower(trim($_POST['emailProfesor']));
    $t = $_POST['telefonoProfesor'];

    if (empty($id)) {
        header("Location: /pfc/vistas/profesores/perfil/ver.php");
        exit;
    } else if (empty($n)) {
        $_SESSION['error'] = "Nombre vacio";
    } else if (empty($e)) {
        $_SESSION['error'] = "Email vacio";
    } else if (!is_numeric($t)) {
        $_SESSION['error'] = "Telefono debe ser numero";
    } else if (actualizarPerfilProfesor($id, $n, $e, $t)) {
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
