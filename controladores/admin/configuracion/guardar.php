<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';
require_once __DIR__ . '/../../../modelos/log.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::validateCSRFToken()) {
    if (isset($_POST['onboarding'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Solicitud inválida.']);
        exit;
    }
    $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
    header("Location: ../../../vistas/admin/configuracion/configuracion.php");
    exit;
}

$campos = ['nombreCentro','codigoCentro','direccionCentro','ciudadCentro',
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
    if (isset($_POST['onboarding'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => implode(', ', $errores)]);
        exit;
    }
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

foreach ($logoFields as $field) {
    if (!empty($_POST['borrar_' . $field])) {
        $cfgActual = obtenerConfiguracionCentro();
        if (!empty($cfgActual[$field])) {
            $ruta = $uploadDir . basename($cfgActual[$field]);
            if (is_file($ruta)) unlink($ruta);
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
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        actualizarLogoCentro($field, $filename);
    }
}

registrarAccion('actualizar', 'configuracion', null, 'Configuración del centro guardada');
if (isset($_POST['onboarding'])) {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'msg' => 'Configuración guardada correctamente.']);
    exit;
}
$_SESSION['exito'] = "Configuración guardada correctamente.";
header("Location: ../../../vistas/admin/configuracion/configuracion.php");
exit;
