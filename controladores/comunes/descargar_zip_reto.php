<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . "/../../modelos/retos.php";

if (session_status() === PHP_SESSION_NONE) session_start();

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════
$idReto = (int)($_GET['id'] ?? 0);
if ($idReto < 1) { http_response_code(400); exit("Petición no válida."); }

$reto = obtenerRetoPorId($idReto);
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
if (!$autorizado) { http_response_code(403); exit("No tienes permiso para descargar este material."); }

// Liberar el bloqueo de sesión de PHP para permitir al usuario navegar en otras pestañas
session_write_close();

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
$archivos = obtenerArchivosReto($idReto);
if (empty($archivos)) { http_response_code(404); exit("Este reto no tiene archivos para descargar."); }

$baseDir = realpath(__DIR__ . "/../../public/uploads");
$zip     = new ZipArchive();
$zipName = "Materiales_Reto_" . preg_replace('/[^A-Za-z0-9]/', '_', $reto['nombreReto']) . ".zip";
$zipPath = sys_get_temp_dir() . '/' . bin2hex(random_bytes(8)) . '.zip';

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    foreach ($archivos as $arch) {
        $filePath = realpath(__DIR__ . "/../../" . ltrim($arch['rutaArchivo'], '/'));
        // Contención: solo se incluyen ficheros dentro de /public/uploads
        if ($filePath && $baseDir && strpos($filePath, $baseDir . DIRECTORY_SEPARATOR) === 0 && is_file($filePath)) {
            $zip->addFile($filePath, $arch['nombreArchivo']);
        }
    }
    $zip->close();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipName . '"');
    header('Content-Length: ' . filesize($zipPath));
    header('X-Content-Type-Options: nosniff');
    readfile($zipPath);
    unlink($zipPath);
    exit;
} else {
    http_response_code(500);
    die("No se pudo crear el archivo ZIP.");
}
