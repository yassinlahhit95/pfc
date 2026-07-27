<?php
declare(strict_types=1);

// GET  /api/v1/attendance-resolve.php — pending justifications for modules
//   the authenticated profesor teaches
// POST /api/v1/attendance-resolve.php (profesor only)
//   { idJustificacion, aprobar: bool, motivoRechazo? } — motivoRechazo
//   required when aprobar is false. Sends a push to the student and any
//   linked tutores (fcm-token.php).

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/justificacionesFalta.php';
require_once __DIR__ . '/_attendance_shared.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($type !== 'profesor') {
    v1Error('Only profesores can resolve justifications.', 403, 'forbidden');
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $pending = listarJustificacionesPendientesPorProfesor($uid);
    foreach ($pending as &$j) {
        if (!empty($j['archivo'])) $j['archivo_url'] = justificanteUrl($j['archivo']);
    }
    unset($j);
    v1Ok(['pending' => $pending]);
}

if ($method === 'POST') {
    $body = v1Body();
    $idJustificacion = (int)($body['idJustificacion'] ?? 0);
    $aprobar = $body['aprobar'] ?? null;
    $motivoRechazo = trim((string)($body['motivoRechazo'] ?? ''));

    if ($idJustificacion <= 0 || !is_bool($aprobar)) {
        v1Error('idJustificacion and aprobar are required.', 400, 'validation');
    }
    if (!$aprobar && $motivoRechazo === '') {
        v1Error('motivoRechazo is required when rejecting.', 400, 'validation');
    }

    $justificacion = justificacionPerteneceAProfesor($idJustificacion, $uid);
    if (!$justificacion) v1Error('Justification not found.', 404, 'not_found');
    if ($justificacion['estado'] !== 'pendiente') {
        v1Error('This justification has already been resolved.', 409, 'validation');
    }

    $ok = resolverJustificacionFalta(
        $idJustificacion,
        (int)$justificacion['idAsistencia'],
        $aprobar,
        $uid,
        $motivoRechazo,
        $justificacion['estadoOriginal']
    );
    if (!$ok) v1Error('Could not resolve the justification.', 500, 'error');

    notificarJustificacionResuelta((int)$justificacion['idEstudiante'], (int)$justificacion['idAsistencia'], $aprobar, $motivoRechazo);

    v1Ok(['message' => $aprobar ? 'Approved.' : 'Rejected.']);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
