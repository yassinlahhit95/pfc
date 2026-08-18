<?php
declare(strict_types=1);

// Generic in-app notification inbox — shared by all 5 roles, backed by the
// same `notificaciones` table/model the web navbar bell already uses
// (modelos/notificaciones.php). See that file's call sites for what
// currently feeds it (module/ciclo assignment, published grades) — other
// event types (mensajes, pagos, eventos, chat, aula) don't write into this
// table yet, so they won't show up here until their controllers are wired
// to crearNotificacion() too.
//
// GET  /api/v1/notificaciones.php?action=list            — full history (leídas + no leídas)
// GET  /api/v1/notificaciones.php?action=unread-count
// POST /api/v1/notificaciones.php?action=mark-read      {ids: [int, ...]}
// POST /api/v1/notificaciones.php?action=mark-all-read

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/notificaciones.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

if ($method === 'GET' && $action === 'list') {
    v1Ok(['notificaciones' => listarNotificaciones($uid, $type, 50)]);
}

if ($method === 'GET' && $action === 'unread-count') {
    v1Ok(['count' => contarNotificacionesNoLeidas($uid, $type)]);
}

if ($method === 'POST') {
    $body = v1Body();

    if ($action === 'mark-read') {
        $ids = $body['ids'] ?? [];
        if (!is_array($ids)) v1Error('ids must be an array.', 400, 'validation');
        if (!marcarNotificacionesLeidas($uid, $type, $ids)) {
            v1Error('Could not mark as read.', 500, 'error');
        }
        v1Ok(['message' => 'Marked as read.']);
    }

    if ($action === 'mark-all-read') {
        if (!marcarTodasNotificacionesLeidas($uid, $type)) {
            v1Error('Could not mark all as read.', 500, 'error');
        }
        v1Ok(['message' => 'All marked as read.']);
    }

    v1Error('Acción no válida.', 400, 'validation');
}

v1Error('Ruta no válida.', 400, 'validation');
