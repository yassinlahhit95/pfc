<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['idReclamacion'])) {
    $id = $_POST['idReclamacion'];
    if (eliminarReclamacion($id)) {
        $_SESSION['exito'] = "Reclamación eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar la reclamación.";
    }
}
header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
exit;
?>
