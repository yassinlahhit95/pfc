<?php
declare(strict_types=1);

// GET  /api/v1/attendance-resolve.php — pending justifications for modules
//   the authenticated profesor teaches
// POST /api/v1/attendance-resolve.php (profesor only)
//   { idJustificacion, aprobar: bool, motivoRechazo? } — motivoRechazo
//   required when aprobar is false. Sends a push to the student (and any
//   linked tutores) once fcm-token.php exists (Phase 2 backend, not wired
//   yet client-side — see firebase_helper.php's obtenerTokenUsuario()).

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/justificacionesFalta.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;

if ($type !== 'profesor') {
    v1Error('Only profesores can resolve justifications.', 403, 'forbidden');
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    v1Ok(['pending' => listarJustificacionesPendientesPorProfesor($uid)]);
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
        $motivoRechazo
    );
    if (!$ok) v1Error('Could not resolve the justification.', 500, 'error');
    v1Ok(['message' => $aprobar ? 'Approved.' : 'Rejected.']);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
