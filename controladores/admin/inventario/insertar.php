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
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (isset($_POST['guardarArticulo'])) {
    if (!Security::validateCSRFToken()) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida. Inténtelo de nuevo.']);
            exit;
        }
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/inventario/verInventario.php");
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
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => true, 'msg' => 'El artículo ha sido añadido al inventario correctamente.']);
                exit;
            }
            $_SESSION['exito'] = "El artículo ha sido añadido al inventario correctamente.";
            header("Location: ../../../vistas/admin/inventario/verInventario.php");
            exit;
        }
        $errores = ['general' => 'Ocurrió un error al intentar añadir el artículo al inventario.'];
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => $errores['general'] ?? reset($errores), 'errores' => $errores, 'csrf_token' => Security::generateCSRFToken()]);
        exit;
    }
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_inventario'] = $_POST;
    header("Location: ../../../vistas/admin/inventario/verInventario.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/inventario/verInventario.php");
exit;
