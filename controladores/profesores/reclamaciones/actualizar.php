<?php
session_start();
require_once "../../../modelos/reclamaciones.php";

if (isset($_POST['actualizarReclamacion'])) {
    $id = $_POST['idReclamacion'];
    $estado = $_POST['estadoReclamacion'];

    if (empty($id)) {
        header("Location: /pfc/vistas/profesores/reclamaciones/lista.php");
    } else {
        if (cambiarEstadoReclamacion($id, $estado)) {
            $_SESSION['exito'] = "Estado de la reclamación actualizado.";
        } else {
            $_SESSION['error'] = "Error al actualizar la reclamación.";
        }
    }
    exit;
}

header("Location: /pfc/vistas/profesores/reclamaciones/lista.php");
exit;
?>