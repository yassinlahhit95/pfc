<?php
session_start();
require_once "../../../modelos/aulas.php";
if (isset($_POST['guardarAula'])) {
    $nombre = $_POST['nombreAula'];
    if (empty($nombre)) {
        $_SESSION['error'] = "Nombre obligatorio";
    } else if (insertarAula($nombre)) {
        $_SESSION['exito'] = "Ok";
        header("Location: /pfc/vistas/admin/aulas/verAulas.php");
        exit;
    } else {
        $_SESSION['error'] = "Error BD";
    }
    header("Location: /pfc/vistas/admin/aulas/verAulas.php");
    exit;
}
header("Location: /pfc/vistas/admin/aulas/verAulas.php");
exit;

