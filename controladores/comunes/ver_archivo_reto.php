<?php
// ══════════════════════════════════════════════════════════════════════
// Sirve UN archivo individual de un reto (public/uploads/retos/ está
// bloqueado a acceso directo por su .htaccess — "Require all denied" — y
// hasta ahora solo descargar_zip_reto.php sabía servir a través de ese
// bloqueo, para el ZIP con todos los materiales). Los enlaces por archivo
// individual (lista.php de profesor/estudiante, verRetos.php de admin)
// apuntaban directamente a la ruta local bloqueada → 403 siempre, exista
// el fichero en disco o solo en R2. Misma lógica de autorización que
// descargar_zip_reto.php, reutilizada tal cual.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . "/../../modelos/retos.php";
require_once __DIR__ . "/../../include/FileServer.php";

if (session_status() === PHP_SESSION_NONE) session_start();

$idArchivo = (int)($_GET['id'] ?? 0);
if ($idArchivo < 1) { http_response_code(400); exit("Petición no válida."); }

$archivo = obtenerArchivoRetoPorId($idArchivo);
if (!$archivo) { http_response_code(404); exit("Archivo no encontrado."); }

$idReto = (int)$archivo['idReto'];
$reto   = obtenerRetoPorId($idReto);
if (!$reto) { http_response_code(404); exit("Reto no encontrado."); }

if (!empty($_SESSION['must_change_password']) || !empty($_SESSION['mfa_setup_required'])) {
    http_response_code(403); exit("Acción bloqueada.");
}

$autorizado = false;
if (!empty($_SESSION['idAdmin'])) {
    $autorizado = true;
} elseif (!empty($_SESSION['idProfesor'])) {
    $retosProf = listarRetosDeProfesor($_SESSION['idProfesor']);
    $autorizado = in_array($idReto, array_column($retosProf, 'idReto'));
} elseif (!empty($_SESSION['idEstudiante'])) {
    require_once __DIR__ . "/../../modelos/estudiantes.php";
    $est = obtenerEstudiantePorId($_SESSION['idEstudiante']);
    if ($est) {
        $retosCiclo = listarRetosPorCiclo($est['idCiclo']);
        $autorizado = in_array($idReto, array_column($retosCiclo, 'idReto'));
    }
}
if (!$autorizado) { http_response_code(403); exit("No tienes permiso para ver este material."); }

session_write_close();

$baseDir  = realpath(__DIR__ . "/../../public/uploads");
$filePath = realpath(__DIR__ . "/../../" . ltrim($archivo['rutaArchivo'], '/'));
// Contención: solo se sirve si cae dentro de /public/uploads (evita que una
// rutaArchivo corrupta/manipulada sirva un fichero arbitrario del servidor).
$existeLocal = $filePath && $baseDir && strpos($filePath, $baseDir . DIRECTORY_SEPARATOR) === 0 && is_file($filePath);

$mimes = [
    'pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
    'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
];
$ext  = strtolower(pathinfo($archivo['nombreArchivo'], PATHINFO_EXTENSION));
$mime = $mimes[$ext] ?? 'application/octet-stream';

$r2Key = ltrim(preg_replace('#^public/uploads/#', '', $archivo['rutaArchivo']), '/');
$inline = ($_GET['modo'] ?? '') !== 'descarga';

servirArchivo($existeLocal ? $filePath : '', $r2Key, $archivo['nombreArchivo'], $mime, $inline);
