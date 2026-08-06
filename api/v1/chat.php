<?php
declare(strict_types=1);

// Direct 1:1 chat — action-param dispatched, mirrors controladores/chat/*.php
// but over Bearer-token auth instead of session. All 5 roles supported
// (modelos/chat.php is already fully role-general — no DB migration needed).
//
// GET  /api/v1/chat.php?action=contacts&q=<search>
// GET  /api/v1/chat.php?action=conversations
// GET  /api/v1/chat.php?action=messages&conv_id=<id>&after=<msgId>
// GET  /api/v1/chat.php?action=unread
// POST /api/v1/chat.php  {action:'start', target_rol, target_id}
// POST /api/v1/chat.php  {action:'send', conv_id, contenido}
//
// Nota: el rol 'director' de la API se mapea internamente al rol 'admin' del chat —
// modelos/chat.php se escribió antes de que existiera la API v1 y usa 'admin'.

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/chat.php';

$auth = v1Auth();
['user_type' => $apiType, 'user_id' => $uid] = $auth;
$rol = $apiType === 'director' ? 'admin' : $apiType;

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'contacts') {
        $q = trim((string)($_GET['q'] ?? ''));
        v1Ok(['contacts' => chatContactosPosibles($rol, $uid, $q)]);
    }

    if ($action === 'conversations') {
        v1Ok(['conversations' => chatConversacionesDe($rol, $uid)]);
    }

    if ($action === 'messages') {
        $convId = (int)($_GET['conv_id'] ?? 0);
        if ($convId <= 0) v1Error('conv_id is required.', 400, 'validation');
        $conv = chatConversacionPorId($convId);
        if (!$conv || !chatEsParticipante($conv, $rol, $uid)) {
            v1Error('Conversation not found.', 404, 'not_found');
        }
        $after = isset($_GET['after']) ? (int)$_GET['after'] : null;
        if ($after !== null) {
            $messages = chatMensajesDespuesDe($convId, $after);
        } else {
            // NOT chatMensajes() — its `ORDER BY fecha ASC LIMIT 80` returns
            // the OLDEST 80 messages in a long conversation, hiding recent
            // ones entirely. Fetch the latest 80 instead, then restore
            // chronological order for display.
            $con = obtenerConexion();
            $st = mysqli_prepare($con,
                'SELECT * FROM (SELECT * FROM chat_mensajes WHERE conversacion_id = ?
                    ORDER BY id DESC LIMIT 80) recientes
                 ORDER BY id ASC');
            mysqli_stmt_bind_param($st, 'i', $convId);
            mysqli_stmt_execute($st);
            $messages = _chatAttachNombres(mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC));
        }
        chatMarcarLeidos($convId, $rol, $uid);
        v1Ok(['messages' => $messages]);
    }

    if ($action === 'unread') {
        v1Ok(['unread' => chatContarNoLeidos($rol, $uid)]);
    }

    v1Error('Unknown action.', 400, 'validation');
}

if ($method === 'POST') {
    $body = v1Body();
    $action = $body['action'] ?? '';

    if ($action === 'start') {
        $targetRol = (string)($body['target_rol'] ?? '');
        $targetId  = (int)($body['target_id'] ?? 0);
        if ($targetRol === '' || $targetId <= 0) {
            v1Error('target_rol and target_id are required.', 400, 'validation');
        }
        if (!chatParEsPermitido($rol, $uid, $targetRol, $targetId)) {
            v1Error('This conversation is not allowed.', 403, 'forbidden');
        }
        $convId = chatEncontrarOCrear($rol, $uid, $targetRol, $targetId);
        v1Ok(['conv_id' => $convId], 201);
    }

    if ($action === 'send') {
        $convId    = (int)($body['conv_id'] ?? 0);
        $contenido = trim((string)($body['contenido'] ?? ''));
        if ($convId <= 0 || $contenido === '') {
            v1Error('conv_id and contenido are required.', 400, 'validation');
        }
        if (mb_strlen($contenido) > 2000) {
            v1Error('contenido must be 2000 characters or fewer.', 400, 'validation');
        }
        $conv = chatConversacionPorId($convId);
        if (!$conv || !chatEsParticipante($conv, $rol, $uid)) {
            v1Error('Conversation not found.', 404, 'not_found');
        }

        // 30 messages / 60s per user (not per-token, so re-login can't bypass it)
        $con = obtenerConexion();
        if (!RateLimiter::allow($con, "chat_send_{$rol}_{$uid}", 30, 60, 120)) {
            v1Error('You are sending messages too fast. Slow down.', 429, 'rate_limited');
        }

        $msgId = chatInsertarMensaje($convId, $rol, $uid, $contenido);
        v1Ok(['message_id' => $msgId], 201);
    }

    v1Error('Unknown action.', 400, 'validation');
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
