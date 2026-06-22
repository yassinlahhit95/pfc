<?php
declare(strict_types=1);

// GET /api/v1/schedule.php — timetable scoped to the authenticated user
//
// estudiante → schedule for their enrolled cycle
// profesor   → their personal teaching schedule
// tutor      → schedule for their tutored students' cycles
// director   → 403

require_once __DIR__ . '/_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
$con = obtenerConexion();

$dayOrder = "FIELD(h.diaSemana,'Lunes','Martes','Miércoles','Jueves','Viernes')";

// ── Estudiante ────────────────────────────────────────────────────────────────
if ($type === 'estudiante') {
    $st = mysqli_prepare($con, 'SELECT idCiclo FROM estudiantes WHERE idEstudiante = ? LIMIT 1');
    mysqli_stmt_bind_param($st, 'i', $uid);
    mysqli_stmt_execute($st);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
    if (!$row || !$row['idCiclo']) v1Ok(['schedule' => []]);

    $idCiclo = (int)$row['idCiclo'];
    $st = mysqli_prepare($con,
        "SELECT h.diaSemana, h.horaInicio, h.horaFin,
                m.nombreModulo,
                p.nombreProfesor,
                a.codigoAula, a.nombreAula
         FROM horarios h
         LEFT JOIN modulos    m ON h.idModulo   = m.idModulo
         LEFT JOIN profesores p ON h.idProfesor = p.idProfesor
         LEFT JOIN aulas      a ON h.idAula     = a.idAula
         WHERE h.idCiclo = ?
         ORDER BY $dayOrder, h.horaInicio");
    mysqli_stmt_bind_param($st, 'i', $idCiclo);
    mysqli_stmt_execute($st);
    v1Ok(['schedule' => mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC)]);
}

// ── Profesor ──────────────────────────────────────────────────────────────────
if ($type === 'profesor') {
    $st = mysqli_prepare($con,
        "SELECT h.diaSemana, h.horaInicio, h.horaFin,
                m.nombreModulo,
                c.nombreCiclo, c.abreviaturaCiclo,
                a.codigoAula, a.nombreAula
         FROM horarios h
         LEFT JOIN modulos m ON h.idModulo = m.idModulo
         LEFT JOIN ciclos  c ON h.idCiclo  = c.idCiclo
         LEFT JOIN aulas   a ON h.idAula   = a.idAula
         WHERE h.idProfesor = ?
         ORDER BY $dayOrder, h.horaInicio");
    mysqli_stmt_bind_param($st, 'i', $uid);
    mysqli_stmt_execute($st);
    v1Ok(['schedule' => mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC)]);
}

// ── Tutor ─────────────────────────────────────────────────────────────────────
if ($type === 'tutor') {
    // Get all cycle IDs of tutored students
    $st = mysqli_prepare($con,
        'SELECT DISTINCT e.idCiclo, c.nombreCiclo, c.abreviaturaCiclo
         FROM estudiante_tutor et
         JOIN estudiantes e ON et.idEstudiante = e.idEstudiante
         JOIN ciclos      c ON e.idCiclo = c.idCiclo
         WHERE et.idTutor = ?');
    mysqli_stmt_bind_param($st, 'i', $uid);
    mysqli_stmt_execute($st);
    $cycles = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

    if (!$cycles) v1Ok(['schedule' => []]);

    $ids = array_column($cycles, 'idCiclo');
    $ph  = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $st = mysqli_prepare($con,
        "SELECT h.diaSemana, h.horaInicio, h.horaFin,
                m.nombreModulo,
                p.nombreProfesor,
                c.nombreCiclo, c.abreviaturaCiclo,
                a.codigoAula, a.nombreAula
         FROM horarios h
         LEFT JOIN modulos    m ON h.idModulo   = m.idModulo
         LEFT JOIN profesores p ON h.idProfesor = p.idProfesor
         LEFT JOIN ciclos     c ON h.idCiclo    = c.idCiclo
         LEFT JOIN aulas      a ON h.idAula     = a.idAula
         WHERE h.idCiclo IN ($ph)
         ORDER BY c.nombreCiclo, $dayOrder, h.horaInicio");
    mysqli_stmt_bind_param($st, $types, ...$ids);
    mysqli_stmt_execute($st);
    v1Ok(['schedule' => mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC)]);
}

v1Error('This endpoint is not available for directors.', 403, 'forbidden');
