<?php
declare(strict_types=1);

// GET /api/v1/grades.php — returns grades scoped to the authenticated user's role
//
// estudiante → own module grades + reto grades
// profesor   → grades for modules they teach, grouped by student
// tutor      → grades for their tutored students
// director / secretaria → 403 (use the web interface)

require_once __DIR__ . '/_api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    v1Error('Method not allowed.', 405, 'method_not_allowed');
}

$auth = v1Auth();
['user_type' => $type, 'user_id' => $uid] = $auth;
$con = obtenerConexion();

// ── Estudiante ────────────────────────────────────────────────────────────────
if ($type === 'estudiante') {
    $st = mysqli_prepare($con,
        'SELECT cm.*, m.nombreModulo, m.horasMaximas
         FROM calificaciones_modulos cm
         JOIN modulos m ON cm.idModulo = m.idModulo
         WHERE cm.idEstudiante = ?
         ORDER BY m.nombreModulo ASC');
    mysqli_stmt_bind_param($st, 'i', $uid);
    mysqli_stmt_execute($st);
    $modulos = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

    $sr = mysqli_prepare($con,
        'SELECT cr.nota, r.nombreReto, r.fechaInicio, r.fechaFin
         FROM calificaciones_retos cr
         JOIN retos r ON cr.idReto = r.idReto
         WHERE cr.idEstudiante = ?
         ORDER BY r.fechaInicio DESC');
    mysqli_stmt_bind_param($sr, 'i', $uid);
    mysqli_stmt_execute($sr);
    $retos = mysqli_fetch_all(mysqli_stmt_get_result($sr), MYSQLI_ASSOC);

    v1Ok(['modulos' => $modulos, 'retos' => $retos]);
}

// ── Profesor ──────────────────────────────────────────────────────────────────
if ($type === 'profesor') {
    // Grades per module taught by this professor
    $st = mysqli_prepare($con,
        'SELECT m.idModulo, m.nombreModulo,
                e.idEstudiante, e.nombreEstudiante,
                cm.nota_1ev, cm.nota_1final, cm.nota_2ev, cm.nota_2final,
                cm.estado_1ev, cm.estado_1final, cm.estado_2ev, cm.estado_2final,
                cm.observaciones
         FROM modulo_profesor mp
         JOIN modulos         m  ON mp.idModulo    = m.idModulo
         JOIN estudiantes     e  ON e.idCiclo       = m.idCiclo
         LEFT JOIN calificaciones_modulos cm
                ON cm.idModulo = m.idModulo AND cm.idEstudiante = e.idEstudiante
         WHERE mp.idProfesor = ?
         ORDER BY m.nombreModulo ASC, e.nombreEstudiante ASC');
    mysqli_stmt_bind_param($st, 'i', $uid);
    mysqli_stmt_execute($st);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

    // Group by module
    $byModule = [];
    foreach ($rows as $r) {
        $mid = (int)$r['idModulo'];
        if (!isset($byModule[$mid])) {
            $byModule[$mid] = [
                'idModulo'    => $mid,
                'nombreModulo' => $r['nombreModulo'],
                'estudiantes'  => [],
            ];
        }
        $byModule[$mid]['estudiantes'][] = [
            'idEstudiante'    => (int)$r['idEstudiante'],
            'nombreEstudiante' => $r['nombreEstudiante'],
            'nota_1ev'        => $r['nota_1ev'],
            'nota_1final'     => $r['nota_1final'],
            'nota_2ev'        => $r['nota_2ev'],
            'nota_2final'     => $r['nota_2final'],
            'estado_1ev'      => $r['estado_1ev'],
            'estado_1final'   => $r['estado_1final'],
            'estado_2ev'      => $r['estado_2ev'],
            'estado_2final'   => $r['estado_2final'],
            'observaciones'   => $r['observaciones'],
        ];
    }

    v1Ok(['modulos' => array_values($byModule)]);
}

// ── Tutor ─────────────────────────────────────────────────────────────────────
if ($type === 'tutor') {
    $st = mysqli_prepare($con,
        'SELECT e.idEstudiante, e.nombreEstudiante, et.parentesco,
                cm.nota_1ev, cm.nota_1final, cm.nota_2ev, cm.nota_2final,
                cm.estado_1ev, cm.estado_1final, cm.estado_2ev, cm.estado_2final,
                m.nombreModulo
         FROM estudiante_tutor et
         JOIN estudiantes e ON et.idEstudiante = e.idEstudiante
         LEFT JOIN calificaciones_modulos cm ON cm.idEstudiante = e.idEstudiante
         LEFT JOIN modulos m ON cm.idModulo = m.idModulo
         WHERE et.idTutor = ?
         ORDER BY e.nombreEstudiante ASC, m.nombreModulo ASC');
    mysqli_stmt_bind_param($st, 'i', $uid);
    mysqli_stmt_execute($st);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

    // Group by student
    $byStudent = [];
    foreach ($rows as $r) {
        $eid = (int)$r['idEstudiante'];
        if (!isset($byStudent[$eid])) {
            $byStudent[$eid] = [
                'idEstudiante'     => $eid,
                'nombreEstudiante' => $r['nombreEstudiante'],
                'parentesco'       => $r['parentesco'],
                'modulos'          => [],
            ];
        }
        if ($r['nombreModulo']) {
            $byStudent[$eid]['modulos'][] = [
                'nombreModulo' => $r['nombreModulo'],
                'nota_1ev'     => $r['nota_1ev'],
                'nota_1final'  => $r['nota_1final'],
                'nota_2ev'     => $r['nota_2ev'],
                'nota_2final'  => $r['nota_2final'],
                'estado_1ev'   => $r['estado_1ev'],
                'estado_1final'=> $r['estado_1final'],
                'estado_2ev'   => $r['estado_2ev'],
                'estado_2final'=> $r['estado_2final'],
            ];
        }
    }

    v1Ok(['students' => array_values($byStudent)]);
}

// director / secretaria — grades are managed on the web dashboard
v1Error('This endpoint is not available for this role.', 403, 'forbidden');
