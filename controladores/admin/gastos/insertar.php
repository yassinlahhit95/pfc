<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_gastos');
require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/log.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!isset($_POST['insertarGasto'])) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    header("Location: ../../../vistas/admin/gastos/agregarGasto.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']); exit; }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/gastos/agregarGasto.php");
    exit;
}

$errores     = [];
$idCategoria = (int)($_POST['idCategoria'] ?? 0);
$idCiclo     = !empty($_POST['idCiclo']) ? (int)$_POST['idCiclo'] : null;
$concepto    = trim($_POST['concepto'] ?? '');
$importe     = filter_var($_POST['importe'] ?? '', FILTER_VALIDATE_FLOAT);
$fecha       = $_POST['fecha'] ?? '';
$tipoJust    = $_POST['tipoJustificante'] ?? 'otro';
$numRef      = trim($_POST['numeroReferencia'] ?? '');
$observ      = trim($_POST['observaciones'] ?? '');

// ── Validation ─────────────────────────────────────────────────────
$tiposValidos = ['factura', 'ticket', 'recibo', 'otro'];
if (!$idCategoria)                              { $errores[] = "Selecciona una categoría."; }
if (empty($concepto))                           { $errores[] = "El concepto es obligatorio."; }
if ($importe === false || $importe <= 0)        { $errores[] = "El importe debe ser mayor que cero."; }
if (empty($fecha) || !strtotime($fecha))        { $errores[] = "La fecha no es válida."; }
if (!in_array($tipoJust, $tiposValidos, true))  { $tipoJust = 'otro'; }

// ── File upload (optional justificante) ────────────────────────────
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
            $directorio    = __DIR__ . "/../../../public/uploads/justificantes/";

            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            if (!move_uploaded_file($archivos['tmp_name'][$i], $directorio . $nombreArchivo)) {
                $errores[] = "No se pudo guardar el archivo {$archivos['name'][$i]} en el servidor.";
            } else {
                $nombresArchivos[] = $nombreArchivo;
            }
        }
    }
}
$archivoGuardar = !empty($nombresArchivos) ? json_encode($nombresArchivos) : null;

if (!empty($errores)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => implode(' ', $errores)]);
        exit;
    }
    $_SESSION['errores'] = implode(' ', $errores);
    header("Location: ../../../vistas/admin/gastos/agregarGasto.php");
    exit;
}

$resultado = insertarGasto($idCategoria, $idCiclo, $concepto, $importe, $fecha,
                            $tipoJust, $numRef ?: null, $archivoGuardar, $observ ?: null);

if ($resultado) {
    registrarAccion('insertar', 'gastos', (int)$resultado, $concepto);
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
        }
    }
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar el gasto en la base de datos.']);
        exit;
    }
    $_SESSION['errores'] = "Error al guardar el gasto en la base de datos.";
}

header("Location: ../../../vistas/admin/gastos/verGastos.php");
exit;
