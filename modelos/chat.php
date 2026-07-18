<?php
require_once __DIR__ . '/conectar.php';
require_once __DIR__ . '/../include/Cache.php';

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

// Comprueba que un usuario (rol, id) existe realmente en su tabla.
function chatUsuarioExiste(string $rol, int $id): bool {
    $con = obtenerConexion();
    $mapa = [
        'admin'      => ['directores',  'idDirector',   ''],
        'profesor'   => ['profesores',  'idProfesor',   ''],
        'estudiante' => ['estudiantes', 'idEstudiante', ' AND eliminado = 0'],
        'tutor'      => ['tutores',     'idTutor',      ''],
        'secretaria' => ['secretarias', 'idSecretaria', ''],
    ];
    if (!isset($mapa[$rol]) || $id <= 0) return false;
    [$tabla, $col, $extra] = $mapa[$rol];
    $st = mysqli_prepare($con, "SELECT 1 FROM `$tabla` WHERE `$col` = ?$extra LIMIT 1");
    mysqli_stmt_bind_param($st, 'i', $id);
    mysqli_stmt_execute($st);
    return mysqli_fetch_row(mysqli_stmt_get_result($st)) !== null;
}

// Política de contactos: qué pares de roles pueden conversar entre sí.
// Debe mantenerse alineada con chatContactosPosibles().
function chatParEsPermitido(string $rolA, int $idA, string $rolB, int $idB): bool {
    if (!chatUsuarioExiste($rolA, $idA) || !chatUsuarioExiste($rolB, $idB)) return false;

    $par = [$rolA, $rolB];
    sort($par);
    $clave = implode('-', $par);

    $paresPermitidos = [
        'admin-profesor', 'admin-estudiante', 'admin-secretaria', 'admin-tutor',
        'profesor-secretaria', 'estudiante-secretaria', 'secretaria-tutor',
        'estudiante-profesor', 'profesor-tutor',
        'estudiante-estudiante',
    ];
    if (!in_array($clave, $paresPermitidos, true)) return false;

    // Estudiante ↔ estudiante: solo compañeros del mismo ciclo
    if ($clave === 'estudiante-estudiante') {
        $con = obtenerConexion();
        $st = mysqli_prepare($con,
            'SELECT (SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?) =
                    (SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?) AS mismo');
        mysqli_stmt_bind_param($st, 'ii', $idA, $idB);
        mysqli_stmt_execute($st);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
        return !empty($row['mismo']);
    }

    return true;
}

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
        case 'secretaria':
            $st = mysqli_prepare($con, 'SELECT nombreSecretaria FROM secretarias WHERE idSecretaria = ?');
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
            return $cache[$key] = ($row['nombreSecretaria'] ?? 'Secretaria');
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
        'secretaria' => ['secretarias', 'idSecretaria',  'nombreSecretaria'],
    ];
    $defaults = ['admin' => 'Admin', 'profesor' => 'Profesor', 'tutor' => 'Tutor', 'estudiante' => 'Estudiante', 'secretaria' => 'Secretaria'];
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

function chatContarNoLeidos(string $rol, int $id): int {
    return Cache::remember("chat_no_leidos_{$rol}_{$id}", 10, function () use ($rol, $id) {
        $con = obtenerConexion();
        $st = mysqli_prepare($con,
            'SELECT COUNT(*) AS total FROM chat_mensajes m
             JOIN chat_conversaciones c ON m.conversacion_id = c.id
             WHERE m.leido = 0
               AND NOT (m.emisor_rol = ? AND m.emisor_id = ?)
               AND (  (c.user_a_rol = ? AND c.user_a_id = ?)
                   OR (c.user_b_rol = ? AND c.user_b_id = ?))');
        mysqli_stmt_bind_param($st, 'sisisi', $rol, $id, $rol, $id, $rol, $id);
        mysqli_stmt_execute($st);
        $r = mysqli_stmt_get_result($st);
        $row = mysqli_fetch_assoc($r);
        return (int)($row['total'] ?? 0);
    });
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
            "SELECT idProfesor AS uid, nombreProfesor AS nombre, 'profesor' AS rol
             FROM profesores WHERE nombreProfesor LIKE ? ORDER BY nombreProfesor LIMIT 200");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idEstudiante AS uid, nombreEstudiante AS nombre, 'estudiante' AS rol
             FROM estudiantes WHERE eliminado = 0 AND nombreEstudiante LIKE ? ORDER BY nombreEstudiante LIMIT 200");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idSecretaria AS uid, nombreSecretaria AS nombre, 'secretaria' AS rol
             FROM secretarias WHERE nombreSecretaria LIKE ? ORDER BY nombreSecretaria LIMIT 20");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idTutor AS uid, nombreTutor AS nombre, 'tutor' AS rol
             FROM tutores WHERE nombreTutor LIKE ? ORDER BY nombreTutor LIMIT 200");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

    } elseif ($rol === 'secretaria') {
        $st = mysqli_prepare($con,
            "SELECT idDirector AS uid, nombreDirector AS nombre, 'admin' AS rol
             FROM directores WHERE nombreDirector LIKE ? ORDER BY nombreDirector LIMIT 20");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idProfesor AS uid, nombreProfesor AS nombre, 'profesor' AS rol
             FROM profesores WHERE nombreProfesor LIKE ? ORDER BY nombreProfesor LIMIT 200");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idEstudiante AS uid, nombreEstudiante AS nombre, 'estudiante' AS rol
             FROM estudiantes WHERE eliminado = 0 AND nombreEstudiante LIKE ? ORDER BY nombreEstudiante LIMIT 200");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idTutor AS uid, nombreTutor AS nombre, 'tutor' AS rol
             FROM tutores WHERE nombreTutor LIKE ? ORDER BY nombreTutor LIMIT 200");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

    } elseif ($rol === 'profesor') {
        $st = mysqli_prepare($con,
            "SELECT idEstudiante AS uid, nombreEstudiante AS nombre, 'estudiante' AS rol
             FROM estudiantes WHERE eliminado = 0 AND nombreEstudiante LIKE ? ORDER BY nombreEstudiante LIMIT 200");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idDirector AS uid, nombreDirector AS nombre, 'admin' AS rol
             FROM directores WHERE nombreDirector LIKE ? ORDER BY nombreDirector LIMIT 20");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        // profesor-secretaria y profesor-tutor están permitidos en chatParEsPermitido()
        // pero faltaban aquí, dejando esos contactos inalcanzables desde la lista.
        $st = mysqli_prepare($con,
            "SELECT idSecretaria AS uid, nombreSecretaria AS nombre, 'secretaria' AS rol
             FROM secretarias WHERE nombreSecretaria LIKE ? ORDER BY nombreSecretaria LIMIT 20");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idTutor AS uid, nombreTutor AS nombre, 'tutor' AS rol
             FROM tutores WHERE nombreTutor LIKE ? ORDER BY nombreTutor LIMIT 200");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

    } elseif ($rol === 'tutor') {
        // Tutors can contact any professor at the school + directors
        $st = mysqli_prepare($con,
            "SELECT idProfesor AS uid, nombreProfesor AS nombre, 'profesor' AS rol
             FROM profesores WHERE nombreProfesor LIKE ? ORDER BY nombreProfesor LIMIT 200");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idDirector AS uid, nombreDirector AS nombre, 'admin' AS rol
             FROM directores WHERE nombreDirector LIKE ? ORDER BY nombreDirector LIMIT 20");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        $st = mysqli_prepare($con,
            "SELECT idSecretaria AS uid, nombreSecretaria AS nombre, 'secretaria' AS rol
             FROM secretarias WHERE nombreSecretaria LIKE ? ORDER BY nombreSecretaria LIMIT 20");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

    } else {
        // Estudiante: profesores del ciclo (via ciclo_profesor o modulo_profesor), directores, compañeros
        $stCiclo = mysqli_prepare($con, 'SELECT idCiclo FROM estudiantes WHERE idEstudiante = ?');
        mysqli_stmt_bind_param($stCiclo, 'i', $id);
        mysqli_stmt_execute($stCiclo);
        $rowCiclo = mysqli_fetch_assoc(mysqli_stmt_get_result($stCiclo));
        $idCiclo  = (int)($rowCiclo['idCiclo'] ?? 0);

        if ($idCiclo > 0) {
            $st = mysqli_prepare($con,
                "SELECT DISTINCT p.idProfesor AS uid, p.nombreProfesor AS nombre, 'profesor' AS rol
                 FROM profesores p
                 WHERE p.idProfesor IN (
                     SELECT mp.idProfesor FROM modulo_profesor mp
                     JOIN modulos m ON mp.idModulo = m.idModulo WHERE m.idCiclo = ?
                     UNION
                     SELECT cp.idProfesor FROM ciclo_profesor cp WHERE cp.idCiclo = ?
                 ) AND p.nombreProfesor LIKE ?
                 ORDER BY p.nombreProfesor LIMIT 50");
            mysqli_stmt_bind_param($st, 'iis', $idCiclo, $idCiclo, $like);
            mysqli_stmt_execute($st);
            $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;
        }

        // Fallback: if no ciclo-specific professors found, show all professors
        if (empty($results)) {
            $st = mysqli_prepare($con,
                "SELECT idProfesor AS uid, nombreProfesor AS nombre, 'profesor' AS rol
                 FROM profesores WHERE nombreProfesor LIKE ? ORDER BY nombreProfesor LIMIT 50");
            mysqli_stmt_bind_param($st, 's', $like);
            mysqli_stmt_execute($st);
            $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;
        }

        $st = mysqli_prepare($con,
            "SELECT idDirector AS uid, nombreDirector AS nombre, 'admin' AS rol
             FROM directores WHERE nombreDirector LIKE ? ORDER BY nombreDirector LIMIT 20");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;

        if ($idCiclo > 0) {
            $st = mysqli_prepare($con,
                "SELECT idEstudiante AS uid, nombreEstudiante AS nombre, 'estudiante' AS rol
                 FROM estudiantes
                 WHERE idCiclo = ? AND idEstudiante != ? AND eliminado = 0 AND nombreEstudiante LIKE ?
                 ORDER BY nombreEstudiante LIMIT 100");
            mysqli_stmt_bind_param($st, 'iis', $idCiclo, $id, $like);
            mysqli_stmt_execute($st);
            $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;
        }

        $st = mysqli_prepare($con,
            "SELECT idSecretaria AS uid, nombreSecretaria AS nombre, 'secretaria' AS rol
             FROM secretarias WHERE nombreSecretaria LIKE ? ORDER BY nombreSecretaria LIMIT 20");
        mysqli_stmt_bind_param($st, 's', $like);
        mysqli_stmt_execute($st);
        $res = mysqli_stmt_get_result($st);
        while ($row = mysqli_fetch_assoc($res)) $results[] = $row;
    }

    return $results;
}
