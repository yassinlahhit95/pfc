<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/ImageOptimizer.php";
require_once __DIR__ . "/../../../include/R2Client.php";

// ajax=1 → subida por XHR con barra de progreso real (aula-recursos.js); responde
// JSON en vez de redirigir, igual que subirArchivos.php.
$ajax = !empty($_POST['ajax']);

function _subirVersionSalir(bool $ajax, string $destino): void {
    if ($ajax) {
        header('Content-Type: application/json');
        $ok  = empty($_SESSION['errores']);
        $msg = $ok ? ($_SESSION['exito'] ?? '') : ($_SESSION['errores'] ?? '');
        unset($_SESSION['exito'], $_SESSION['errores']);
        echo json_encode(['ok' => $ok, 'msg' => $msg]);
    } else {
        header("Location: $destino");
    }
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
if (!Security::validateCSRFToken(null, false)) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    _subirVersionSalir($ajax, "../../../vistas/profesores/aula/recursos.php");
}

$idProfesor = $_SESSION['idProfesor'];
$idArchivo  = intval($_POST['idArchivo'] ?? 0);
$idModulo   = intval($_POST['idModulo'] ?? 0);

$archivo = $idArchivo > 0 ? obtenerArchivoPorId($idArchivo) : null;
if (!$archivo || $archivo['idProfesor'] != $idProfesor) {
    $_SESSION['errores'] = "No puedes actualizar este archivo.";
    _subirVersionSalir($ajax, "../../../vistas/profesores/aula/recursos.php?id=$idModulo");
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$permitidos = [
    'pdf', 'doc', 'docx', 'txt', 'rtf', 'odt',
    'xls', 'xlsx', 'ods', 'csv',
    'ppt', 'pptx', 'odp',
    'jpg', 'jpeg', 'png', 'gif', 'webp',
    'zip', 'rar'
];

if (empty($_FILES['archivo']['name']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['errores'] = "No se recibió ningún archivo.";
    _subirVersionSalir($ajax, "../../../vistas/profesores/aula/recursos.php?id={$archivo['idModulo']}");
}

$nombreOrig = $_FILES['archivo']['name'];
$ext        = strtolower(pathinfo($nombreOrig, PATHINFO_EXTENSION));
$tamanio    = $_FILES['archivo']['size'];

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (!in_array($ext, $permitidos)) {
    $_SESSION['errores'] = "Tipo de archivo no permitido ($ext).";
} elseif ($tamanio > 20 * 1024 * 1024) {
    $_SESSION['errores'] = "El archivo supera el límite de 20 MB.";
} else {
    $nombreArchivo = bin2hex(random_bytes(12)) . '.' . $ext;
    $tmpName       = $_FILES['archivo']['tmp_name'];

    $imgMimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    if (isset($imgMimes[$ext])) ImageOptimizer::optimize($tmpName, $imgMimes[$ext]); // optimizar el temporal ANTES de subir a R2

    $mimeReal = @mime_content_type($tmpName) ?: 'application/octet-stream';
    $bytes    = file_get_contents($tmpName);
    $subioOk  = $bytes !== false && R2Client::putObject('aula/archivos/' . $nombreArchivo, $bytes, $mimeReal);
    @unlink($tmpName);

    if ($subioOk) {
        $nuevaVersion = actualizarArchivoConVersionAula($idArchivo, $nombreArchivo, $nombreOrig, $ext, $tamanio, $idProfesor);
        if ($nuevaVersion) {
            $_SESSION['exito'] = "Nueva versión (v$nuevaVersion) guardada.";
        } else {
            R2Client::deleteObject('aula/archivos/' . $nombreArchivo); // el archivo se subió pero el registro en BD falló — eliminar huérfano
            $_SESSION['errores'] = "No se pudo registrar la nueva versión.";
        }
    } else {
        $_SESSION['errores'] = "Error al guardar el archivo.";
    }
}

$destino = "../../../vistas/profesores/aula/recursos.php?id={$archivo['idModulo']}";
if (!empty($archivo['idCarpeta'])) $destino .= "&carpeta=" . $archivo['idCarpeta'];
_subirVersionSalir($ajax, $destino);
