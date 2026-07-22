<?php
// Sirve un archivo TFG para visualizaci�n o descarga.
// Verifica permisos de Estudiante, Profesor, Secretar�a o Admin.
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../modelos/tfg.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../include/FileServer.php";

$idEstudianteReq = intval($_GET['id'] ?? 0);
$modo            = ($_GET['modo'] ?? 'ver') === 'descarga' ? 'descarga' : 'ver';

if ($idEstudianteReq < 1) {
    http_response_code(400); exit('Petici�n no v�lida.');
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
} elseif (!empty($_SESSION['idTutor'])) {
    // Un tutor legal puede ver el TFG de sus hijos vinculados
    require_once __DIR__ . "/../../modelos/tutores.php";
    foreach (listarEstudiantesPorTutor($_SESSION['idTutor']) as $hijo) {
        if ((int)$hijo['idEstudiante'] === $idEstudianteReq) { $autorizado = true; break; }
    }
}

if (!$autorizado) {
    http_response_code(403); exit('No tienes permiso para acceder a este documento.');
}

if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    http_response_code(403); exit('Acci�n bloqueada.');
}

$uploadDir = realpath(__DIR__ . "/../../public/uploads/pfc");
$candidato = $uploadDir !== false ? $uploadDir . DIRECTORY_SEPARATOR . $tfg['archivoTFG'] : false;
$ruta      = $candidato !== false ? realpath($candidato) : false;

// El guard de path-traversal solo aplica cuando realpath() SÍ resuelve algo
// (fichero heredado en disco local); que no resuelva ya no es un error — el
// fichero puede ser un objeto nuevo que solo existe en R2 (sin backfill de
// lo que ya había en public/uploads/), y servirArchivo() decide ese caso.
if (!$uploadDir || ($ruta !== false && strpos($ruta, $uploadDir . DIRECTORY_SEPARATOR) !== 0)) {
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

servirArchivo($ruta !== false ? $ruta : $candidato, 'pfc/' . $tfg['archivoTFG'], $tfg['archivoTFG'], $mime, $disposition === 'inline');
