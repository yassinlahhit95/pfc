<?php
declare(strict_types=1);

// POST /api/v1/fcm-token.php — register/refresh this device's FCM token so
// push notifications (chat, mensajes, calificaciones, entregas, sesiones,
// asistencia) can reach it. Body: { token: "<fcm registration token>" }.
// Column name differs per role table (`fcm_token` for every role except
// `secretarias.token_fcm`) — same asymmetry V1_STRIP already accounts for.

require_once __DIR__ . '/_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if (!isset(V1_USER_MAP[$type])) {
    v1Error('Unsupported role.', 403, 'forbidden');
}

$body = v1Body();
$token = trim((string)($body['token'] ?? ''));
if ($token === '') {
    v1Error('token is required.', 400, 'validation');
}

[$tabla, $campoId] = V1_USER_MAP[$type];
$campoToken = $type === 'secretaria' ? 'token_fcm' : 'fcm_token';

$con = obtenerConexion();
$st = mysqli_prepare($con, "UPDATE `$tabla` SET `$campoToken` = ? WHERE `$campoId` = ?");
mysqli_stmt_bind_param($st, 'si', $token, $uid);
if (!mysqli_stmt_execute($st)) {
    v1Error('Could not save the token.', 500, 'error');
}

v1Ok(['message' => 'Token registered.']);
