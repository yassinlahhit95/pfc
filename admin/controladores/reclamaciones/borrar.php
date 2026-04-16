<?php
session_start();
require_once "../../modelos/reclamaciones.php";

if (isset($_POST['idReclamacion'])) {
    $id = $_POST['idReclamacion'];
    
    if (is_numeric($id) && ctype_digit($id) && preg_match('/^[0-9]+$/', $id)) {
        $modeloReclamacion = new reclamacion();
        if ($modeloReclamacion->eliminarReclamacionModelo($id)) {
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
