<?php
session_start();
require_once "../../../modelos/tfg.php";
$id = $_POST['idEstudiante'];
if (empty($id)) {
    $_SESSION['error'] = "ID obligatorio";
} else if (eliminarArchivoTFG($id)) {
    $_SESSION['exito'] = "Ok";
    header("Location: /pfc/vistas/admin/tfg/verTFGs.php");
    exit;
} else {
    $_SESSION['error'] = "Error BD";
}
header("Location: /pfc/vistas/admin/tfg/verTFGs.php");
exit;

