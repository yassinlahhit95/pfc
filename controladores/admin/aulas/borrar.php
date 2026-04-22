<?php
session_start();
require_once "../../../modelos/aulas.php";

if (isset($_POST['idAula'])) {
    $id = $_POST['idAula'];
    if (eliminarAula($id)) {
        $_SESSION['exito'] = "Aula eliminada correctamente.";
    } else {
        $_SESSION['error'] = "Error al eliminar el aula.";
    }
}
header("Location: /pfc/vistas/admin/aulas/verAulas.php");
exit;
?>
