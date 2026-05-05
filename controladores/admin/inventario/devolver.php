<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

$idPrestamo = trim($_POST['idPrestamo'] ?? $_GET['id'] ?? '');

$hayError = false;

if (empty($idPrestamo)) {
    $_SESSION['error'] = "Falta ID préstamo.";
    $hayError = true;
}

if (!$hayError) {
    if (devolverPrestamo($idPrestamo)) {
        $_SESSION['exito'] = "Préstamo devuelto.";
    } else {
        $_SESSION['error'] = "Error al devolver.";
    }
}

header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
exit;
