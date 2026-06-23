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
if (isset($_POST['guardarArticulo'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/inventario/agregarArticulo.php");
        exit;
    }
    $nombre      = trim($_POST['nombreArticulo']);
    $numeroSerie = trim($_POST['numeroSerie']);

    $errores = [];
    if (empty($nombre))      $errores['nombreArticulo'] = "El nombre del artículo es un campo obligatorio.";
    if (empty($numeroSerie)) $errores['numeroSerie'] = "El número de serie es un campo obligatorio.";

    if (empty($errores) && checkArticuloExistente($numeroSerie)) {
        $errores['numeroSerie'] = "Este número de serie ya está registrado en el inventario.";
    }

    if (empty($errores)) {
        if (insertarArticulo($nombre, $numeroSerie)) {
            registrarAccion('insertar', 'inventario', null, "$nombre · S/N:$numeroSerie");
            $_SESSION['exito'] = "El artículo ha sido añadido al inventario correctamente.";
            header("Location: ../../../vistas/admin/inventario/verInventario.php");
            exit;
        }
        $_SESSION['errores'] = "Ocurrió un error al intentar añadir el artículo al inventario.";
    } else {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_inventario'] = $_POST;
    }

    header("Location: ../../../vistas/admin/inventario/agregarArticulo.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
