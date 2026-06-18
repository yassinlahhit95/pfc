<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../modelos/configuracion.php';

$campos = ['nombreCentro','codigoCentro','direccionCentro','ciudadCentro',
           'cpCentro','telefonoCentro','emailCentro','cursoEscolar',
           'textoLegal','nombreDirectorFirmante'];
$datos = [];
foreach ($campos as $c) {
    $datos[$c] = trim($_POST[$c] ?? '');
}

if (empty($datos['nombreCentro'])) {
    $_SESSION['errores'] = "El nombre del centro es obligatorio.";
    header("Location: ../../../vistas/admin/configuracion/configuracion.php"); exit;
}
if ($datos['emailCentro'] && !Security::validateEmail($datos['emailCentro'])) {
    $_SESSION['errores'] = "El email del centro no es válido.";
    header("Location: ../../../vistas/admin/configuracion/configuracion.php"); exit;
}
if ($datos['telefonoCentro'] && !Security::validatePhone($datos['telefonoCentro'])) {
    $_SESSION['errores'] = "El teléfono del centro no es válido.";
    header("Location: ../../../vistas/admin/configuracion/configuracion.php"); exit;
}

guardarConfiguracionCentro($datos);

$logoFields = ['logoCentro', 'logoGobierno1', 'logoGobierno2'];
$uploadDir  = __DIR__ . '/../../../public/uploads/configuracion/';
$mimeExtMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

foreach ($logoFields as $field) {
    if (!empty($_POST['borrar_' . $field])) {
        $cfg = obtenerConfiguracionCentro();
        if (!empty($cfg[$field])) {
            $ruta = $uploadDir . basename($cfg[$field]);
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
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        actualizarLogoCentro($field, $filename);
    }
}

$_SESSION['exito'] = "Configuración guardada correctamente.";
header("Location: ../../../vistas/admin/configuracion/configuracion.php");
