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

// colorAcento was missing from this list — guardarConfiguracionCentro() binds
// $d['colorAcento'] unconditionally in its UPDATE, so every save was silently
// wiping the center's accent color to null/empty (undefined-key), even though
// the form's color picker looked and behaved like it should work.
$campos = ['nombreCentro','codigoCentro','nifCifCentro','direccionCentro','ciudadCentro',
           'cpCentro','telefonoCentro','emailCentro','cursoEscolar','colorAcento',
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
if (!empty($datos['colorAcento']) && !preg_match('/^#[0-9a-fA-F]{6}$/', $datos['colorAcento'])) {
    $errores['colorAcento'] = "El color de acento no es válido.";
}
if (empty($datos['colorAcento'])) {
    $datos['colorAcento'] = '#4f46e5'; // mismo valor por defecto que el <input type="color"> en la vista
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

    ImageOptimizer::optimize($tmpName, $mime);
    $bytes = file_get_contents($tmpName);

    // Local write first, same as every other upload handler in this app
    // (gastos/blog/ofertaCiclos) — R2 is a best-effort mirror, never the only
    // copy. R2Client::putObject() throws when R2 isn't configured in .env,
    // which is a normal, fully-supported state (noDeploy/CLOUDFLARE_R2_SETUP.md);
    // this used to be the *only* storage path here, so a logo upload with R2
    // unconfigured crashed the whole save request with an uncaught exception.
    if ($bytes !== false && file_put_contents($uploadDir . $filename, $bytes) !== false) {
        try {
            R2Client::putObject('configuracion/' . $filename, $bytes, $mime);
        } catch (Throwable $t) {
            error_log('R2 upload failed for configuracion/' . $filename . ': ' . $t->getMessage());
        }
        // El logo anterior deja de usarse: se elimina de ambos almacenamientos
        if (!empty($cfgActual[$field])) {
            $nombreAnterior = basename($cfgActual[$field]);
            $rutaAnterior = $uploadDir . $nombreAnterior;
            if (is_file($rutaAnterior)) @unlink($rutaAnterior);
            try {
                R2Client::deleteObject('configuracion/' . $nombreAnterior);
            } catch (Throwable $t) {
                error_log('R2 delete failed for configuracion/' . $nombreAnterior . ': ' . $t->getMessage());
            }
        }
        actualizarLogoCentro($field, $filename);
    }
    @unlink($tmpName);
}

registrarAccion('actualizar', 'configuracion', null, 'Configuración del centro guardada');
$_SESSION['exito'] = "Configuración guardada correctamente.";
header("Location: ../../../vistas/admin/configuracion/configuracion.php");
exit;
