<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['actualizarReclamacion'])) {
    $id = $_POST['idReclamacion'];
    $est = $_POST['estadoReclamacion'];

    if (cambiarEstadoReclamacion($id, $est)) {
        $_SESSION['exito'] = "Estado actualizado.";
    } else {
        $_SESSION['error'] = "Error al actualizar.";
    }
}
header("Location: /pfc/vistas/profesores/reclamaciones/lista.php");
exit;
?>
