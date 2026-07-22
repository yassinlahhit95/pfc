<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_gastos');
require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../include/ImageOptimizer.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!isset($_POST['actualizarGasto'])) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/admin/gastos/verGastos.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/gastos/verGastos.php");
    exit;
}

$errores     = [];
$idGasto     = (int)($_POST['idGasto'] ?? 0);
$idCategoria = (int)($_POST['idCategoria'] ?? 0);
$idCiclo     = !empty($_POST['idCiclo']) ? (int)$_POST['idCiclo'] : null;
$concepto    = trim($_POST['concepto'] ?? '');
$importe     = filter_var($_POST['importe'] ?? '', FILTER_VALIDATE_FLOAT);
$fecha       = $_POST['fecha'] ?? '';
$tipoJust    = $_POST['tipoJustificante'] ?? 'otro';
$numRef      = trim($_POST['numeroReferencia'] ?? '');
$observ      = trim($_POST['observaciones'] ?? '');
// Path for justificantes (ensure defined for later cleanup)
$directorio  = __DIR__ . "/../../../public/uploads/justificantes/";
$archivosAntiguos = null;

// ── Validation ─────────────────────────────────────────────────────
$tiposValidos = ['factura', 'ticket', 'recibo', 'otro'];
if (!$idGasto)                                  { $errores[] = "ID de gasto no válido."; }
if (!$idCategoria)                              { $errores[] = "Selecciona una categoría."; }
if (empty($concepto))                           { $errores[] = "El concepto es obligatorio."; }
if ($importe === false || $importe <= 0)        { $errores[] = "El importe debe ser mayor que cero."; }
if (empty($fecha) || !strtotime($fecha))        { $errores[] = "La fecha no es válida."; }
if (!in_array($tipoJust, $tiposValidos, true))  { $tipoJust = 'otro'; }

if (!empty($errores)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => implode(' ', $errores)]);
        exit;
    }
    $_SESSION['errores'] = implode(' ', $errores);
    header("Location: ../../../vistas/admin/gastos/modificarGasto.php?idGasto=$idGasto");
    exit;
}

// Retrieve existing record to keep old file if no new one uploaded
$gastoActual = obtenerGastoPorId($idGasto);
if (!$gastoActual) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Gasto no encontrado.']);
        exit;
    }
    $_SESSION['errores'] = "Gasto no encontrado.";
    header("Location: ../../../vistas/admin/gastos/verGastos.php");
    exit;
}

// ── File upload (optional replacement) ─────────────────────────────
$nombreArchivo = $gastoActual['archivoJustificante'];
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
            $ext         = $mime === 'application/pdf' ? 'pdf' : pathinfo($archivos['name'][$i], PATHINFO_EXTENSION);
            $ext         = preg_replace('/[^a-z0-9]/', '', strtolower($ext));
            $nuevoNombre = bin2hex(random_bytes(16)) . '.' . $ext;
            $tmpName     = $archivos['tmp_name'][$i];

            if ($mime !== 'application/pdf') ImageOptimizer::optimize($tmpName, $mime); // optimizar el temporal ANTES de subir a R2

            $bytes   = file_get_contents($tmpName);
            $subioOk = $bytes !== false && R2Client::putObject('justificantes/' . $nuevoNombre, $bytes, $mime);
            @unlink($tmpName);

            if ($subioOk) {
                $nombresArchivos[] = $nuevoNombre;
            } else {
                $errores[] = "No se pudo guardar el archivo {$archivos['name'][$i]} en el servidor.";
            }
        }
    }
    
    // Si se subieron archivos nuevos, se sustituyen los anteriores — pero el
    // borrado físico se difiere hasta después de confirmar el UPDATE en BD
    // (ver más abajo), para no perder los antiguos si el guardado falla.
    if (!empty($nombresArchivos) && empty($errores)) {
        $archivosAntiguos = $nombreArchivo;
        $nombreArchivo = json_encode($nombresArchivos);
    }
}

if (!empty($errores)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => implode(' ', $errores)]);
        exit;
    }
    $_SESSION['errores'] = implode(' ', $errores);
    header("Location: ../../../vistas/admin/gastos/modificarGasto.php?idGasto=$idGasto");
    exit;
}

if (actualizarGasto($idGasto, $idCategoria, $idCiclo, $concepto, $importe, $fecha,
                    $tipoJust, $numRef ?: null, $nombreArchivo, $observ ?: null)) {
    if (!empty($archivosAntiguos)) {
        $viejos = json_decode($archivosAntiguos, true);
        $viejos = is_array($viejos) ? $viejos : [$archivosAntiguos];
        foreach ($viejos as $nombreViejo) {
            if (file_exists($directorio . $nombreViejo)) { @unlink($directorio . $nombreViejo); }
            R2Client::deleteObject('justificantes/' . $nombreViejo);
        }
    }
    registrarAccion('actualizar', 'gastos', $idGasto, $concepto);
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'msg' => 'Gasto actualizado correctamente.']);
        exit;
    }
    $_SESSION['exito'] = "Gasto actualizado correctamente.";
} else {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Error al actualizar el gasto.']);
        exit;
    }
    $_SESSION['errores'] = "Error al actualizar el gasto.";
}

header("Location: ../../../vistas/admin/gastos/verGastos.php");
exit;
