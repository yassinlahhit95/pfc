<?php
require_once __DIR__ . '/conectar.php';

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

// Ordena los dos participantes de forma canónica para clave única de conversación.
function chatNormalizar(string $rolA, int $idA, string $rolB, int $idB): array {
    $ka = $rolA . sprintf('%010d', $idA);
    $kb = $rolB . sprintf('%010d', $idB);
    return $ka <= $kb
        ? [$rolA, $idA, $rolB, $idB]
        : [$rolB, $idB, $rolA, $idA];
}

function chatNombreUsuario(string $rol, int $id): string {
    static $cache = [];
    $key = $rol . ':' . $id;
    if (isset($cache[$key])) return $cache[$key];
    $con = obtenerConexion();
    switch ($rol) {
        case 'admin':
            $st = mysqli_prepare($con, 'SELECT nombreDirector FROM directores WHERE idDirector = ?');
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
            return $cache[$key] = ($row['nombreDirector'] ?? 'Admin');
        case 'profesor':
            $st = mysqli_prepare($con, 'SELECT nombreProfesor FROM profesores WHERE idProfesor = ?');
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
            return $cache[$key] = ($row['nombreProfesor'] ?? 'Profesor');
        case 'tutor':
            $st = mysqli_prepare($con, 'SELECT nombreTutor FROM tutores WHERE idTutor = ?');
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
            return $cache[$key] = ($row['nombreTutor'] ?? 'Tutor');
        default:
            $st = mysqli_prepare($con, 'SELECT nombreEstudiante FROM estudiantes WHERE idEstudiante = ?');
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
            return $cache[$key] = ($row['nombreEstudiante'] ?? 'Estudiante');
    }
}

// Batch-fetches names for an array of ['rol' => string, 'id' => int] pairs.
// Returns a map of 'rol:id' => name — at most 4 queries regardless of set size.
function chatBatchNombres(array $pairs): array {
    if (!$pairs) return [];
    $con = obtenerConexion();
    $byRol = [];
    foreach ($pairs as $p) {
        $byRol[$p['rol']][$p['id']] = true;
    }
    $map = [
        'admin'      => ['directores',  'idDirector',    'nombreDirector'],
        'profesor'   => ['profesores',  'idProfesor',    'nombreProfesor'],
        'tutor'      => ['tutores',     'idTutor',       'nombreTutor'],
        'estudiante' => ['estudiantes', 'idEstudiante',  'nombreEstudiante'],
    ];
    $defaults = ['admin' => 'Admin', 'profesor' => 'Profesor', 'tutor' => 'Tutor', 'estudiante' => 'Estudiante'];
    $names = [];
    foreach ($byRol as $rol => $idSet) {
        if (!isset($map[$rol])) continue;
        [$tabla, $idCol, $nameCol] = $map[$rol];
        $ids = array_keys($idSet);
        $ph  = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        $st = mysqli_prepare($con, "SELECT `$idCol`, `$nameCol` FROM `$tabla` WHERE `$idCol` IN ($ph)");
        mysqli_stmt_bind_param($st, $types, ...$ids);
        mysqli_stmt_execute($st);
        foreach (mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC) as $row) {
            $names[$rol . ':' . $row[$idCol]] = $row[$nameCol];
        }
        foreach ($ids as $id) {
            $k = $rol . ':' . $id;
            if (!isset($names[$k])) $names[$k] = $defaults[$rol] ?? '?';
        }
    }
    return $names;
}

// ══════════════════════════════════════════════════════════════════════
// CONVERSACIONES
// ══════════════════════════════════════════════════════════════════════

function chatEncontrarOCrear(string $rolA, int $idA, string $rolB, int $idB): int {
    [$nRolA, $nIdA, $nRolB, $nIdB] = chatNormalizar($rolA, $idA, $rolB, $idB);
    $con = obtenerConexion();

    // Atomic upsert: INSERT IGNORE avoids a race between two concurrent opens of the same conversation.
    // LAST_INSERT_ID(id) makes LAST_INSERT_ID() return the existing row's id on duplicate.
    $st = mysqli_prepare($con,
        'INSERT INTO chat_conversaciones (user_a_rol,user_a_id,user_b_rol,user_b_id)
         VALUES (?,?,?,?)
         ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)');
    mysqli_stmt_bind_param($st, 'sisi', $nRolA, $nIdA, $nRolB, $nIdB);
    mysqli_stmt_execute($st);
    $newId = (int)mysqli_insert_id($con);
    if ($newId > 0) return $newId;

    // Fallback: row existed before the upsert (LAST_INSERT_ID stays 0 in older MySQL)
    $st = mysqli_prepare($con,
        'SELECT id FROM chat_conversaciones WHERE user_a_rol=? AND user_a_id=? AND user_b_rol=? AND user_b_id=?');
    mysqli_stmt_bind_param($st, 'sisi', $nRolA, $nIdA, $nRolB, $nIdB);
    mysqli_stmt_execute($st);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
    return (int)($row['id'] ?? 0);
}

function chatConversacionesDe(string $rol, int $id): array {
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'SELECT c.id, c.user_a_rol, c.user_a_id, c.user_b_rol, c.user_b_id,
                c.last_message_at,
                (SELECT COUNT(*) FROM chat_mensajes m
                 WHERE m.conversacion_id = c.id AND m.leido = 0
                   AND NOT (m.emisor_rol = ? AND m.emisor_id = ?)) AS unread_count,
                (SELECT m2.contenido FROM chat_mensajes m2
                 WHERE m2.conversacion_id = c.id
                 ORDER BY m2.fecha DESC LIMIT 1) AS last_preview
         FROM chat_conversaciones c
         WHERE (c.user_a_rol = ? AND c.user_a_id = ?)
            OR (c.user_b_rol = ? AND c.user_b_id = ?)
         ORDER BY c.last_message_at DESC');
    mysqli_stmt_bind_param($st, 'sisisi', $rol, $id, $rol, $id, $rol, $id);
    mysqli_stmt_execute($st);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);

    $pairs = [];
    foreach ($rows as &$row) {
        $isA = ($row['user_a_rol'] === $rol && (int)$row['user_a_id'] === $id);
        $row['other_rol'] = $isA ? $row['user_b_rol'] : $row['user_a_rol'];
        $row['other_id']  = $isA ? (int)$row['user_b_id'] : (int)$row['user_a_id'];
        $row['unread_count'] = (int)$row['unread_count'];
        $pairs[] = ['rol' => $row['other_rol'], 'id' => $row['other_id']];
    }
    unset($row);

    $names = chatBatchNombres($pairs);
    foreach ($rows as &$row) {
        $row['other_nombre'] = $names[$row['other_rol'] . ':' . $row['other_id']] ?? '?';
    }
    unset($row);
    return $rows;
}

function chatConversacionPorId(int $convId): ?array {
    $con = obtenerConexion();
    $st = mysqli_prepare($con, 'SELECT * FROM chat_conversaciones WHERE id = ?');
    mysqli_stmt_bind_param($st, 'i', $convId);
    mysqli_stmt_execute($st);
    $r = mysqli_stmt_get_result($st);
    return mysqli_fetch_assoc($r) ?: null;
}

function chatEsParticipante(array $conv, string $rol, int $id): bool {
    return ($conv['user_a_rol'] === $rol && (int)$conv['user_a_id'] === $id)
        || ($conv['user_b_rol'] === $rol && (int)$conv['user_b_id'] === $id);
}

// ══════════════════════════════════════════════════════════════════════
// MENSAJES
// ══════════════════════════════════════════════════════════════════════

function _chatAttachNombres(array $rows): array {
    $pairs = array_map(fn($r) => ['rol' => $r['emisor_rol'], 'id' => (int)$r['emisor_id']], $rows);
    $names = chatBatchNombres($pairs);
    foreach ($rows as &$row) {
        $row['emisor_nombre'] = $names[$row['emisor_rol'] . ':' . $row['emisor_id']] ?? '?';
    }
    return $rows;
}

function chatMensajes(int $convId, int $limit = 80): array {
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'SELECT * FROM chat_mensajes WHERE conversacion_id = ?
         ORDER BY fecha ASC LIMIT ?');
    mysqli_stmt_bind_param($st, 'ii', $convId, $limit);
    mysqli_stmt_execute($st);
    return _chatAttachNombres(mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC));
}

function chatMensajesDespuesDe(int $convId, int $afterId): array {
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'SELECT * FROM chat_mensajes WHERE conversacion_id = ? AND id > ?
         ORDER BY fecha ASC LIMIT 100');
    mysqli_stmt_bind_param($st, 'ii', $convId, $afterId);
    mysqli_stmt_execute($st);
    return _chatAttachNombres(mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC));
}

function chatInsertarMensaje(int $convId, string $emisorRol, int $emisorId, string $contenido): int {
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'INSERT INTO chat_mensajes (conversacion_id, emisor_rol, emisor_id, contenido)
         VALUES (?, ?, ?, ?)');
    mysqli_stmt_bind_param($st, 'isis', $convId, $emisorRol, $emisorId, $contenido);
    mysqli_stmt_execute($st);
    $newId = (int)mysqli_insert_id($con);

    $st2 = mysqli_prepare($con,
        'UPDATE chat_conversaciones SET last_message_at = NOW() WHERE id = ?');
    mysqli_stmt_bind_param($st2, 'i', $convId);
    mysqli_stmt_execute($st2);

    return $newId;
}

function chatMarcarLeidos(int $convId, string $lectoRol, int $lectoId): void {
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'UPDATE chat_mensajes SET leido = 1
         WHERE conversacion_id = ? AND leido = 0
           AND NOT (emisor_rol = ? AND emisor_id = ?)');
    mysqli_stmt_bind_param($st, 'isi', $convId, $lectoRol, $lectoId);
    mysqli_stmt_execute($st);
}

// ══════════════════════════════════════════════════════════════════════
// CONTACTOS
// ══════════════════════════════════════════════════════════════════════

// $busqueda filters by name at the DB level, eliminating the PHP-side array_filter.
// An empty string matches all rows (LIKE '%%').
function chatContactosPosibles(string $rol, int $id, string $busqueda = ''): array {
    $con     = obtenerConexion();
    $results = [];
    $like    = '%' . $busqueda . '%';

    if ($rol === 'admin') {
        $st = mysqli_prepare($con,
            'SELECT idProfesor AS uid, nombreProfesor AS nombre, "profesor" AS rol
             FROM profesores WHERE nombreProfesor LIKE ? ORDER BY nombreProfesor LIMIT 200');
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        while ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($st))) $results[] = $row;

        $st = mysqli_prepare($con,
            'SELECT idEstudiante AS uid, nombreEstudiante AS nombre, "estudiante" AS rol
             FROM estudiantes WHERE nombreEstudiante LIKE ? ORDER BY nombreEstudiante LIMIT 200');
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        while ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($st))) $results[] = $row;

    } elseif ($rol === 'profesor') {
        $st = mysqli_prepare($con,
            'SELECT idEstudiante AS uid, nombreEstudiante AS nombre, "estudiante" AS rol
             FROM estudiantes WHERE nombreEstudiante LIKE ? ORDER BY nombreEstudiante LIMIT 200');
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        while ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($st))) $results[] = $row;

        $st = mysqli_prepare($con,
            'SELECT idDirector AS uid, nombreDirector AS nombre, "admin" AS rol
             FROM directores WHERE nombreDirector LIKE ? ORDER BY nombreDirector LIMIT 20');
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        while ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($st))) $results[] = $row;

    } elseif ($rol === 'tutor') {
        // Profesores vinculados a los ciclos de sus estudiantes tutelados
        $st = mysqli_prepare($con,
            'SELECT DISTINCT p.idProfesor AS uid, p.nombreProfesor AS nombre, "profesor" AS rol
             FROM profesores p
             JOIN modulo_profesor mp ON p.idProfesor = mp.idProfesor
             JOIN modulos m ON mp.idModulo = m.idModulo
             JOIN estudiantes e ON m.idCiclo = e.idCiclo
             JOIN estudiante_tutor et ON e.idEstudiante = et.idEstudiante
             WHERE et.idTutor = ? AND p.nombreProfesor LIKE ?');
        mysqli_stmt_bind_param($st, 'is', $id, $like);
        mysqli_stmt_execute($st);
        while ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($st))) $results[] = $row;

        $st = mysqli_prepare($con,
            'SELECT idDirector AS uid, nombreDirector AS nombre, "admin" AS rol
             FROM directores WHERE nombreDirector LIKE ? ORDER BY nombreDirector LIMIT 20');
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        while ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($st))) $results[] = $row;

    } else {
        $st = mysqli_prepare($con,
            'SELECT idProfesor AS uid, nombreProfesor AS nombre, "profesor" AS rol
             FROM profesores WHERE nombreProfesor LIKE ? ORDER BY nombreProfesor LIMIT 200');
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        while ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($st))) $results[] = $row;

        $st = mysqli_prepare($con,
            'SELECT idDirector AS uid, nombreDirector AS nombre, "admin" AS rol
             FROM directores WHERE nombreDirector LIKE ? ORDER BY nombreDirector LIMIT 20');
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        while ($row = mysqli_fetch_assoc(mysqli_stmt_get_result($st))) $results[] = $row;
    }

    return $results;
}
