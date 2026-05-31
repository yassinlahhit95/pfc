<?php
// Acceso público a un recurso mediante enlace temporal (#14).
// No requiere sesión: la validez la garantiza el token (activo + no caducado).
require_once __DIR__ . "/../../modelos/aula.php";

$token = preg_replace('/[^a-f0-9]/', '', $_GET['token'] ?? '');
$modo  = ($_GET['modo'] ?? 'ver') === 'descarga' ? 'descarga' : 'ver';

if (strlen($token) < 16) { http_response_code(400); exit('Enlace no válido.'); }

$enlace = obtenerEnlaceValidoPorToken($token);
if (!$enlace) { http_response_code(410); exit('Este enlace ha caducado o ya no está disponible.'); }

// Respetar la restricción de descarga del enlace
if ($modo === 'descarga' && !$enlace['permitirDescarga']) {
    http_response_code(403); exit('La descarga de este recurso está deshabilitada.');
}

$ruta = __DIR__ . "/../../public/uploads/aula/archivos/" . $enlace['nombreArchivo'];
if (!file_exists($ruta)) { http_response_code(404); exit('El fichero ya no existe.'); }

$mimes = [
    'pdf'  => 'application/pdf',
    'doc'  => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls'  => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'ppt'  => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'txt'  => 'text/plain', 'csv' => 'text/csv', 'rtf' => 'application/rtf',
    'jpg'  => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
    'gif'  => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
    'zip'  => 'application/zip', 'rar' => 'application/vnd.rar',
];
$ext  = strtolower($enlace['extension']);
$mime = $mimes[$ext] ?? 'application/octet-stream';
$inlineOk = in_array($ext, ['pdf','txt','csv','jpg','jpeg','png','gif','webp','svg']);
$disposition = ($modo === 'ver' && $inlineOk) ? 'inline' : 'attachment';

header("Content-Type: $mime");
header("Content-Length: " . filesize($ruta));
header("Content-Disposition: $disposition; filename=\"" . rawurlencode($enlace['nombreOriginal']) . "\"");
header("X-Content-Type-Options: nosniff");
readfile($ruta);
exit;
