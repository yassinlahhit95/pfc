<?php
session_start();
require_once "../../../modelos/reclamaciones.php";
$id = $_POST['idReclamacion'];
$estado = $_POST['nuevo_estado'];
if (empty($id)) {
    $_SESSION['error'] = "ID obligatorio";
} else if (cambiarEstadoReclamacion($id, $estado)) {
    $_SESSION['exito'] = "Ok";
    header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
    exit;
} else {
    $_SESSION['error'] = "Error BD";
}
header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
exit;

