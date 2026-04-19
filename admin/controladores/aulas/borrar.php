<?php
session_start();
require_once "../../modelos/aulas.php";

if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar') {
    $id = $_POST['idAula'] ?? '';
    if (!empty($id)) {
        if (eliminarAula($id)) {
            $_SESSION['exito'] = "Aula eliminada correctamente.";
        } else {
            $_SESSION['error'] = "Error al eliminar el aula.";
        }
    } else {
        $_SESSION['error'] = "ID del aula no válido.";
    }
    header("Location: ../../vistas/aulas/verAulas.php");
    exit;
}

header("Location: ../../vistas/aulas/verAulas.php");
exit;
?>
