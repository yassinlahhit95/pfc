<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (eliminarReclamacion($id)) {
        $_SESSION['exito'] = "Reclamación eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar la reclamación.";
    }
}
header("Location: /pfc/vistas/profesores/reclamaciones/lista.php");
exit;
?>

