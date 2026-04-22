<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (eliminarReto($id)) {
        $_SESSION['exito'] = "Reto eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el reto.";
    }
}
header("Location: /pfc/vistas/profesores/retos/lista.php");
exit;
?>
