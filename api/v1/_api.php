<?php
declare(strict_types=1);

// AulaPro Mobile API v1 — shared bootstrap.
// Stateless (no sessions). Auth via Bearer token stored in api_tokens table.

require_once __DIR__ . '/../../config/Config.php';
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/RateLimiter.php';
require_once __DIR__ . '/../../include/AccountLockout.php';

// ── Constants ─────────────────────────────────────────────────────────────────

const V1_TOKEN_TTL_DAYS = 30;

// Hash ficticio constante para igualar el tiempo de password_verify() cuando
// un email no existe en una tabla — nunca es el hash de un usuario real, solo
// necesita tener formato bcrypt válido para que password_verify() haga el
// mismo trabajo que con un hash genuino (mitiga enumeración de cuentas por
// tiempos de respuesta en el login).
const V1_DUMMY_HASH = '$2y$10$t1LHvm11cHG/09pxM/HpOuNHOGR0q4ZSuanwHhgS6bxAZ3soIh8.m';

// [table, idCol, emailCol, nameCol]
const V1_USER_MAP = [
    'estudiante' => ['estudiantes', 'idEstudiante', 'emailEstudiante', 'nombreEstudiante'],
    'profesor'   => ['profesores',  'idProfesor',   'emailProfesor',   'nombreProfesor'],
    'director'   => ['directores',  'idDirector',   'emailDirector',   'nombreDirector'],
    'tutor'      => ['tutores',     'idTutor',       'emailTutor',      'nombreTutor'],
];

// Columns that must never be sent to clients
const V1_STRIP = ['password', 'fcm_token', 'pwd_changed_at', 'mfa_secret', 'mfa_backup_codes'];

// ── Response helpers ──────────────────────────────────────────────────────────

function v1Error(string $msg, int $httpCode = 400, string $code = 'error'): never {
    http_response_code($httpCode);
    echo json_encode(['ok' => false, 'error' => $msg, 'code' => $code]);
    exit;
}

function v1Ok(array $payload, int $httpCode = 200): never {
    http_response_code($httpCode);
    echo json_encode(array_merge(['ok' => true], $payload));
    exit;
}

function v1Body(): array {
    static $body = null;
    if ($body !== null) return $body;
    $raw = file_get_contents('php://input');
    return $body = ($raw ? (json_decode($raw, true) ?? []) : []);
}

function v1Strip(array $row): array {
    foreach (V1_STRIP as $col) unset($row[$col]);
    return $row;
}

// ── Auth middleware ───────────────────────────────────────────────────────────

// Validates Bearer token; returns ['user_type'=>string, 'user_id'=>int].
// Terminates with 401 on failure.
function v1Auth(): array {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!$header || stripos($header, 'Bearer ') !== 0) {
        v1Error('Missing or malformed Authorization header.', 401, 'unauthenticated');
    }
    $token = trim(substr($header, 7));
    if (strlen($token) !== 64 || !ctype_xdigit($token)) {
        v1Error('Invalid token format.', 401, 'unauthenticated');
    }

    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'SELECT id, user_type, user_id FROM api_tokens
         WHERE token = ? AND expires_at > NOW() LIMIT 1');
    mysqli_stmt_bind_param($st, 's', $token);
    mysqli_stmt_execute($st);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));

    if (!$row) {
        v1Error('Token expired or not found. Please log in again.', 401, 'token_expired');
    }

    // Per-token rate limit: 120 requests/minute (protects data endpoints from token abuse)
    if (!RateLimiter::allow($con, 'apiv1_' . substr($token, 0, 8), 120, 60, 300)) {
        v1Error('Rate limit exceeded. Please slow down.', 429, 'rate_limited');
    }

    $id = (int)$row['id'];
    $upd = mysqli_prepare($con, 'UPDATE api_tokens SET last_used_at = NOW() WHERE id = ?');
    mysqli_stmt_bind_param($upd, 'i', $id);
    mysqli_stmt_execute($upd);

    return ['user_type' => $row['user_type'], 'user_id' => (int)$row['user_id']];
}

// ── Common HTTP setup ─────────────────────────────────────────────────────────

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');
// CORS: mobile apps have no browser Origin — open CORS is safe since every
// endpoint still requires a valid Bearer token.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
