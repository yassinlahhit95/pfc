<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');
require_once __DIR__ . "/../../../modelos/inventario.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$idPrestamo = (int)($_POST['idPrestamo'] ?? $_GET['id'] ?? 0);

if ($idPrestamo <= 0) {
    $_SESSION['errores'] = "El préstamo especificado no existe.";
    header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (devolverPrestamo($idPrestamo)) {
    registrarAccion('devolver', 'inventario', $idPrestamo);
    $_SESSION['exito'] = "La devolución del préstamo ha sido registrada correctamente.";
} else {
    $_SESSION['errores'] = "No se pudo registrar la devolución del préstamo.";
}

header("Location: ../../../vistas/admin/inventario/gestionarPrestamos.php");
exit;
