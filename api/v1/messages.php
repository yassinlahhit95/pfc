<?php
declare(strict_types=1);

// GET  /api/v1/messages.php            — list root threads for the authenticated user
// GET  /api/v1/messages.php?id=<id>    — full thread (root + replies)
// POST /api/v1/messages.php            — create a new thread, reply, or mark-read (see body shapes below)
//
// estudiante → own threads (to a profesor, or to admin/direction if idProfesor omitted)
// profesor   → threads addressed to them (from a student, or their own to admin)
// admin / secretaria → all threads (secretaria's replies are proxied as emisor_rol='admin',
//                       matching the web app's existing behavior — reclamaciones.emisor_rol
//                       has no 'secretaria' value)
// tutor      → 403 (tutors use chat.php instead, not this ticket-style system)

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/reclamaciones.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($type === 'tutor') {
    v1Error('Tutors use the chat endpoint instead of messages.', 403, 'forbidden');
}
v1RequireFeature('feature_mensajes');

$method = $_SERVER['REQUEST_METHOD'];

// ── GET ───────────────────────────────────────────────────────────────────────
if ($method === 'GET') {
    $idRaiz = isset($_GET['id']) ? (int)$_GET['id'] : null;

    if ($idRaiz !== null) {
        $hilo = obtenerHiloCompleto($idRaiz);
        if (!$hilo) v1Error('Thread not found.', 404, 'not_found');
        $root = $hilo[0];

        $autorizado = match ($type) {
            'estudiante' => (int)($root['idEstudiante'] ?? 0) === $uid,
            'profesor'   => mensajePerteneceAProfesor($idRaiz, $uid) || (int)($root['idProfesor'] ?? 0) === $uid,
            'director', 'secretaria' => true,
            default => false,
        };
        if (!$autorizado) v1Error('You do not have access to this thread.', 403, 'forbidden');

        v1Ok(['thread' => $hilo]);
    }

    $items = match ($type) {
        'estudiante' => listarMensajesDeEstudiante($uid),
        'profesor'   => listarMensajesParaProfesor($uid),
        'director', 'secretaria' => listarTodosLosMensajes(),
        default => [],
    };
    v1Ok(['messages' => $items]);
}

// ── POST ──────────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $body = v1Body();

    // Mark as read
    if (($body['action'] ?? '') === 'read') {
        $id = (int)($body['id'] ?? 0);
        if ($id <= 0) v1Error('id is required.', 400, 'validation');
        if ($type === 'estudiante' || $type === 'profesor') {
            $hilo = obtenerHiloCompleto($id);
            $root = $hilo[0] ?? null;
            $autorizado = $root && (
                ($type === 'estudiante' && (int)($root['idEstudiante'] ?? 0) === $uid) ||
                ($type === 'profesor'   && (int)($root['idProfesor'] ?? 0) === $uid)
            );
            if (!$autorizado) v1Error('You do not have access to this thread.', 403, 'forbidden');
        }
        marcarMensajeComoLeido($id);
        v1Ok(['message' => 'Marked as read.']);
    }

    // Reply to an existing thread
    if (isset($body['id_parent'])) {
        $idParent  = (int)$body['id_parent'];
        $contenido = trim((string)($body['contenido'] ?? ''));
        if ($idParent <= 0 || $contenido === '') {
            v1Error('id_parent and contenido are required.', 400, 'validation');
        }
        if (mb_strlen($contenido) > 1000) {
            v1Error('contenido must be 1000 characters or fewer.', 400, 'validation');
        }

        $parentHilo = obtenerHiloCompleto($idParent);
        if (!$parentHilo) v1Error('Parent thread not found.', 404, 'not_found');
        $root = $parentHilo[0];

        $idEstudiante = null;
        $idProfesor   = null;
        $emisorRol    = $type;

        if ($type === 'estudiante') {
            if ((int)($root['idEstudiante'] ?? 0) !== $uid) v1Error('Not your thread.', 403, 'forbidden');
            $idEstudiante = $uid;
        } elseif ($type === 'profesor') {
            if (!mensajePerteneceAProfesor($idParent, $uid) && (int)($root['idProfesor'] ?? 0) !== $uid) {
                v1Error('Not your thread.', 403, 'forbidden');
            }
            $idProfesor = $uid;
        } else {
            // director / secretaria — secretaria proxies as 'admin' (reclamaciones.emisor_rol has no 'secretaria' value)
            $emisorRol = 'admin';
        }

        insertarRespuestaMensaje($idParent, $idEstudiante, $idProfesor, $contenido, $emisorRol);
        v1Ok(['message' => 'Reply sent.'], 201);
    }

    // New thread — only estudiante/profesor originate threads via the mobile app for now
    $asunto     = trim((string)($body['asunto'] ?? ''));
    $descripcion = trim((string)($body['descripcion'] ?? ''));
    if ($asunto === '' || $descripcion === '') {
        v1Error('asunto and descripcion are required.', 400, 'validation');
    }
    if (mb_strlen($descripcion) > 1000) {
        v1Error('descripcion must be 1000 characters or fewer.', 400, 'validation');
    }

    if ($type === 'estudiante') {
        $idProfesor = isset($body['idProfesor']) ? (int)$body['idProfesor'] : 0;
        insertarNuevoMensaje($uid, $idProfesor, $asunto, $descripcion, 'estudiante');
        v1Ok(['message' => 'Thread created.'], 201);
    }
    if ($type === 'profesor') {
        $idEstudiante = (int)($body['idEstudiante'] ?? 0);
        if ($idEstudiante <= 0) v1Error('idEstudiante is required.', 400, 'validation');
        insertarNuevoMensaje($idEstudiante, $uid, $asunto, $descripcion, 'profesor');
        v1Ok(['message' => 'Thread created.'], 201);
    }
    if ($type === 'director' || $type === 'secretaria') {
        $idEstudiante = isset($body['idEstudiante']) ? (int)$body['idEstudiante'] : 0;
        $idProfesor   = isset($body['idProfesor']) ? (int)$body['idProfesor'] : 0;
        if ($idEstudiante <= 0 && $idProfesor <= 0) {
            v1Error('idEstudiante or idProfesor is required.', 400, 'validation');
        }
        insertarNuevoMensaje($idEstudiante, $idProfesor, $asunto, $descripcion, 'admin');
        v1Ok(['message' => 'Thread created.'], 201);
    }

    v1Error('This role cannot start a new thread from the app.', 403, 'forbidden');
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
