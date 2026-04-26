<?php
session_start();
require_once "../../../modelos/directores.php";

if (isset($_POST['idDirector'])) {
    $id = $_POST['idDirector'];
    if (eliminarDirector($id)) {
        $_SESSION['exito'] = "Director eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el director.";
    }
}
header("Location: /pfc/vistas/admin/directores/verDirectores.php");
exit;
?>

