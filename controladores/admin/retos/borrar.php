<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['idReto'])) {
    $id = $_POST['idReto'];
    if (eliminarReto($id)) {
        $_SESSION['exito'] = "Reto eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el reto.";
    }
}
header("Location: /pfc/vistas/admin/retos/verRetos.php");
exit;
?>
