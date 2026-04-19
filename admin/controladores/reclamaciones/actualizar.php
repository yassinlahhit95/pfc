<?php
session_start();
require_once "../../modelos/reclamaciones.php";

if (isset($_POST['idReclamacion']) && isset($_POST['nuevo_estado'])) {
    $id = $_POST['idReclamacion'];
    $nuevoEstado = $_POST['nuevo_estado'];

    if (!is_numeric($id) || !ctype_digit((string)$id) || !preg_match('/^[0-9]+$/', (string)$id)) {
        $_SESSION['error'] = "El ID de la reclamación debe ser numérico.";
        header("Location: ../../vistas/reclamaciones/verReclamaciones.php");
        exit;
    }

    if (cambiarEstadoReclamacion($id, $nuevoEstado)) {
        $_SESSION['exito'] = "Estado actualizado correctamente.";
    } else {
        $_SESSION['error'] = "Error al actualizar el estado.";
    }
}

header("Location: ../../vistas/reclamaciones/verReclamaciones.php");
exit;
?>
