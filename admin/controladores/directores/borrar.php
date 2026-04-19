<?php
session_start();
require_once "../../modelos/directores.php";

if (isset($_POST['idDirector'])) {
    $id = $_POST['idDirector'];
    
    if (!empty($id)) {
        if (eliminarDirector($id)) {
            $_SESSION['exito'] = "Director eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el director";
        }
    } else {
        $_SESSION['error'] = "ID del director no válido";
    }
}

header("Location: ../../vistas/directores/verDirectores.php");
exit;
?>
