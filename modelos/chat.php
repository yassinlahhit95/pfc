<?php
require_once __DIR__ . '/conectar.php';

// ── Helpers ──────────────────────────────────────────────────────────────────

function chatNormalizar(string $rolA, int $idA, string $rolB, int $idB): array {
    $ka = $rolA . sprintf('%010d', $idA);
    $kb = $rolB . sprintf('%010d', $idB);
    return $ka <= $kb
        ? [$rolA, $idA, $rolB, $idB]
        : [$rolB, $idB, $rolA, $idA];
}

function chatNombreUsuario(string $rol, int $id): string {
    $con = obtenerConexion();
    switch ($rol) {
        case 'admin':
            $st = mysqli_prepare($con, 'SELECT nombreDirector FROM directores WHERE idDirector = ?');
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            $r = mysqli_stmt_get_result($st);
            $row = mysqli_fetch_assoc($r);
            return $row['nombreDirector'] ?? 'Admin';
        case 'profesor':
            $st = mysqli_prepare($con, 'SELECT nombreProfesor FROM profesores WHERE idProfesor = ?');
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            $r = mysqli_stmt_get_result($st);
            $row = mysqli_fetch_assoc($r);
            return $row['nombreProfesor'] ?? 'Profesor';
        default:
            $st = mysqli_prepare($con, 'SELECT nombreEstudiante FROM estudiantes WHERE idEstudiante = ?');
            mysqli_stmt_bind_param($st, 'i', $id);
            mysqli_stmt_execute($st);
            $r = mysqli_stmt_get_result($st);
            $row = mysqli_fetch_assoc($r);
            return $row['nombreEstudiante'] ?? 'Estudiante';
    }
}

function chatAvatarIniciales(string $nombre): string {
    $parts = preg_split('/\s+/', trim($nombre));
    $a = mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1));
    $b = mb_strtoupper(mb_substr($parts[1] ?? '', 0, 1));
    return $a . $b;
}

// ── Conversations ─────────────────────────────────────────────────────────────

function chatEncontrarOCrear(string $rolA, int $idA, string $rolB, int $idB): int {
    [$nRolA, $nIdA, $nRolB, $nIdB] = chatNormalizar($rolA, $idA, $rolB, $idB);
    $con = obtenerConexion();

    $st = mysqli_prepare($con,
        'SELECT id FROM chat_conversaciones
         WHERE user_a_rol=? AND user_a_id=? AND user_b_rol=? AND user_b_id=?');
    mysqli_stmt_bind_param($st, 'sisi', $nRolA, $nIdA, $nRolB, $nIdB);
    mysqli_stmt_execute($st);
    $r = mysqli_stmt_get_result($st);
    $row = mysqli_fetch_assoc($r);
    if ($row) return (int)$row['id'];

    $st = mysqli_prepare($con,
        'INSERT INTO chat_conversaciones (user_a_rol,user_a_id,user_b_rol,user_b_id) VALUES (?,?,?,?)');
    mysqli_stmt_bind_param($st, 'sisi', $nRolA, $nIdA, $nRolB, $nIdB);
    mysqli_stmt_execute($st);
    return (int)mysqli_insert_id($con);
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

    foreach ($rows as &$row) {
        $otherRol = ($row['user_a_rol'] === $rol && (int)$row['user_a_id'] === $id)
            ? $row['user_b_rol'] : $row['user_a_rol'];
        $otherId = ($row['user_a_rol'] === $rol && (int)$row['user_a_id'] === $id)
            ? (int)$row['user_b_id'] : (int)$row['user_a_id'];
        $row['other_rol']    = $otherRol;
        $row['other_id']     = $otherId;
        $row['other_nombre'] = chatNombreUsuario($otherRol, $otherId);
        $row['unread_count'] = (int)$row['unread_count'];
    }
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

// ── Messages ──────────────────────────────────────────────────────────────────

function chatMensajes(int $convId, int $limit = 80): array {
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'SELECT * FROM chat_mensajes WHERE conversacion_id = ?
         ORDER BY fecha ASC LIMIT ?');
    mysqli_stmt_bind_param($st, 'ii', $convId, $limit);
    mysqli_stmt_execute($st);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
    foreach ($rows as &$row) {
        $row['emisor_nombre'] = chatNombreUsuario($row['emisor_rol'], (int)$row['emisor_id']);
    }
    return $rows;
}

function chatMensajesDespuesDe(int $convId, int $afterId): array {
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'SELECT * FROM chat_mensajes WHERE conversacion_id = ? AND id > ?
         ORDER BY fecha ASC LIMIT 100');
    mysqli_stmt_bind_param($st, 'ii', $convId, $afterId);
    mysqli_stmt_execute($st);
    $rows = mysqli_fetch_all(mysqli_stmt_get_result($st), MYSQLI_ASSOC);
    foreach ($rows as &$row) {
        $row['emisor_nombre'] = chatNombreUsuario($row['emisor_rol'], (int)$row['emisor_id']);
    }
    return $rows;
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

function chatContarNoLeidos(string $rol, int $id): int {
    $con = obtenerConexion();
    $st = mysqli_prepare($con,
        'SELECT COUNT(*) FROM chat_mensajes m
         JOIN chat_conversaciones c ON c.id = m.conversacion_id
         WHERE m.leido = 0
           AND NOT (m.emisor_rol = ? AND m.emisor_id = ?)
           AND ((c.user_a_rol = ? AND c.user_a_id = ?)
             OR (c.user_b_rol = ? AND c.user_b_id = ?))');
    mysqli_stmt_bind_param($st, 'sisisi', $rol, $id, $rol, $id, $rol, $id);
    mysqli_stmt_execute($st);
    mysqli_stmt_bind_result($st, $count);
    mysqli_stmt_fetch($st);
    return (int)$count;
}

// ── Contacts ──────────────────────────────────────────────────────────────────

function chatContactosPosibles(string $rol, int $id): array {
    $con = obtenerConexion();
    $results = [];

    if ($rol === 'admin') {
        $r = mysqli_query($con,
            'SELECT idProfesor AS uid, nombreProfesor AS nombre, "profesor" AS rol
             FROM profesores ORDER BY nombreProfesor');
        while ($row = mysqli_fetch_assoc($r)) $results[] = $row;

        $r = mysqli_query($con,
            'SELECT idEstudiante AS uid, nombreEstudiante AS nombre, "estudiante" AS rol
             FROM estudiantes ORDER BY nombreEstudiante');
        while ($row = mysqli_fetch_assoc($r)) $results[] = $row;

    } elseif ($rol === 'profesor') {
        $r = mysqli_query($con,
            'SELECT idEstudiante AS uid, nombreEstudiante AS nombre, "estudiante" AS rol
             FROM estudiantes ORDER BY nombreEstudiante');
        while ($row = mysqli_fetch_assoc($r)) $results[] = $row;

        $r = mysqli_query($con,
            'SELECT idDirector AS uid, nombreDirector AS nombre, "admin" AS rol
             FROM directores ORDER BY nombreDirector');
        while ($row = mysqli_fetch_assoc($r)) $results[] = $row;

    } else {
        $r = mysqli_query($con,
            'SELECT idProfesor AS uid, nombreProfesor AS nombre, "profesor" AS rol
             FROM profesores ORDER BY nombreProfesor');
        while ($row = mysqli_fetch_assoc($r)) $results[] = $row;

        $r = mysqli_query($con,
            'SELECT idDirector AS uid, nombreDirector AS nombre, "admin" AS rol
             FROM directores ORDER BY nombreDirector');
        while ($row = mysqli_fetch_assoc($r)) $results[] = $row;
    }

    return $results;
}
