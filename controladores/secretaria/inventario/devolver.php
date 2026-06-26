<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/SecretariaGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');
require_once __DIR__ . "/../../../modelos/inventario.php";
require_once __DIR__ . "/../../../modelos/log.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
    exit;
}

$idPrestamo = (int)($_POST['idPrestamo'] ?? 0);

if ($idPrestamo <= 0) {
    $_SESSION['errores'] = "El préstamo especificado no existe.";
    header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if (devolverPrestamo($idPrestamo)) {
    registrarAccionSecretaria('devolver', 'inventario', $idPrestamo);
    $_SESSION['exito'] = "La devolución del préstamo ha sido registrada correctamente.";
} else {
    $_SESSION['errores'] = "No se pudo registrar la devolución del préstamo.";
}

header("Location: ../../../vistas/secretaria/inventario/gestionarPrestamos.php");
exit;
