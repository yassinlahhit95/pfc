<?php
session_start();
require_once "../../../modelos/ciclos.php";

if (isset($_POST['idCiclo'])) {
    $id = $_POST['idCiclo'];
    if (eliminarCiclo($id)) {
        $_SESSION['exito'] = "Ciclo eliminado correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el ciclo.";
    }
}
header("Location: /pfc/vistas/admin/ciclos/verCiclos.php");
exit;
?>
