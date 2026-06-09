<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/inventario.php";

$idPrestamo = trim($_POST['idPrestamo'] ?? $_GET['id'] ?? '');

$hayError = false;

if (empty($idPrestamo)) {
    $_SESSION['errores'] = "Error del préstamo.";
    $hayError = true;
}

if (!$hayError) {
    if (devolverPrestamo($idPrestamo)) {
        $_SESSION['exito'] = "Préstamo devuelto.";
    } else {
        $_SESSION['errores'] = "Error al devolver.";
    }
}

header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
exit;
?>
