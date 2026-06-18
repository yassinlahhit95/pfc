<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');
require_once __DIR__ . "/../../../modelos/inventario.php";

if (isset($_POST['idArticulo'])) {
    $idArticulo = (int)($_POST['idArticulo'] ?? 0);
    if (eliminarArticulo($idArticulo)) {
        $_SESSION['exito'] = "Artículo eliminado.";
    } else {
        $_SESSION['errores'] = "No se pudo eliminar el artículo.";
    }
}
header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
?>
