<?php
session_start();
require_once "../../../modelos/inventario.php";
$id = $_POST['idPrestamo'];
if (empty($id)) {
    $id = $_GET['id'];
}
if (empty($id)) {
    $_SESSION['error'] = "ID obligatorio";
} else if (devolverPrestamo($id)) {
    $_SESSION['exito'] = "Ok";
    header("Location: /pfc/vistas/admin/inventario/gestionarPrestamos.php");
    exit;
} else {
    $_SESSION['error'] = "Error BD";
}
header("Location: /pfc/vistas/admin/inventario/gestionarPrestamos.php");
exit;


