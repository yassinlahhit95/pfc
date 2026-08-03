<?php
// AJAX-only, GET — current R2 bucket storage usage for the "Almacenamiento"
// card in vistas/admin/saas/estado.php. Polled client-side every ~30s for a
// live progress bar. Cached 60s (Cache::remember) so the poll never hammers
// R2's ListObjectsV2 API on every request — matches the pattern already used
// for dashboard counters (modelos/panelDeControl.php) and unread badges.
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/R2Client.php';
require_once __DIR__ . '/../../../include/Cache.php';
require_once __DIR__ . '/../../../config/Config.php';

header('Content-Type: application/json; charset=utf-8');

// 10 GB — Cloudflare R2's free-tier storage limit (noDeploy/CLOUDFLARE_R2_SETUP.md).
// Used only as a fallback when this tenant has no SaaS-assigned plan quota cached
// yet (e.g. cron/sync_saas_license.php hasn't run since the last APCu clear).
const R2_FREE_TIER_LIMIT_BYTES = 10 * 1024 * 1024 * 1024;

try {
    $usage = Cache::remember('r2_storage_usage', 60, fn() => R2Client::totalUsage());

    // Same tenant-prefixed key cron/sync_saas_license.php writes after asking
    // saas-admin for this client's actual plan quota — without this, every
    // client saw the flat 10GB free-tier number regardless of their real plan
    // (e.g. a client assigned 1GB by SaaS would still see "X / 10 GB" here).
    $tenantPrefix = Config::getInstance()->get('R2_TENANT_PREFIX', 'default');
    $planLimitGb  = function_exists('apcu_fetch') ? apcu_fetch("saas_max_storage_gb:{$tenantPrefix}") : false;
    $limitBytes   = ($planLimitGb !== false && $planLimitGb > 0)
        ? (int)$planLimitGb * 1024 * 1024 * 1024
        : R2_FREE_TIER_LIMIT_BYTES;

    echo json_encode([
        'ok'          => true,
        'bytes'       => $usage['bytes'],
        'objectCount' => $usage['objectCount'],
        'limitBytes'  => $limitBytes,
        'percent'     => round(min(100, $usage['bytes'] / $limitBytes * 100), 2),
    ]);
} catch (Throwable $e) {
    // R2 not configured (.env empty) — not an error state for this widget,
    // just "no data yet" so the UI can show a neutral message instead of a
    // scary failure.
    echo json_encode(['ok' => false, 'configured' => false]);
}
