<?php
require_once __DIR__ . '/../include/db.php';
require_once __DIR__ . '/../include/Logger.php';
require_once __DIR__ . '/../config/Config.php';

$config = Config::getInstance();

$saasAdminUrl = $config->get('SAAS_ADMIN_URL', '');
// Mismo secreto de conexión que usa la autenticación HMAC entrante de admin.php
// (ADMIN_API_KEY/ADMIN_API_SECRET) — verifyLicense() de saas-admin lo busca por
// api_key en su propia tabla `connections`, así que solo hay un par de claves
// compartido por conexión, no uno distinto para cada sentido de la relación API.
$apiKey       = $config->get('ADMIN_API_KEY', '');
$apiSecret    = $config->get('ADMIN_API_SECRET', '');

if ($saasAdminUrl === '' || $apiKey === '' || $apiSecret === '') {
    Logger::error('SAAS_SYNC_ERROR', 'SAAS_ADMIN_URL/ADMIN_API_KEY/ADMIN_API_SECRET no configurados en .env');
    echo "Fallo al sincronizar: credenciales no configuradas.\n";
    exit(1);
}

// Asumimos que el host local es pfc.test
$domain = $_SERVER['HTTP_HOST'] ?? 'pfc.test';

$url = $saasAdminUrl . '/api/v1/license/verify?domain=' . urlencode($domain);

// /api/v1/license/verify de saas-admin exige el mismo esquema HMAC que usa su
// propio Connector de salida: X-API-Key/X-Timestamp/X-Signature sobre "GET|timestamp|".
$timestamp = time();
$signature = hash_hmac('sha256', 'GET|' . $timestamp . '|', $apiSecret);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-API-Key: ' . $apiKey,
    'X-Timestamp: ' . $timestamp,
    'X-Signature: ' . $signature,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && $response) {
    $data = json_decode($response, true);
    if (isset($data['max_storage_gb'])) {
        $max_gb = (int)$data['max_storage_gb'];
        $tenantPrefix = $config->get('R2_TENANT_PREFIX', 'default');

        // Lo guardamos en APCu para que sea ultra rápido leerlo en cada subida
        // (clave prefijada por tenant — ver FeatureGuard/R2Client para el mismo patrón)
        if (function_exists('apcu_store')) {
            apcu_store("saas_max_storage_gb:{$tenantPrefix}", $max_gb, 86400); // 24h
        }

        Logger::info('SAAS_SYNC', "Límite de almacenamiento sincronizado: {$max_gb} GB");
        echo "Sincronización exitosa: {$max_gb} GB\n";
    }
} else {
    Logger::error('SAAS_SYNC_ERROR', "Fallo al conectar con SAAS Admin. Código: {$httpCode}");
    echo "Fallo al sincronizar.\n";
}
