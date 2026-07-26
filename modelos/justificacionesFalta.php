<?php
require_once __DIR__ . '/conectar.php';

function crearJustificacionFalta(int $idAsistencia, int $idEstudiante, string $motivo, ?string $archivo, string $estadoOriginal = 'ausente'): bool {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO justificaciones_falta (idAsistencia, idEstudiante, motivo, archivo, estadoOriginal)
         VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iisss", $idAsistencia, $idEstudiante, $motivo, $archivo, $estadoOriginal);
    return mysqli_stmt_execute($stmt);
}

// Última justificación (cualquier estado) por falta — para saber si una fila
// de asistencia ya tiene una solicitud en curso y no ofrecer el botón dos veces.
function obtenerJustificacionPorAsistencia(int $idAsistencia): ?array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM justificaciones_falta WHERE idAsistencia = ? ORDER BY idJustificacion DESC LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $idAsistencia);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// Ausencias/retrasos de un estudiante sin justificación pendiente ni aprobada — para avisos "necesita atención".
function contarFaltasSinJustificarPorEstudiante(int $idEstudiante): int {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT COUNT(*) AS total
         FROM asistencias a
         WHERE a.idEstudiante = ?
           AND a.estado IN ('ausente', 'retraso')
           AND NOT EXISTS (
               SELECT 1 FROM justificaciones_falta jf
               WHERE jf.idAsistencia = a.idAsistencia
                 AND jf.estado IN ('pendiente', 'aprobada')
           )");
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (int)($fila['total'] ?? 0);
}

// Pendientes de revisar, limitadas a los módulos que imparte $idProfesor.
function listarJustificacionesPendientesPorProfesor(int $idProfesor): array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT jf.*, a.fecha, a.idModulo, m.nombreModulo, e.nombreEstudiante
         FROM justificaciones_falta jf
         JOIN asistencias a ON a.idAsistencia = jf.idAsistencia
         JOIN modulos m ON m.idModulo = a.idModulo
         JOIN modulo_profesor mp ON mp.idModulo = m.idModulo
         JOIN estudiantes e ON e.idEstudiante = jf.idEstudiante
         WHERE jf.estado = 'pendiente' AND mp.idProfesor = ?
         ORDER BY jf.fechaSolicitud ASC");
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

// Resueltas (aprobada/rechazada), limitadas a los módulos que imparte $idProfesor — historial.
function listarJustificacionesResueltasPorProfesor(int $idProfesor): array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT jf.*, a.fecha, a.idModulo, m.nombreModulo, e.nombreEstudiante
         FROM justificaciones_falta jf
         JOIN asistencias a ON a.idAsistencia = jf.idAsistencia
         JOIN modulos m ON m.idModulo = a.idModulo
         JOIN modulo_profesor mp ON mp.idModulo = m.idModulo
         JOIN estudiantes e ON e.idEstudiante = jf.idEstudiante
         WHERE jf.estado IN ('aprobada', 'rechazada') AND mp.idProfesor = ?
         ORDER BY jf.fechaResolucion DESC");
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
}

// Comprueba que el profesor imparte el módulo de la falta antes de dejarle resolver.
function justificacionPerteneceAProfesor(int $idJustificacion, int $idProfesor): ?array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT jf.* FROM justificaciones_falta jf
         JOIN asistencias a ON a.idAsistencia = jf.idAsistencia
         JOIN modulo_profesor mp ON mp.idModulo = a.idModulo
         WHERE jf.idJustificacion = ? AND mp.idProfesor = ?");
    mysqli_stmt_bind_param($stmt, "ii", $idJustificacion, $idProfesor);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// Resuelve (o cambia una decisión ya tomada) una justificación. No se restringe a
// 'pendiente' a propósito: el profesor puede corregir una decisión anterior desde
// el historial (p.ej. aprobó por error y quiere rechazarla, o al revés). Al rechazar
// se restaura el estado original de la asistencia (guardado en jf.estadoOriginal),
// no un valor fijo, porque pudo ser 'ausente' o 'retraso'.
function resolverJustificacionFalta(int $idJustificacion, int $idAsistencia, bool $aprobar, int $idProfesor, string $motivoRechazo, string $estadoOriginal): bool {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $estado = $aprobar ? 'aprobada' : 'rechazada';
        $stmt = mysqli_prepare($con,
            "UPDATE justificaciones_falta
             SET estado = ?, idProfesorResuelve = ?, motivoRechazo = ?, fechaResolucion = NOW()
             WHERE idJustificacion = ?");
        mysqli_stmt_bind_param($stmt, "sisi", $estado, $idProfesor, $motivoRechazo, $idJustificacion);
        if (!mysqli_stmt_execute($stmt)) throw new \RuntimeException('update justificacion');

        $nuevoEstadoAsistencia = $aprobar ? 'justificado' : $estadoOriginal;
        $stmt2 = mysqli_prepare($con, "UPDATE asistencias SET estado = ? WHERE idAsistencia = ?");
        mysqli_stmt_bind_param($stmt2, "si", $nuevoEstadoAsistencia, $idAsistencia);
        if (!mysqli_stmt_execute($stmt2)) throw new \RuntimeException('update asistencia');

        mysqli_commit($con);
        return true;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        error_log('resolverJustificacionFalta error: ' . $e->getMessage());
        return false;
    }
}
