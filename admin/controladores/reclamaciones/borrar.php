<?php
session_start();
require_once "../../modelos/reclamaciones.php";

if (isset($_POST['idReclamacion'])) {
    $id = $_POST['idReclamacion'];
    
    if (!empty($id)) {
        if (eliminarReclamacion($id)) {
            $_SESSION['exito'] = "Reclamación eliminada.";
        } else {
            $_SESSION['error'] = "Error al eliminar la reclamación.";
        }
    } else {
        $_SESSION['error'] = "ID de la reclamación no válido";
    }
}

header("Location: ../../vistas/reclamaciones/verReclamaciones.php");
exit;
?>
