<?php
// ══════════════════════════════════════════════════════════════════════
// BACKFILL DE OPTIMIZACIÓN DE IMÁGENES — ejecutar una vez tras desplegar
// ══════════════════════════════════════════════════════════════════════
// Redimensiona/recomprime (vía ImageOptimizer) las imágenes que ya estaban
// subidas en public/uploads/landing y public/uploads/blog ANTES de que se
// añadiera la optimización automática al subir. Idempotente: si una imagen
// ya está por debajo del límite de tamaño, ImageOptimizer no la toca
// (solo reescribe si el lado largo supera el máximo).
// Acceso: solo CLI o sesión de administrador (nunca público).
header('Content-Type: text/plain; charset=utf-8');

require_once __DIR__ . '/include/Security.php';
require_once __DIR__ . '/include/ImageOptimizer.php';

$esCli = php_sapi_name() === 'cli';
if (!$esCli && empty($_SESSION['idAdmin'])) {
    http_response_code(403);
    exit("403 — Solo un administrador con sesión iniciada puede ejecutar este script.\n");
}

$mimeExtMap = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
$dirs = [
    __DIR__ . '/public/uploads/landing',
    __DIR__ . '/public/uploads/blog',
];

$totalAntes = 0;
$totalDespues = 0;
$procesados = 0;

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        echo "(omitido, no existe): $dir\n";
        continue;
    }
    foreach (scandir($dir) as $nombre) {
        $ruta = $dir . DIRECTORY_SEPARATOR . $nombre;
        if (!is_file($ruta)) continue;
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        if (!isset($mimeExtMap[$ext])) continue;

        $antes = filesize($ruta);
        ImageOptimizer::optimize($ruta, $mimeExtMap[$ext]);
        clearstatcache(true, $ruta);
        $despues = filesize($ruta);

        $totalAntes   += $antes;
        $totalDespues += $despues;
        $procesados++;

        printf("%-70s %8d -> %8d bytes (%+.0f%%)\n",
            $nombre, $antes, $despues,
            $antes > 0 ? (($despues - $antes) / $antes) * 100 : 0);
    }
}

printf("\n%d imágenes procesadas. Total: %d -> %d bytes (%.0f%% menos)\n",
    $procesados, $totalAntes, $totalDespues,
    $totalAntes > 0 ? ((1 - $totalDespues / $totalAntes) * 100) : 0);
