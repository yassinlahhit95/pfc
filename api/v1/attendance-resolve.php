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

    $idEstudiante = (int)$justificacion['idEstudiante'];
    $titulo = $aprobar ? 'Justificación aprobada' : 'Justificación rechazada';
    $mensaje = $aprobar
        ? 'Tu justificación de falta ha sido aprobada.'
        : 'Tu justificación de falta ha sido rechazada' . ($motivoRechazo !== '' ? ": $motivoRechazo" : '.');

    $fh = __DIR__ . '/../../controladores/firebase/firebase_helper.php';
    if (file_exists($fh)) {
        require_once $fh;
        $con = obtenerConexion();
        $destinatarios = [['id' => $idEstudiante, 'rol' => 'estudiante']];
        $st = mysqli_prepare($con, 'SELECT idTutor FROM estudiante_tutor WHERE idEstudiante = ?');
        mysqli_stmt_bind_param($st, 'i', $idEstudiante);
        mysqli_stmt_execute($st);
        foreach (mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC) as $row) {
            $destinatarios[] = ['id' => (int)$row['idTutor'], 'rol' => 'tutor'];
        }
        foreach ($destinatarios as $d) {
            $token = obtenerTokenUsuario($d['id'], $d['rol']);
            if ($token) {
                enviarNotificacionFirebase($token, $titulo, $mensaje, 'asistencia_resuelta', ['idAsistencia' => (int)$justificacion['idAsistencia']]);
            }
        }
    }

    v1Ok(['message' => $aprobar ? 'Approved.' : 'Rejected.']);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
