<?php
session_start();
require_once "../../../modelos/aulas.php";

if (isset($_POST['accion'])) {
    if ($_POST['accion'] == 'eliminar') {
        
        $id = '';
        if (isset($_POST['idAula'])) {
            $id = $_POST['idAula'];
        }

        if (!empty($id)) {
            if (eliminarAula($id)) {
                $_SESSION['exito'] = "Aula eliminada correctamente.";
            } else {
                $_SESSION['error'] = "Error al eliminar el aula.";
            }
        }
    }
}

header("Location: ../../vistas/aulas/verAulas.php");
exit;
?>