<?php
session_start();
require_once "../../../modelos/calificaciones.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (eliminarCalificacion($id)) {
        $_SESSION['exito'] = "Calificación eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar la calificación.";
    }
}
header("Location: /pfc/vistas/profesores/calificaciones/lista.php");
exit;
?>
