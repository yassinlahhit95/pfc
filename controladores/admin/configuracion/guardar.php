<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';
require_once __DIR__ . '/../../../modelos/log.php';
require_once __DIR__ . '/../../../include/ImageOptimizer.php';
require_once __DIR__ . '/../../../include/R2Client.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken()) {
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/configuracion/configuracion.php");
    exit;
}

$campos = ['nombreCentro','codigoCentro','nifCifCentro','direccionCentro','ciudadCentro',
           'cpCentro','telefonoCentro','emailCentro','cursoEscolar',
           'textoLegal','nombreDirectorFirmante'];
$datos = [];
foreach ($campos as $c) {
    $datos[$c] = trim($_POST[$c] ?? '');
}

// ── Validación de campos ────────────────────────────────────────────────
$errores = [];
if (empty($datos['nombreCentro'])) {
    $errores['nombreCentro'] = "El nombre del centro educativo es un campo obligatorio.";
}
if (!empty($datos['emailCentro']) && !Security::validateEmail($datos['emailCentro'])) {
    $errores['emailCentro'] = "La dirección de correo electrónico no es válida.";
}
if (!empty($datos['telefonoCentro']) && !Security::validatePhone($datos['telefonoCentro'])) {
    $errores['telefonoCentro'] = "El número de teléfono no es válido (debe contener entre 9 y 15 dígitos).";
}

if (!empty($errores)) {
    $_SESSION['errores']             = $errores;
    $_SESSION['datos_configuracion'] = $_POST;
    header("Location: ../../../vistas/admin/configuracion/configuracion.php");
    exit;
}

guardarConfiguracionCentro($datos);

// ── Subida y borrado de logos ───────────────────────────────────────────
$logoFields = ['logoCentro', 'logoGobierno1', 'logoGobierno2'];
$uploadDir  = __DIR__ . '/../../../public/uploads/configuracion/';
$mimeExtMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

$cfgActual = obtenerConfiguracionCentro();
foreach ($logoFields as $field) {
    if (!empty($_POST['borrar_' . $field])) {
        if (!empty($cfgActual[$field])) {
            $nombreViejo = basename($cfgActual[$field]);
            $ruta = $uploadDir . $nombreViejo;
            if (is_file($ruta)) @unlink($ruta);
            R2Client::deleteObject('configuracion/' . $nombreViejo);
        }
        actualizarLogoCentro($field, '');
        continue;
    }
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) continue;
    $file = $_FILES[$field];
    if ($file['size'] > 2 * 1024 * 1024) continue;
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($mimeExtMap[$mime])) continue;
    $ext      = $mimeExtMap[$mime];
    $filename = $field . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $tmpName  = $file['tmp_name'];

    ImageOptimizer::optimize($tmpName, $mime); // optimizar el temporal antes de subir a R2
    $bytes = file_get_contents($tmpName);
    if ($bytes !== false && R2Client::putObject('configuracion/' . $filename, $bytes, $mime)) {
        // El logo anterior deja de usarse: se elimina de ambos almacenamientos
        if (!empty($cfgActual[$field])) {
            $nombreAnterior = basename($cfgActual[$field]);
            $rutaAnterior = $uploadDir . $nombreAnterior;
            if (is_file($rutaAnterior)) @unlink($rutaAnterior);
            R2Client::deleteObject('configuracion/' . $nombreAnterior);
        }
        actualizarLogoCentro($field, $filename);
    }
    @unlink($tmpName);
}

registrarAccion('actualizar', 'configuracion', null, 'Configuración del centro guardada');
$_SESSION['exito'] = "Configuración guardada correctamente.";
header("Location: ../../../vistas/admin/configuracion/configuracion.php");
exit;
