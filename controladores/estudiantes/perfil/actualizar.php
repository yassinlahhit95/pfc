<?php
session_start();
require_once "../../../modelos/estudiantes.php";

if (isset($_POST['actualizarPerfil'])) {
    $id = $_POST['idEstudiante'];
    $n = trim($_POST['nombreEstudiante']);
    $e = strtolower(trim($_POST['emailEstudiante']));
    $t = $_POST['telefonoEstudiante'];

    if (empty($id)) {
        header("Location: /pfc/vistas/estudiantes/perfil/ver.php");
        exit;
    } else if (empty($n)) {
        $_SESSION['error'] = "Nombre vacio";
    } else if (empty($e)) {
        $_SESSION['error'] = "Email vacio";
    } else if (!is_numeric($t)) {
        $_SESSION['error'] = "Telefono debe ser numero";
    } else if (actualizarPerfilEstudiante($id, $n, $e, $t)) {
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
