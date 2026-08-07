<?php
// Solo AJAX, GET — uso actual del almacenamiento del bucket R2 para la tarjeta
// "Almacenamiento" de vistas/admin/saas/estado.php. Se consulta desde el cliente
// cada ~30s para una barra de progreso en vivo. Cacheado 60s (Cache::remember)
// para que el sondeo no sature la API ListObjectsV2 de R2 en cada petición —
// mismo patrón ya usado para los contadores del dashboard (modelos/panelDeControl.php)
// y los badges de no leídos.
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/R2Client.php';
require_once __DIR__ . '/../../../include/Cache.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';

header('Content-Type: application/json; charset=utf-8');

// 10 GB — límite de almacenamiento del nivel gratuito de Cloudflare R2 (noDeploy/CLOUDFLARE_R2_SETUP.md).
// Solo se usa como respaldo cuando no hay token de licencia válido (periodo de
// gracia) o el plan asignado no trae cuota propia.
const R2_FREE_TIER_LIMIT_BYTES = 10 * 1024 * 1024 * 1024;

try {
    $usage = Cache::remember('r2_storage_usage', 60, fn() => R2Client::totalUsage());

    // Cuota real del plan del cliente, embebida en el token de licencia firmado
    // por saas-admin (mismo canal que features/lock/status — se actualiza al
    // instante en cada heartbeat, no depende de un cron aparte).
    $planLimitGb = FeatureGuard::getMaxStorageGb();
    $limitBytes  = $planLimitGb !== null && $planLimitGb > 0
        ? $planLimitGb * 1024 * 1024 * 1024
        : R2_FREE_TIER_LIMIT_BYTES;

    echo json_encode([
        'ok'          => true,
        'bytes'       => $usage['bytes'],
        'objectCount' => $usage['objectCount'],
        'limitBytes'  => $limitBytes,
        'percent'     => round(min(100, $usage['bytes'] / $limitBytes * 100), 2),
    ]);
} catch (Throwable $e) {
    // R2 no configurado (.env vacío) — para este widget no es un estado de
    // error, solo "todavía no hay datos", así la UI puede mostrar un mensaje
    // neutro en vez de un fallo alarmante.
    echo json_encode(['ok' => false, 'configured' => false]);
}
