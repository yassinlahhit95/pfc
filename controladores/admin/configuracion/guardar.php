<?php
require_once __DIR__ . '/../../../include/Security.php';
if (empty($_SESSION['idAdmin'])) { header("Location: ../../../vistas/login.php"); exit; }

require_once __DIR__ . '/../../../modelos/configuracion.php';

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud inválida.";
    header("Location: ../../../vistas/admin/configuracion/configuracion.php"); exit;
}

$campos = ['nombreCentro','codigoCentro','direccionCentro','ciudadCentro',
           'cpCentro','telefonoCentro','emailCentro','cursoEscolar',
           'textoLegal','nombreDirectorFirmante'];
$datos = [];
foreach ($campos as $c) {
    $datos[$c] = trim($_POST[$c] ?? '');
}

guardarConfiguracionCentro($datos);

// Handle logo uploads
$logoFields = ['logoCentro', 'logoGobierno1', 'logoGobierno2'];
$uploadDir  = __DIR__ . '/../../../public/uploads/configuracion/';
$allowed    = ['image/jpeg','image/png','image/svg+xml','image/webp'];

foreach ($logoFields as $field) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) continue;
    $file = $_FILES[$field];
    if (!in_array(mime_content_type($file['tmp_name']), $allowed)) continue;
    if ($file['size'] > 2 * 1024 * 1024) continue;
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $field . '_' . time() . '.' . $ext;
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        actualizarLogoCentro($field, $filename);
    }
}

$_SESSION['exito'] = "Configuración guardada correctamente.";
header("Location: ../../../vistas/admin/configuracion/configuracion.php");
