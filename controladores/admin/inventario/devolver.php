<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

$idPrestamo = trim($_POST['idPrestamo'] ?? $_GET['id'] ?? '');

$hayError = false;

if (empty($idPrestamo)) {
    $_SESSION['error'] = "Vaya, no se ha proporcionado el ID del prÃ©stamo.";
    $hayError = true;
}

if (!$hayError) {
    if (devolverPrestamo($idPrestamo)) {
        $_SESSION['exito'] = "Listo! PrÃ©stamo devuelto correctamente.";
    } else {
        $_SESSION['error'] = "Vaya, ha ocurrido un error al devolver el prÃ©stamo.";
    }
}

header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
exit;
