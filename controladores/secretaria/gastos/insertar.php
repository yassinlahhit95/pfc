<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_gastos');
require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../include/ImageOptimizer.php";
require_once __DIR__ . "/../../../include/R2Client.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../../vistas/secretaria/gastos/agregarGasto.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/secretaria/gastos/agregarGasto.php"); exit;
}

$idCategoria      = (int)($_POST['idCategoria'] ?? 0);
$idCiclo          = (int)($_POST['idCiclo'] ?? 0) ?: null;
$concepto         = Security::sanitize($_POST['concepto'] ?? '');
$importe          = (float)($_POST['importe'] ?? 0);
$fecha            = Security::sanitize($_POST['fecha'] ?? '');
$tipoJustificante = Security::sanitize($_POST['tipoJustificante'] ?? '');
$numeroReferencia = Security::sanitize($_POST['numeroReferencia'] ?? '');
$observaciones    = Security::sanitize($_POST['observaciones'] ?? '');

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$errores = [];
if ($idCategoria <= 0) $errores[] = "Debes seleccionar una categoría.";
if (empty($concepto))  $errores[] = "El concepto es obligatorio.";
if ($importe <= 0)     $errores[] = "El importe debe ser mayor que 0.";
if (empty($fecha))     $errores[] = "La fecha es obligatoria.";

// ── File upload ────────────────────────────
$nombresArchivos = [];
if (!empty($_FILES['archivoJustificante']['name'][0])) {
    $archivos = $_FILES['archivoJustificante'];
    $totalArchivos = count($archivos['name']);
    
    for ($i = 0; $i < $totalArchivos; $i++) {
        if ($archivos['error'][$i] !== UPLOAD_ERR_OK) {
            if ($archivos['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                $errores[] = "Error al subir el archivo {$archivos['name'][$i]} (código: {$archivos['error'][$i]}).";
            }
            continue;
        }
        
        if ($archivos['size'][$i] > 8 * 1024 * 1024) {
            $errores[] = "El archivo {$archivos['name'][$i]} supera el límite de 8 MB.";
            continue;
        }

        $mimePermitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $archivos['tmp_name'][$i]);
        finfo_close($finfo);

        if (!in_array($mime, $mimePermitidos, true)) {
            $errores[] = "Tipo de archivo no permitido para {$archivos['name'][$i]}. Acepta: PDF, JPG, PNG, WebP.";
        } else {
            $ext           = $mime === 'application/pdf' ? 'pdf' : pathinfo($archivos['name'][$i], PATHINFO_EXTENSION);
            $ext           = preg_replace('/[^a-z0-9]/', '', strtolower($ext));
            $nombreArchivo = bin2hex(random_bytes(16)) . '.' . $ext;
            $tmpName       = $archivos['tmp_name'][$i];

            if ($mime !== 'application/pdf') ImageOptimizer::optimize($tmpName, $mime); // optimizar el temporal ANTES de subir a R2

            $bytes   = file_get_contents($tmpName);
            $subioOk = $bytes !== false && R2Client::putObject('justificantes/' . $nombreArchivo, $bytes, $mime);
            @unlink($tmpName);

            if (!$subioOk) {
                $errores[] = "No se pudo guardar el archivo {$archivos['name'][$i]} en el servidor.";
            } else {
                $nombresArchivos[] = $nombreArchivo;
            }
        }
    }
}
$archivoGuardar = !empty($nombresArchivos) ? json_encode($nombresArchivos) : null;

if ($errores) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => implode(' ', $errores)]);
        exit;
    }
    $_SESSION['errores'] = implode(' ', $errores);
    header("Location: ../../../vistas/secretaria/gastos/agregarGasto.php");
    exit;
}

$ok = insertarGasto($idCategoria, $idCiclo, $concepto, $importe, $fecha,
                    $tipoJustificante, $numeroReferencia, $archivoGuardar, $observaciones);

if ($ok) {
    require_once __DIR__ . '/../../../modelos/log.php';
    registrarAccionSecretaria('insertar', 'gastos', null, "Concepto: $concepto, Importe: $importe");
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'msg' => 'Gasto registrado correctamente.']);
        exit;
    }
    $_SESSION['exito'] = "Gasto registrado correctamente.";
} else {
    if (!empty($nombresArchivos)) {
        foreach ($nombresArchivos as $na) {
            @unlink(__DIR__ . "/../../../public/uploads/justificantes/" . $na);
            R2Client::deleteObject('justificantes/' . $na);
        }
    }
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Error al registrar el gasto.']);
        exit;
    }
    $_SESSION['errores'] = "Error al registrar el gasto.";
}
header("Location: ../../../vistas/secretaria/gastos/verGastos.php");
exit;
