<?php
declare(strict_types=1);

// POST  /api/v1/auth.php  — login  → returns Bearer token
// DELETE /api/v1/auth.php — logout → revokes current token

require_once __DIR__ . '/_api.php';

$method = $_SERVER['REQUEST_METHOD'];

// ── LOGIN ─────────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $body     = v1Body();
    $email    = strtolower(trim($body['email'] ?? ''));
    $password = $body['password'] ?? '';
    $role     = trim($body['role'] ?? '');          // optional: narrow the search
    $device   = substr(trim($body['device_info'] ?? ''), 0, 200);

    if (!$email || !$password) {
        v1Error('email and password are required.', 400, 'validation');
    }

    $con = obtenerConexion();

    // IP-level rate limiting (scope isolated from web login)
    if (!RateLimiter::allow($con, 'api_login', 10, 300, 900)) {
        v1Error('Too many login attempts from this IP. Try again in 15 minutes.', 429, 'rate_limited');
    }

    // Account-level lockout
    $lockout = AccountLockout::status($con, $email);
    if ($lockout['locked']) {
        v1Error("Account locked. Try again in {$lockout['minutes']} minute(s).", 429, 'account_locked');
    }

    // Try each user type (or just the requested role if provided)
    $candidates = ($role && isset(V1_USER_MAP[$role]))
        ? [$role => V1_USER_MAP[$role]]
        : V1_USER_MAP;

    $match     = null;
    $matchType = null;

    foreach ($candidates as $type => [$tabla, $idCol, $emailCol]) {
        // Los estudiantes en la papelera (soft-delete) no pueden autenticarse
        $filtroEliminado = ($tabla === 'estudiantes') ? ' AND eliminado = 0' : '';
        // Solo tutores/secretarias tienen must_change_password; el resto de tablas no,
        // así que se reporta 0 literal para no romper la consulta (columna inexistente).
        $colMustChange = in_array($tabla, ['tutores', 'secretarias'], true)
            ? '`must_change_password`'
            : '0 AS must_change_password';
        $st = mysqli_prepare($con,
            "SELECT `$idCol` AS uid, `password`, $colMustChange
             FROM `$tabla` WHERE `$emailCol` = ?$filtroEliminado LIMIT 1");
        mysqli_stmt_bind_param($st, 's', $email);
        mysqli_stmt_execute($st);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
        // Siempre se llama a password_verify(), exista o no la fila: si se
        // omitiera por completo cuando el email no aparece en esta tabla, el
        // tiempo de respuesta variaría según en qué tabla (o en ninguna)
        // existe el email — un canal lateral por tiempos que permite
        // enumerar cuentas sin necesidad de acertar la contraseña. El hash
        // ficticio es constante y nunca corresponde a un usuario real.
        $hashAComprobar = $row ? (string)$row['password'] : V1_DUMMY_HASH;
        $passwordOk     = password_verify($password, $hashAComprobar);
        if ($row && $passwordOk) {
            $match     = $row;
            $matchType = $type;
            break;
        }
    }

    if (!$match) {
        AccountLockout::recordFailure($con, $email);
        v1Error('Invalid email or password.', 401, 'invalid_credentials');
    }

    AccountLockout::clear($con, $email);

    // Purge expired tokens: this user's stale tokens + a global batch on each login
    $del = mysqli_prepare($con,
        'DELETE FROM api_tokens WHERE user_type = ? AND user_id = ? AND expires_at <= NOW()');
    mysqli_stmt_bind_param($del, 'si', $matchType, $match['uid']);
    mysqli_stmt_execute($del);
    // Global sweep: remove up to 100 expired tokens per login to keep the table lean
    mysqli_query($con, 'DELETE FROM api_tokens WHERE expires_at <= NOW() LIMIT 100');

    $token  = bin2hex(random_bytes(32)); // 64-char hex
    $expiry = date('Y-m-d H:i:s', time() + V1_TOKEN_TTL_DAYS * 86400);
    $uid    = (int)$match['uid'];

    $ins = mysqli_prepare($con,
        'INSERT INTO api_tokens (user_type, user_id, token, device_info, expires_at)
         VALUES (?, ?, ?, ?, ?)');
    mysqli_stmt_bind_param($ins, 'sisss', $matchType, $uid, $token, $device, $expiry);
    mysqli_stmt_execute($ins);

    $resp = [
        'token'      => $token,
        'token_type' => 'Bearer',
        'expires_at' => $expiry,
        'user_type'  => $matchType,
        'user_id'    => $uid,
    ];
    // Signal to the mobile app that the user must change their password
    if (!empty($match['must_change_password'])) {
        $resp['must_change_password'] = true;
    }

    v1Ok($resp, 201);
}

// ── LOGOUT ────────────────────────────────────────────────────────────────────
if ($method === 'DELETE') {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if ($header && stripos($header, 'Bearer ') === 0) {
        $token = trim(substr($header, 7));
        if (strlen($token) === 64 && ctype_xdigit($token)) {
            $con = obtenerConexion();
            $del = mysqli_prepare($con, 'DELETE FROM api_tokens WHERE token = ?');
            mysqli_stmt_bind_param($del, 's', $token);
            mysqli_stmt_execute($del);
        }
    }
    v1Ok(['message' => 'Logged out successfully.']);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
