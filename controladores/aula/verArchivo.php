<?php
// Sirve un recurso del aula para visualización (inline) o descarga (attachment).
// Registra el acceso del estudiante para control de lectura y estadísticas de uso.
require_once __DIR__ . "/../../include/Security.php"; // enforces session fingerprint + idle timeout
require_once __DIR__ . "/../../modelos/aula.php";
require_once __DIR__ . "/../../modelos/modulos.php";
require_once __DIR__ . "/../../include/FileServer.php";

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN Y PERMISOS
// ══════════════════════════════════════════════════════════════════════
$idArchivo = intval($_GET['id'] ?? 0);
$modo      = ($_GET['modo'] ?? 'ver') === 'descarga' ? 'descarga' : 'ver';

if ($idArchivo < 1) { http_response_code(400); exit('Petición no válida.'); }

$archivo = obtenerArchivoPorId($idArchivo);
if (!$archivo || $archivo['eliminado']) { http_response_code(404); exit('Archivo no encontrado.'); }

$modulo  = obtenerModuloPorId($archivo['idModulo']);
$idCiclo = $modulo['idCiclo'] ?? 0;

$autorizado = false;
$esEstudiante = false;
if (!empty($_SESSION['idAdmin'])) {
    // Dirección puede supervisar cualquier recurso del aula
    $autorizado = true;
} elseif (!empty($_SESSION['idProfesor'])) {
    // El profesor solo puede acceder a recursos de los módulos que imparte
    $misModulos = listarModulosDeProfesor($_SESSION['idProfesor']);
    if (in_array($archivo['idModulo'], array_column($misModulos, 'idModulo'))) {
        $autorizado = true;
    }
} elseif (!empty($_SESSION['idEstudiante'])) {
    // El estudiante solo puede acceder a recursos del ciclo en el que está matriculado
    require_once __DIR__ . "/../../modelos/estudiantes.php";
    $datos = obtenerEstudiantePorId($_SESSION['idEstudiante']);
    if ($datos && $datos['idCiclo'] == $idCiclo) { $autorizado = true; $esEstudiante = true; }
}
if (!$autorizado) { http_response_code(403); exit('No tienes permiso para acceder a este recurso.'); }
if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) { http_response_code(403); exit('Acción bloqueada.'); }

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$uploadDir = realpath(__DIR__ . "/../../public/uploads/aula/archivos");
$candidato = $uploadDir !== false ? $uploadDir . DIRECTORY_SEPARATOR . $archivo['nombreArchivo'] : false;
$ruta      = $candidato !== false ? realpath($candidato) : false;

// El guard de path-traversal solo aplica cuando realpath() SÍ resuelve algo
// (fichero heredado en disco local); que no resuelva ya no es un error — el
// fichero puede ser un objeto nuevo que solo existe en R2 (sin backfill de
// lo que ya había en public/uploads/), y servirArchivo() decide ese caso.
if (!$uploadDir || ($ruta !== false && strpos($ruta, $uploadDir . DIRECTORY_SEPARATOR) !== 0)) {
    http_response_code(404); exit('El fichero ya no existe.');
}

if ($esEstudiante) {
    registrarAccesoArchivoAula($idArchivo, $_SESSION['idEstudiante'], $modo === 'descarga' ? 'descarga' : 'vista');
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
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

// Solo se pueden ver embebidos los formatos que el navegador soporta de forma nativa
$inlineOk    = in_array($ext, ['pdf','txt','csv','jpg','jpeg','png','gif','webp']);
$disposition = ($modo === 'ver' && $inlineOk) ? 'inline' : 'attachment';

servirArchivo($ruta !== false ? $ruta : $candidato, 'aula/archivos/' . $archivo['nombreArchivo'], $archivo['nombreOriginal'], $mime, $disposition === 'inline');
