<?php
session_start();
require_once "../../modelos/inventario.php";

$id = $_POST['idPrestamo'] ?? $_GET['id'] ?? null;
$redireccion = $_POST['redireccion'] ?? '../../vistas/inventario/gestionarPrestamos.php';

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
