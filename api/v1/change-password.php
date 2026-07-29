<?php
declare(strict_types=1);
require_once __DIR__ . '/../../modelos/conectar.php';
// POST /api/v1/change-password.php — self-service password change, all 5 roles.
// Body: { current_password, new_password }
// Requires the current password (unlike the web's forced must_change_password
// flow, which trusts the just-completed login instead) since this is a
// voluntary change from within an already-authenticated session. Same
// password policy as the web app (Security::validatePassword) and same
// hashing (bcrypt cost 12). Revokes every OTHER token for this account
// (not the one making this request) so a stolen-but-still-valid token
// elsewhere is cut off the moment the real owner changes their password.

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../include/Security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

$header = v1AuthHeader();
$currentToken = trim(substr($header, 7));

$body = v1Body();
$currentPassword = (string)($body['current_password'] ?? '');
$newPassword = (string)($body['new_password'] ?? '');

if ($currentPassword === '' || $newPassword === '') {
    v1Error('current_password and new_password are required.', 400, 'validation');
}

[$tabla, $idCol] = V1_USER_MAP[$type];
$con = obtenerConexion();

$st = mysqli_prepare($con, "SELECT `password` FROM `$tabla` WHERE `$idCol` = ? LIMIT 1");
mysqli_stmt_bind_param($st, 'i', $uid);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if (!$row || !password_verify($currentPassword, $row['password'])) {
    v1Error('Current password is incorrect.', 401, 'invalid_credentials');
}

$politica = Security::validatePassword($newPassword);
if (!$politica['valid']) {
    v1Error($politica['error'], 400, 'validation');
}

$hash = Security::hashPassword($newPassword);
// Only tutores/secretarias have must_change_password (see auth.php's same
// conditional) — including it unconditionally breaks the UPDATE for every
// other role's table.
$colMustChange = in_array($tabla, ['tutores', 'secretarias'], true) ? ', `must_change_password` = 0' : '';
$upd = mysqli_prepare($con, "UPDATE `$tabla` SET `password` = ?$colMustChange WHERE `$idCol` = ?");
mysqli_stmt_bind_param($upd, 'si', $hash, $uid);
if (!mysqli_stmt_execute($upd)) {
    v1Error('Could not update the password.', 500, 'error');
}
Security::touchPasswordChanged($con, $tabla, $idCol, $uid);

// Revoke every other token for this account — not the one used for this
// request, so the app doesn't immediately log the user back out.
$revoke = mysqli_prepare($con,
    'DELETE FROM api_tokens WHERE user_type = ? AND user_id = ? AND token != ?');
mysqli_stmt_bind_param($revoke, 'sis', $type, $uid, $currentToken);
mysqli_stmt_execute($revoke);

v1Ok(['message' => 'Password updated.']);
