<?php
declare(strict_types=1);

// GET /api/v1/messages-unread.php — unread mensajería count for the authenticated user.
// Bearer-token equivalent of the session-based controladores/ajax/mensajes_polling.php.
// Poll on a long interval (~60s) while the messages tab is open — push notifications
// (fcm-token.php) cover the real-time case, this is just a fallback/badge refresh.

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/reclamaciones.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
v1RequireFeature('feature_mensajes');

$unread = match ($type) {
    'estudiante' => contarMensajesNoLeidosEstudiante($uid),
    'profesor'   => contarMensajesNoLeidosProfesor($uid),
    'director', 'secretaria' => contarMensajesNoLeidosAdmin(),
    default => 0, // tutor doesn't use this system
};

v1Ok(['unread' => $unread]);
