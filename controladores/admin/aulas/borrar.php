<?php
session_start();
require_once __DIR__ . "/../../../modelos/aulas.php";

if (isset($_POST['idAula'])) {
    $idAula = trim($_POST['idAula']);
    if (eliminarAula($idAula)) {
        $_SESSION['exito'] = "Aula eliminada.";
    } else {
        $_SESSION['error'] = "Error al eliminar el aula.";
    }
}
header("Location: ../../../vistas/admin/aulas/verAulas.php");
exit;
?>
