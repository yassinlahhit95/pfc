<?php
// Sirve un archivo TFG para visualización o descarga.
// Verifica permisos de Estudiante, Profesor, Secretaría o Admin.
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../modelos/tfg.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";

$idEstudianteReq = intval($_GET['id'] ?? 0);
$modo            = ($_GET['modo'] ?? 'ver') === 'descarga' ? 'descarga' : 'ver';

if ($idEstudianteReq < 1) {
    http_response_code(400); exit('Petición no válida.');
}

$tfg = obtenerTFGporEstudiante($idEstudianteReq);
if (!$tfg || empty($tfg['archivoTFG'])) {
    http_response_code(404); exit('El archivo TFG no existe o no ha sido subido.');
}

$autorizado = false;

if (!empty($_SESSION['idAdmin']) || !empty($_SESSION['idSecretaria'])) {
    $autorizado = true;
} elseif (!empty($_SESSION['idProfesor'])) {
    // Un profesor puede ver TFGs de sus ciclos
    $autorizado = true;
} elseif (!empty($_SESSION['idEstudiante'])) {
    if ($_SESSION['idEstudiante'] == $idEstudianteReq) {
        $autorizado = true;
    }
}

if (!$autorizado) {
    http_response_code(403); exit('No tienes permiso para acceder a este documento.');
}

if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    http_response_code(403); exit('Acción bloqueada.');
}

$uploadDir = realpath(__DIR__ . "/../../public/uploads/pfc");
$ruta      = ($uploadDir !== false)
    ? realpath($uploadDir . DIRECTORY_SEPARATOR . $tfg['archivoTFG'])
    : false;

if (!$ruta || !$uploadDir || strpos($ruta, $uploadDir . DIRECTORY_SEPARATOR) !== 0 || !is_file($ruta)) {
    http_response_code(404); exit('El fichero físico no se encuentra en el servidor.');
}

$ext  = strtolower(pathinfo($tfg['archivoTFG'], PATHINFO_EXTENSION));
$mimes = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'zip'  => 'application/zip',
    'rar'  => 'application/vnd.rar',
];
$mime = $mimes[$ext] ?? 'application/octet-stream';

$inlineOk    = in_array($ext, ['pdf']);
$disposition = ($modo === 'ver' && $inlineOk) ? 'inline' : 'attachment';

header("Content-Type: $mime");
header("Content-Length: " . filesize($ruta));
header("Content-Disposition: $disposition; filename=\"" . rawurlencode($tfg['archivoTFG']) . "\"");
header("X-Content-Type-Options: nosniff");
readfile($ruta);
exit;
