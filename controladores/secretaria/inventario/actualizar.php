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
if (isset($_POST['actualizarArticulo'])) {
    if (!Security::validateCSRFToken(null, false)) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/secretaria/inventario/verInventario.php");
        exit;
    }
    $idArticulo     = (int)($_POST['idArticulo'] ?? 0);
    $nombreArticulo = trim($_POST['nombreArticulo']);
    $numeroSerie    = trim($_POST['numeroSerie']);
    $estado         = trim($_POST['estado'] ?? '');
    $cantidad       = max(1, (int)($_POST['cantidad'] ?? 1));

    $errores = [];
    if (empty($nombreArticulo)) $errores['nombreArticulo'] = "El nombre del artículo es un campo obligatorio.";
    if (empty($numeroSerie)) $errores['numeroSerie'] = "El número de serie es un campo obligatorio.";
    if (empty($errores) && checkArticuloExistente($numeroSerie, $idArticulo)) $errores['numeroSerie'] = "Este número de serie ya está registrado por otro artículo.";

    if (empty($errores)) {
        $datosArticuloActual = obtenerArticuloPorId($idArticulo);
        $estadoActual = !empty($estado) ? $estado : ($datosArticuloActual['estado'] ?? 'disponible');

        $fotoActual = $datosArticuloActual['foto'] ?? null;
        $foto = null;

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $dir = __DIR__ . '/../../../public/uploads/equipos/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $foto = uniqid('dev_') . '.jpg';
            move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $foto);
            
            if ($fotoActual && file_exists($dir . $fotoActual)) {
                @unlink($dir . $fotoActual);
            }
        }

        if (actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoActual, $cantidad, $foto)) {
            registrarAccionSecretaria('actualizar', 'inventario', $idArticulo, $nombreArticulo);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => true, 'msg' => 'El dispositivo ha sido actualizado correctamente.']);
                exit;
            }
            $_SESSION['exito'] = "El dispositivo ha sido actualizado correctamente.";
            header("Location: ../../../vistas/secretaria/inventario/verInventario.php");
            exit;
        }
        $errores = ['general' => 'Ocurrió un error al intentar actualizar el dispositivo.'];
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => $errores['general'] ?? reset($errores), 'errores' => $errores, 'csrf_token' => Security::generateCSRFToken()]);
        exit;
    }
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_inventario'] = $_POST;
    header("Location: ../../../vistas/secretaria/inventario/modificarArticulo.php?idArticulo=" . $idArticulo);
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/secretaria/inventario/verInventario.php");
exit;
