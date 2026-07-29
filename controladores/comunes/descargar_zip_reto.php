<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . "/../../modelos/retos.php";
require_once __DIR__ . "/../../include/FileServer.php";
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
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

$baseDir  = realpath(__DIR__ . "/../../public/uploads");
$zipName  = "Materiales_Reto_" . preg_replace('/[^A-Za-z0-9]/', '_', $reto['nombreReto']) . ".zip";

// ══════════════════════════════════════════════════════════════════════
// CACHÉ DEL ZIP: se reconstruye solo si cambian los ficheros del reto
// (el nombre incluye un hash de ruta+mtime de cada fichero), en vez de
// recrearlo en cada descarga. Vive dentro de public/uploads/retos/, que
// tiene un .htaccess con "Require all denied" heredado por subcarpetas,
// así que no es descargable salvo a través de este controlador.
// ══════════════════════════════════════════════════════════════════════
$cacheDir = $baseDir . '/retos/_cache_zip';
if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);

$firma = md5(implode('|', array_map(function ($a) {
    $p = realpath(__DIR__ . "/../../" . ltrim($a['rutaArchivo'], '/'));
    return $a['rutaArchivo'] . '_' . ($p ? filemtime($p) : 0);
}, $archivos)));
$zipPath = $cacheDir . "/reto_{$idReto}_{$firma}.zip";

if (!is_file($zipPath)) {
    // Limpia versiones cacheadas antiguas de este mismo reto (firma distinta).
    foreach (glob($cacheDir . "/reto_{$idReto}_*.zip") ?: [] as $antiguo) {
        @unlink($antiguo);
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $tmpDescargados = [];
        foreach ($archivos as $arch) {
            $filePath = realpath(__DIR__ . "/../../" . ltrim($arch['rutaArchivo'], '/'));
            // Contención: solo se incluyen ficheros dentro de /public/uploads
            if ($filePath && $baseDir && strpos($filePath, $baseDir . DIRECTORY_SEPARATOR) === 0 && is_file($filePath)) {
                $zip->addFile($filePath, $arch['nombreArchivo']);
                continue;
            }

            // No está en disco local — puede ser un fichero nuevo que solo
            // existe en R2 (sin backfill de lo anterior a la migración).
            // ZipArchive::addFile() necesita una ruta local real, así que se
            // descarga a un temporal vía una URL firmada de muy corta
            // duración (llamada servidor→R2, no expuesta al navegador).
            require_once __DIR__ . "/../../include/R2Client.php";
            $r2Key = ltrim(preg_replace('#^public/uploads/#', '', $arch['rutaArchivo']), '/');
            try {
                $url = R2Client::presignedGetUrl($r2Key, 60);
                $h = curl_init($url);
                curl_setopt($h, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($h, CURLOPT_TIMEOUT, 30);
                $bytes = curl_exec($h);
                $code  = curl_getinfo($h, CURLINFO_HTTP_CODE);
                curl_close($h);
                if ($bytes !== false && $code === 200) {
                    $tmpPath = $cacheDir . '/_tmp_' . bin2hex(random_bytes(8));
                    file_put_contents($tmpPath, $bytes);
                    $zip->addFile($tmpPath, $arch['nombreArchivo']);
                    $tmpDescargados[] = $tmpPath;
                }
            } catch (\Throwable $e) {
                // Fichero no disponible en ningún lado — se omite del ZIP en vez de romper la descarga completa.
            }
        }
        $zip->close();
        foreach ($tmpDescargados as $tmpPath) { @unlink($tmpPath); }
    } else {
        http_response_code(500);
        die("No se pudo crear el archivo ZIP.");
    }
}

servirArchivo($zipPath, '', $zipName, 'application/zip');
