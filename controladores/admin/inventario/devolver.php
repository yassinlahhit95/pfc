<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');
require_once __DIR__ . "/../../../modelos/inventario.php";

$idPrestamo = (int)($_POST['idPrestamo'] ?? $_GET['id'] ?? 0);

$hayError = false;

if ($idPrestamo <= 0) {
    $_SESSION['errores'] = "Préstamo no encontrado.";
    $hayError = true;
}

if (!$hayError) {
    if (devolverPrestamo($idPrestamo)) {
        $_SESSION['exito'] = "Préstamo devuelto.";
    } else {
        $_SESSION['errores'] = "No se pudo registrar la devolución.";
    }
}

header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
exit;
?>
