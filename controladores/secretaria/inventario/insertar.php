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
if (isset($_POST['guardarArticulo'])) {
    if (!Security::validateCSRFToken(null, false)) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/secretaria/inventario/agregarArticulo.php");
        exit;
    }
    $nombre      = trim($_POST['nombreArticulo']);
    $numeroSerie = trim($_POST['numeroSerie']);
    $cantidad    = max(1, (int)($_POST['cantidad'] ?? 1));

    $errores = [];
    if (empty($nombre))      $errores['nombreArticulo'] = "El nombre del artículo es un campo obligatorio.";
    if (empty($numeroSerie)) $errores['numeroSerie'] = "El número de serie es un campo obligatorio.";

    if (empty($errores) && checkArticuloExistente($numeroSerie)) {
        $errores['numeroSerie'] = "Este número de serie ya está registrado en el inventario.";
    }

    if (empty($errores)) {
        $foto = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $dir = __DIR__ . '/../../../public/uploads/equipos/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $foto = uniqid('dev_') . '.jpg';
            move_uploaded_file($_FILES['foto']['tmp_name'], $dir . $foto);
        }

        if (insertarArticulo($nombre, $numeroSerie, $cantidad, $foto)) {
            registrarAccionSecretaria('insertar', 'inventario', null, "$nombre · S/N:$numeroSerie");
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['ok' => true, 'msg' => 'El dispositivo ha sido añadido correctamente.']);
                exit;
            }
            $_SESSION['exito'] = "El dispositivo ha sido añadido correctamente.";
            header("Location: ../../../vistas/secretaria/inventario/verInventario.php");
            exit;
        }
        $errores = ['general' => 'Ocurrió un error al intentar añadir el dispositivo.'];
    }

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => $errores['general'] ?? reset($errores), 'errores' => $errores, 'csrf_token' => Security::generateCSRFToken()]);
        exit;
    }
    $_SESSION['errores'] = $errores;
    $_SESSION['datos_inventario'] = $_POST;

    header("Location: ../../../vistas/secretaria/inventario/agregarArticulo.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/secretaria/inventario/verInventario.php");
exit;
