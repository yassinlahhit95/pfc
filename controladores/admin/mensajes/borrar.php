<?php
session_start();
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$hayError = false;

if (isset($_POST['idReclamacion'])) {
    $idReclamacionParaBorrar = trim($_POST['idReclamacion']);
    
    if (eliminarMensaje($idReclamacionParaBorrar)) {
        $_SESSION['exito'] = "Reclamación eliminada.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/reclamaciones/verReclamaciones.php");
exit;
