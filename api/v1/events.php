<?php
declare(strict_types=1);

// GET /api/v1/events.php
//   Query params: limit (max 100, default 20), offset (default 0), upcoming (flag)
//   ?upcoming  — only events from today onwards
//   ?from=YYYY-MM-DD&to=YYYY-MM-DD — date range filter
// POST /api/v1/events.php
// PUT /api/v1/events.php
// DELETE /api/v1/events.php?id=...

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/eventos.php';
require_once __DIR__ . '/../../modelos/log.php';

$method = $_SERVER['REQUEST_METHOD'];
$usuario = v1Auth(); // any authenticated user can see events, but only director/secretaria can mutate
['user_type' => $type, 'user_id' => $uid] = $usuario;

if ($method === 'GET') {
    v1RequireFeature('feature_eventos');
    $limit  = min(max((int)($_GET['limit']  ?? 20), 1), 100);
    $offset = max((int)($_GET['offset'] ?? 0), 0);
    $upcoming = isset($_GET['upcoming']);

    $from = $_GET['from'] ?? null;
    $to   = $_GET['to']   ?? null;
    if ($from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = null;
    if ($to   && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = null;
    if ($upcoming) {
        $hoy  = date('Y-m-d');
        $from = ($from && $from > $hoy) ? $from : $hoy;
    }

    if ($type === 'director') {
        $rows = listarEventosDirector($from, $to);
    } else {
        $rows = obtenerEventosParaUsuario((int)$uid, $type, $from, $to);
    }

    $rows = array_map(fn($e) => [
        'idEvento'          => (int)$e['idEvento'],
        'tituloEvento'      => $e['tituloEvento'],
        'descripcionEvento' => $e['descripcionEvento'],
        'fechaEvento'       => $e['fechaEvento'],
        'horaEvento'        => $e['horaEvento'],
        'ubicacionEvento'   => $e['ubicacionEvento'],
    ], array_slice(array_values($rows), $offset, $limit));

    v1Ok(['events' => $rows, 'limit' => $limit, 'offset' => $offset]);
}

// Mutations require director or secretaria
if ($type !== 'director' && $type !== 'secretaria') {
    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}
v1RequireFeature('feature_eventos');

if ($method === 'POST') {
    $body = v1Body();
    $titulo = trim((string)($body['titulo'] ?? ''));
    $fecha = trim((string)($body['fecha'] ?? ''));
    if ($titulo === '' || $fecha === '') {
        v1Error('titulo and fecha are required.', 400, 'validation');
    }

    $data = [
        'tituloEvento'      => $titulo,
        'descripcionEvento' => $body['descripcion'] ?? '',
        'fechaEvento'       => $fecha,
        'horaEvento'        => $body['hora'] ?? '',
        'ubicacionEvento'   => $body['ubicacion'] ?? '',
        'tipo_visibilidad'  => $body['tipo_visibilidad'] ?? 'publica',
        'audiencia_json'    => $body['audiencia_json'] ?? null,
        'idCreador'         => (int)$uid
    ];

    $idNuevo = crearEvento($data);
    if (!$idNuevo) {
        v1Error('Could not create event.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('insertar', 'eventos', null, $titulo);
    } else {
        registrarAccionSecretaria('insertar', 'eventos', null, $titulo);
    }
    v1Ok(['success' => true, 'idEvento' => $idNuevo]);
}

if ($method === 'PUT') {
    $body = v1Body();
    $idEvento = (int)($body['idEvento'] ?? 0);
    if (!$idEvento) v1Error('idEvento is required.', 400, 'validation');

    $data = [];
    if (isset($body['titulo'])) $data['tituloEvento'] = trim((string)$body['titulo']);
    if (isset($body['descripcion'])) $data['descripcionEvento'] = $body['descripcion'];
    if (isset($body['fecha'])) $data['fechaEvento'] = $body['fecha'];
    if (isset($body['hora'])) $data['horaEvento'] = $body['hora'];
    if (isset($body['ubicacion'])) $data['ubicacionEvento'] = $body['ubicacion'];
    if (isset($body['tipo_visibilidad'])) $data['tipo_visibilidad'] = $body['tipo_visibilidad'];
    if (isset($body['audiencia_json'])) $data['audiencia_json'] = $body['audiencia_json'];

    if (!editarEvento($idEvento, $data)) {
        v1Error('Could not update event.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('actualizar', 'eventos', $idEvento, $data['tituloEvento'] ?? '');
    } else {
        registrarAccionSecretaria('actualizar', 'eventos', $idEvento, $data['tituloEvento'] ?? '');
    }
    v1Ok(['success' => true]);
}

if ($method === 'DELETE') {
    $idEvento = (int)($_GET['id'] ?? 0);
    if (!$idEvento) v1Error('id parameter is required.', 400, 'validation');

    if (!borrarEventoSuave($idEvento)) {
        v1Error('Could not delete event.', 500, 'error');
    }

    if ($type === 'director') {
        registrarAccion('eliminar', 'eventos', $idEvento, 'Borrado desde app');
    } else {
        registrarAccionSecretaria('eliminar', 'eventos', $idEvento, 'Borrado desde app');
    }
    v1Ok(['success' => true]);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
