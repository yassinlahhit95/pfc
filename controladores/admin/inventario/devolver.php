<?php
session_start();
require_once "../../../modelos/inventario.php";

$id = null;
if (isset($_POST['idPrestamo'])) {
    $id = $_POST['idPrestamo'];
} else if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$redireccion = '/pfc/vistas/admin/inventario/gestionarPrestamos.php';
if (isset($_POST['redireccion'])) {
    $redireccion = $_POST['redireccion'];
}

if ($id) {
    if (devolverPrestamo($id)) {
        $_SESSION['exito'] = "Dispositivo devuelto correctamente";
    } else {
        $_SESSION['error'] = "Error al procesar la devolución";
    }
}

header("Location: $redireccion");
exit;
?>