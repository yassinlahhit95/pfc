<?php
declare(strict_types=1);

// GET  /api/v1/attendance.php — attendance scoped to the authenticated user's role
//   estudiante → own records
//   tutor      → records for each linked child (estudiante_tutor)
//   profesor   → requires idModulo (a module they teach); optional fecha filter
//   director/secretaria → 403 (use the web dashboard)
// Every ausente/retraso row includes its latest justification (if any) under
// `justificacion` — batched in one query, not per-row, per the N+1 the web
// UI has (obtenerJustificacionPorAsistencia called once per row there).
//
// POST /api/v1/attendance.php (profesor only) — bulk mark attendance for a
//   module+date: { idModulo, fecha, registros: [{idEstudiante, estado, observacion?}] }

require_once __DIR__ . '/_api.php';
require_once __DIR__ . '/../../modelos/asistencias.php';
require_once __DIR__ . '/../../modelos/justificacionesFalta.php';
require_once __DIR__ . '/../../modelos/modulos.php';
require_once __DIR__ . '/../../modelos/tutores.php';
require_once __DIR__ . '/_attendance_shared.php';

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
$method = $_SERVER['REQUEST_METHOD'];

function attendanceAttachJustifications(array $rows): array {
    $ids = [];
    foreach ($rows as $r) {
        if (in_array($r['estado'], ['ausente', 'retraso'], true)) $ids[] = (int)$r['idAsistencia'];
    }
    if (!$ids) {
        foreach ($rows as &$r) $r['justificacion'] = null;
        return $rows;
    }
    $con = obtenerConexion();
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $st = mysqli_prepare($con,
        "SELECT * FROM justificaciones_falta WHERE idAsistencia IN ($ph) ORDER BY idJustificacion DESC");
    mysqli_stmt_bind_param($st, $types, ...$ids);
    mysqli_stmt_execute($st);
    $byAsistencia = [];
    foreach (mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC) as $j) {
        $aid = (int)$j['idAsistencia'];
        if (!isset($byAsistencia[$aid]) ) {
            if (!empty($j['archivo'])) {
                $j['archivo_url'] = justificanteUrl($j['archivo']);
            }
            $byAsistencia[$aid] = $j; // latest wins (DESC order)
        }
    }
    foreach ($rows as &$r) {
        $r['justificacion'] = $byAsistencia[(int)$r['idAsistencia']] ?? null;
    }
    return $rows;
}

if ($method === 'GET') {
    if ($type === 'estudiante') {
        $rows = listarAsistenciasFiltradas(null, null, $uid, null, null, null);
        v1Ok(['attendance' => attendanceAttachJustifications($rows)]);
    }

    if ($type === 'tutor') {
        $hijos = listarEstudiantesPorTutor($uid);
        $rows = [];
        foreach ($hijos as $h) {
            $rows = array_merge($rows, listarAsistenciasFiltradas(null, null, (int)$h['idEstudiante'], null, null, null));
        }
        v1Ok(['attendance' => attendanceAttachJustifications($rows)]);
    }

    if ($type === 'profesor') {
        $idModulo = (int)($_GET['idModulo'] ?? 0);
        if ($idModulo <= 0) v1Error('idModulo is required.', 400, 'validation');
        $misModulos = listarModulosDeProfesor($uid);
        if (!in_array($idModulo, array_column($misModulos, 'idModulo'), true)) {
            v1Error('You do not teach this module.', 403, 'forbidden');
        }
        $fecha = $_GET['fecha'] ?? null;
        if ($fecha !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) $fecha = null;
        $rows = listarAsistenciasFiltradas(null, $idModulo, null, $fecha, $fecha, null);
        // Roster included so the app can render "mark attendance" pre-filled
        // for already-registered students and blank for the rest, without a
        // second round-trip.
        v1Ok([
            'attendance' => attendanceAttachJustifications($rows),
            'roster' => listarEstudiantesDeModulo($idModulo),
        ]);
    }

    v1Error('This endpoint is not available for this role.', 403, 'forbidden');
}

if ($method === 'POST') {
    if ($type !== 'profesor') {
        v1Error('Only profesores can register attendance.', 403, 'forbidden');
    }
    $body = v1Body();
    $idModulo = (int)($body['idModulo'] ?? 0);
    $fecha = (string)($body['fecha'] ?? '');
    $registros = $body['registros'] ?? null;

    if ($idModulo <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || !is_array($registros)) {
        v1Error('idModulo, fecha (YYYY-MM-DD) and registros[] are required.', 400, 'validation');
    }
    $misModulos = listarModulosDeProfesor($uid);
    if (!in_array($idModulo, array_column($misModulos, 'idModulo'), true)) {
        v1Error('You do not teach this module.', 403, 'forbidden');
    }

    $ok = guardarAsistenciasMasivo($idModulo, $uid, $fecha, $registros);
    if (!$ok) v1Error('Could not save attendance.', 500, 'error');
    v1Ok(['message' => 'Attendance saved.']);
}

v1Error('Method not allowed.', 405, 'method_not_allowed');
