<?php
session_start();
require_once "../../../modelos/eventos.php";

if (isset($_POST['idEvento'])) {
    $id = $_POST['idEvento'];
    $resultado = eliminarEvento($id);
    if ($resultado) {
        $_SESSION['exito'] = "Evento eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el evento.";
    }
}

header("Location: /pfc/vistas/admin/eventos/gestionEventos.php");
exit;
?>
