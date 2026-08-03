<?php
declare(strict_types=1);

// ── Bootstrap ──────────────────────────────────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store, no-cache');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function apiEnv(string $key, string $default = ''): string {
    static $env = null;
    if ($env === null) {
        $env  = [];
        $path = dirname(__DIR__) . '/.env';
        if (!file_exists($path)) return $default;
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if (!$line || $line[0] === '#' || !str_contains($line, '=')) continue;
            [$k, $v] = explode('=', $line, 2);
            $env[trim($k)] = trim($v, " \t\n\r\0\x0B\"'");
        }
    }
    return $env[$key] ?? $default;
}

function apiError(string $msg, int $code = 400): never {
    http_response_code($code);
    echo json_encode(['error' => $msg]);
    exit;
}

function apiOk(array $payload): never {
    echo json_encode(array_merge(['status' => 'ok', 'timestamp' => date('c')], $payload));
    exit;
}

// ── Authentication ─────────────────────────────────────────────────────────────
$configuredKey    = apiEnv('ADMIN_API_KEY');
$configuredSecret = apiEnv('ADMIN_API_SECRET');

if (!$configuredKey || !$configuredSecret) {
    apiError('API not configured on this server.', 503);
}

$incomingKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
$timestamp   = (int)($_SERVER['HTTP_X_TIMESTAMP'] ?? 0);
$signature   = $_SERVER['HTTP_X_SIGNATURE'] ?? '';

if (!$incomingKey || !$timestamp || !$signature) {
    apiError('Missing authentication headers.', 401);
}

if (!hash_equals($configuredKey, $incomingKey)) {
    apiError('Invalid API key.', 401);
}

// Reject requests older than 5 minutes (replay protection)
if (abs(time() - $timestamp) > 300) {
    apiError('Request timestamp expired. Verify server clocks are in sync.', 401);
}

$rawBody  = file_get_contents('php://input') ?: '';
$method   = $_SERVER['REQUEST_METHOD'];
$expected = hash_hmac('sha256', $method . '|' . $timestamp . '|' . $rawBody, $configuredSecret);

if (!hash_equals($expected, $signature)) {
    apiError('Invalid request signature.', 401);
}

// ── Database ───────────────────────────────────────────────────────────────────
try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        apiEnv('DB_HOST', 'localhost'),
        apiEnv('DB_PORT', '3306'),
        apiEnv('DB_NAME', 'pfc')
    );
    $pdo = new PDO($dsn, apiEnv('DB_USER', 'root'), apiEnv('DB_PASS', ''), [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException) {
    apiError('Database connection failed.', 503);
}

require_once dirname(__DIR__) . '/config/Config.php';
require_once dirname(__DIR__) . '/include/R2Client.php';

// ── Parse action ───────────────────────────────────────────────────────────────
$payload = $rawBody ? (json_decode($rawBody, true) ?? []) : [];
$action  = ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($payload['action'])) ? $payload['action'] : ($_GET['action'] ?? '');

// ── License token helper ────────────────────────────────────────────────────────
function storeLicenseToken(PDO $pdo, string $token): bool {
    if (!$token) return false;
    $parts = explode('.', $token, 2);
    $expDatetime = null;
    if (count($parts) === 2) {
        $pl = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
        if (isset($pl['exp'])) {
            $expDatetime = date('Y-m-d H:i:s', (int)$pl['exp']);
        }
    }
    try {
        $pdo->prepare(
            "UPDATE configuracion_centro SET license_token = ?, license_token_exp = ?, saas_last_sync = NOW() WHERE idConfig = 1"
        )->execute([$token, $expDatetime]);
        return true;
    } catch (PDOException) {
        // license_token column missing — this DB predates it and needs its schema
        // brought up to date against the current noDeploy/database.sql
        return false;
    }
}

// Store license token if present in any request (best-effort; won't crash if column missing)
$licenseToken = trim($payload['license_token'] ?? '');
$licenseTokenStored = false;
if ($licenseToken) {
    $licenseTokenStored = storeLicenseToken($pdo, $licenseToken);
}

// ── Route ──────────────────────────────────────────────────────────────────────
switch ($action) {

    // GET /api/admin.php?action=health
    case 'health':
        try { $pdo->query('SELECT 1'); $db = 'connected'; } catch (PDOException) { $db = 'error'; }
        apiOk([
            'app'     => 'AulaPro',
            'db'      => $db,
            'version' => '1.0',
        ]);

    // GET /api/admin.php?action=stats
    case 'stats':
        $tables = [
            'students'   => 'estudiantes',
            'teachers'   => 'profesores',
            'admins'     => 'directores',
            'modules'    => 'modulos',
            'cycles'     => 'ciclos',
            'challenges' => 'retos',
        ];
        $counts = [];
        foreach ($tables as $key => $table) {
            try {
                $counts[$key] = (int) $pdo->query("SELECT COUNT(*) FROM `{$table}`")->fetchColumn();
            } catch (PDOException) {
                $counts[$key] = null;
            }
        }
        apiOk(['stats' => $counts]);

    // GET /api/admin.php?action=features
    case 'features':
        $cfg = $pdo->query('SELECT * FROM configuracion_centro LIMIT 1')->fetch() ?: [];
        $features = [];
        foreach ($cfg as $col => $val) {
            if (str_starts_with($col, 'feature_')) {
                $features[$col] = (bool)(int)$val;
            }
        }
        apiOk([
            'features'    => $features,
            'center_name' => $cfg['nombreCentro'] ?? '',
            'school_year' => $cfg['cursoEscolar'] ?? '',
        ]);

    // GET /api/admin.php?action=config
    case 'config':
        $cfg = $pdo->query(
            'SELECT nombreCentro, codigoCentro, cursoEscolar, emailCentro, telefonoCentro, colorAcento, logoCentro FROM configuracion_centro LIMIT 1'
        )->fetch() ?: [];
        apiOk(['config' => $cfg]);

    // POST — body: {"action":"set_feature","feature":"feature_chat","value":true}
    case 'set_feature':
        $feature = trim($payload['feature'] ?? '');
        $value   = isset($payload['value']) ? (bool)$payload['value'] : null;

        if (!$feature || $value === null) {
            apiError('Missing feature or value.', 400);
        }

        // Allow only safe column names
        if (!preg_match('/^feature_[a-z_]+$/', $feature)) {
            apiError('Invalid feature name.', 400);
        }

        // Confirm the column actually exists in the table
        $exists = $pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
        $exists->execute(['configuracion_centro', $feature]);
        if (!(int)$exists->fetchColumn()) {
            apiError("Feature '{$feature}' does not exist.", 404);
        }

        // Column name validated via regex + DB check — safe to interpolate
        $pdo->prepare("UPDATE configuracion_centro SET `{$feature}` = ? WHERE idConfig = 1")->execute([(int)$value]);
        apiOk(['feature' => $feature, 'value' => $value]);

    // POST — body: {"action":"suspend","message":"Suscripción expirada."}
    case 'suspend':
        $message = trim($payload['message'] ?? 'Acceso suspendido por el administrador.');
        $pdo->prepare("UPDATE configuracion_centro SET instance_status = 'suspended', suspension_message = ? WHERE idConfig = 1")
            ->execute([$message]);
        apiOk(['instance_status' => 'suspended']);

    // POST — body: {"action":"activate"}
    case 'activate':
        $pdo->prepare("UPDATE configuracion_centro SET instance_status = 'active', suspension_message = NULL WHERE idConfig = 1")
            ->execute([]);
        apiOk(['instance_status' => 'active']);

    // POST — body: {"action":"set_message","message":"Suscripción requerida.","type":"warning"}
    case 'set_message':
        $message = trim($payload['message'] ?? '');
        $type    = trim($payload['type']    ?? 'info');
        $allowed = ['info', 'warning', 'error', 'subscription', 'activation'];
        if (!in_array($type, $allowed, true)) $type = 'info';
        $pdo->prepare("UPDATE configuracion_centro SET saas_message = ?, saas_message_type = ?, saas_last_sync = NOW() WHERE idConfig = 1")
            ->execute([$message ?: null, $type]);
        apiOk(['saas_message' => $message, 'saas_message_type' => $type]);

    // POST — body: {"action":"clear_message"}
    case 'clear_message':
        $pdo->prepare("UPDATE configuracion_centro SET saas_message = NULL, saas_message_type = 'info', saas_last_sync = NOW() WHERE idConfig = 1")
            ->execute([]);
        apiOk(['saas_message' => null]);

    // POST — body: {"action":"lock_features"}
    case 'lock_features':
        $pdo->prepare("UPDATE configuracion_centro SET saas_lock_features = 1, saas_last_sync = NOW() WHERE idConfig = 1")
            ->execute([]);
        apiOk(['saas_lock_features' => true]);

    // POST — body: {"action":"unlock_features"}
    case 'unlock_features':
        $pdo->prepare("UPDATE configuracion_centro SET saas_lock_features = 0, saas_last_sync = NOW() WHERE idConfig = 1")
            ->execute([]);
        apiOk(['saas_lock_features' => false]);

    // GET — returns full SaaS control state
    case 'status':
        $row = $pdo->query("SELECT instance_status, suspension_message, saas_lock_features, saas_message, saas_message_type, saas_last_sync FROM configuracion_centro WHERE idConfig = 1 LIMIT 1")->fetch() ?: [];
        apiOk(['control' => $row]);

    // GET /api/admin.php?action=diagnostics
    case 'diagnostics':
        $checks = [];

        // DB connectivity + latency
        $t0 = microtime(true);
        try {
            $pdo->query('SELECT 1');
            $checks['db'] = ['ok' => true, 'latency_ms' => round((microtime(true) - $t0) * 1000, 1)];
        } catch (PDOException) {
            $checks['db'] = ['ok' => false, 'error' => 'DB unreachable'];
        }

        // Disk space (app root)
        $free  = disk_free_space(dirname(__DIR__));
        $total = disk_total_space(dirname(__DIR__));
        $checks['disk'] = [
            'ok'       => $free !== false && $free > 1_073_741_824,
            'free_gb'  => $free  !== false ? round($free  / 1e9, 1) : null,
            'total_gb' => $total !== false ? round($total / 1e9, 1) : null,
        ];

        // PHP version + required extensions (kept in sync with noDeploy/install-check.php)
        $requiredExt = ['mysqli', 'zip', 'curl', 'openssl', 'mbstring', 'fileinfo'];
        $missingExt  = array_values(array_filter($requiredExt, fn($e) => !extension_loaded($e)));
        $checks['php'] = [
            'ok'          => version_compare(PHP_VERSION, '8.3.0', '>=') && !$missingExt,
            'version'     => PHP_VERSION,
            'missing_ext' => $missingExt,
        ];

        // Storage writable
        $checks['storage_writable'] = [
            'ok'   => is_writable(dirname(__DIR__) . '/public/uploads'),
            'path' => 'public/uploads',
        ];

        // Cron job freshness — same table/threshold as api/v1/admin/cron-health.php
        try {
            $jobs = $pdo->query('SELECT job_name, last_run, last_run_status FROM cron_execution_log')->fetchAll();
            $jobChecks = [];
            foreach ($jobs as $j) {
                $hoursAgo = $j['last_run'] ? (time() - strtotime($j['last_run'])) / 3600 : 9999.0;
                $jobChecks[$j['job_name']] = [
                    'ok'        => $j['last_run_status'] === 'success' && $hoursAgo < 25,
                    'hours_ago' => round($hoursAgo, 1),
                ];
            }
            $checks['cron_jobs'] = [
                'ok'   => !in_array(false, array_column($jobChecks, 'ok'), true),
                'jobs' => $jobChecks,
            ];
        } catch (PDOException) {
            $checks['cron_jobs'] = ['ok' => null, 'note' => 'not tracked on this instance'];
        }

        // R2 storage reachability + usage (tenant-scoped)
        try {
            $prefix = Config::getInstance()->get('R2_TENANT_PREFIX', '');
            $usage  = R2Client::totalUsage($prefix);
            $checks['storage_r2'] = ['ok' => true, 'used_bytes' => $usage['bytes']];
        } catch (\Throwable) {
            $checks['storage_r2'] = ['ok' => false, 'error' => 'R2 unreachable'];
        }

        // Schema integrity — presence of a column other features depend on
        // (no formal migration-version system exists in this project)
        $colCheck = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $colCheck->execute(['configuracion_centro', 'license_token']);
        $checks['schema'] = ['ok' => (bool)$colCheck->fetchColumn()];

        $overallOk = !in_array(false, array_column($checks, 'ok'), true);
        apiOk(['overall_ok' => $overallOk, 'checks' => $checks]);

    // POST — body: {"action":"heartbeat","license_token":"<signed_token>"}
    // Renews the license token. token_exp is extended by SaaS.
    case 'heartbeat':
        if (!$licenseToken) apiError('Missing license_token in heartbeat.', 400);
        if (!$licenseTokenStored) apiError('license_token column missing — this AulaPro database predates it; bring its schema up to date against the current noDeploy/database.sql.', 500);
        $stored = $licenseTokenStored; // already stored by the pre-switch block
        // Use SELECT * so the query works even if the migration 006 columns don't exist yet
        $row = $pdo->query("SELECT * FROM configuracion_centro WHERE idConfig = 1 LIMIT 1")->fetch() ?: [];
        apiOk([
            'heartbeat'         => $stored ? 'accepted' : 'accepted_pending_migration',
            'license_token_exp' => $row['license_token_exp'] ?? null,
            'instance_status'   => $row['instance_status']   ?? 'active',
        ]);

    default:
        apiError('Unknown action: ' . htmlspecialchars((string)$action), 400);
}
