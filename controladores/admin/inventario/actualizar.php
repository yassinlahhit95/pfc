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
if (isset($_POST['actualizarArticulo'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/inventario/verInventario.php");
        exit;
    }
    $idArticulo     = (int)($_POST['idArticulo'] ?? 0);
    $nombreArticulo = trim($_POST['nombreArticulo']);
    $numeroSerie    = trim($_POST['numeroSerie']);

    $errores = '';
    if (empty($nombreArticulo)) $errores = "El nombre del artículo es un campo obligatorio.";
    elseif (empty($numeroSerie)) $errores = "El número de serie es un campo obligatorio.";
    elseif (checkArticuloExistente($numeroSerie, $idArticulo)) $errores = "Este número de serie ya está registrado por otro artículo.";

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_inventario'] = $_POST;
    } else {
        $datosArticuloActual = obtenerArticuloPorId($idArticulo);
        $estadoActual = $datosArticuloActual['estado'] ?? 'Disponible';

        if (actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoActual)) {
            registrarAccion('actualizar', 'inventario', $idArticulo, $nombreArticulo);
            $_SESSION['exito'] = "El artículo ha sido actualizado correctamente.";
            header("Location: ../../../vistas/admin/inventario/verInventario.php");
            exit;
        }
        $_SESSION['errores'] = "Ocurrió un error al intentar actualizar el artículo.";
    }

    header("Location: ../../../vistas/admin/inventario/modificarArticulo.php?idArticulo=" . $idArticulo);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
