<?php
// Sirve un recurso del aula para visualización (inline) o descarga (attachment).
// Registra el acceso cuando quien lo abre es un estudiante (control de lectura
// y estadísticas de uso). Profesores y estudiantes autenticados con permiso.
session_start();
require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../modelos/modulos.php";

$idArchivo = intval($_GET['id'] ?? 0);
$modo      = ($_GET['modo'] ?? 'ver') === 'descarga' ? 'descarga' : 'ver';

if ($idArchivo < 1) { http_response_code(400); exit('Petición no válida.'); }

$archivo = obtenerArchivoPorId($idArchivo);
if (!$archivo || $archivo['eliminado']) { http_response_code(404); exit('Archivo no encontrado.'); }

$modulo  = obtenerModuloPorId($archivo['idModulo']);
$idCiclo = $modulo['idCiclo'] ?? 0;

// ── Permisos ──────────────────────────────────────────────
$autorizado = false;
$esEstudiante = false;
if (!empty($_SESSION['idProfesor'])) {
    // El profesor solo puede acceder a recursos de los módulos que imparte
    $misModulos = listarModulosDeProfesor($_SESSION['idProfesor']);
    if (in_array($archivo['idModulo'], array_column($misModulos, 'idModulo'))) {
        $autorizado = true;
    }
} elseif (!empty($_SESSION['idEstudiante'])) {
    // El estudiante sólo recursos de su propio ciclo
    require_once __DIR__ . "/../../modelos/estudiantes.php";
    $datos = obtenerEstudiantePorId($_SESSION['idEstudiante']);
    if ($datos && $datos['idCiclo'] == $idCiclo) { $autorizado = true; $esEstudiante = true; }
}
if (!$autorizado) { http_response_code(403); exit('No tienes permiso para acceder a este recurso.'); }

// ── Localizar el fichero físico ───────────────────────────
$ruta = __DIR__ . "/../../public/uploads/aula/archivos/" . $archivo['nombreArchivo'];
if (!file_exists($ruta)) { http_response_code(404); exit('El fichero ya no existe.'); }

// ── Registrar acceso del estudiante ───────────────────────
if ($esEstudiante) {
    registrarAccesoArchivoAula($idArchivo, $_SESSION['idEstudiante'], $modo === 'descarga' ? 'descarga' : 'vista');
}

// ── Cabeceras y envío ─────────────────────────────────────
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
$ext  = strtolower($archivo['extension']);
$mime = $mimes[$ext] ?? 'application/octet-stream';

// Sólo se pueden ver embebidos los formatos que el navegador soporta
$inlineOk = in_array($ext, ['pdf','txt','csv','jpg','jpeg','png','gif','webp','svg']);
$disposition = ($modo === 'ver' && $inlineOk) ? 'inline' : 'attachment';

$nombreDescarga = $archivo['nombreOriginal'];

header("Content-Type: $mime");
header("Content-Length: " . filesize($ruta));
header("Content-Disposition: $disposition; filename=\"" . rawurlencode($nombreDescarga) . "\"");
header("X-Content-Type-Options: nosniff");
readfile($ruta);
exit;
