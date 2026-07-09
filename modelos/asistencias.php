<?php
require_once __DIR__ . "/conectar.php";

function guardarAsistenciasMasivo(int $idModulo, int $idProfesor, string $fecha, array $registros): bool {
    $con = obtenerConexion();
    $sql = "INSERT INTO asistencias (idEstudiante, idModulo, idProfesor, fecha, estado, observacion)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              idProfesor = VALUES(idProfesor),
              estado     = VALUES(estado),
              observacion = VALUES(observacion),
              fechaRegistro = NOW()";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) return false;

    $ok = true;
    foreach ($registros as $r) {
        $idEst  = (int)($r['idEstudiante'] ?? 0);
        $estado = $r['estado'] ?? 'presente';
        $obs    = $r['observacion'] ?? null;
        if (!in_array($estado, ['presente','ausente','retraso','justificado'], true)) {
            $estado = 'presente';
        }
        mysqli_stmt_bind_param($stmt, "iiisss", $idEst, $idModulo, $idProfesor, $fecha, $estado, $obs);
        if (!mysqli_stmt_execute($stmt)) { $ok = false; break; }
    }
    return $ok;
}

function listarAsistenciasPorModuloFecha(int $idModulo, string $fecha): array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT idEstudiante, COALESCE(estado, 'sin_registrar') AS estado, observacion
         FROM   asistencias
         WHERE  idModulo = ? AND fecha = ?");
    if (!$stmt) return [];
    mysqli_stmt_bind_param($stmt, "is", $idModulo, $fecha);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (!$res) return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;
    return $rows;
}

function listarEstudiantesDeModulo(int $idModulo): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT DISTINCT e.idEstudiante, e.nombreEstudiante
         FROM modulos m
         JOIN estudiantes e ON e.idCiclo = m.idCiclo
         WHERE m.idModulo = ? AND e.eliminado = 0
         ORDER BY e.nombreEstudiante");
    if (!$stmt) return [];
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (!$res) return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;
    return $rows;
}

function listarAsistenciasFiltradas(?int $idCiclo, ?int $idModulo, ?int $idEstudiante, ?string $fechaDesde, ?string $fechaHasta): array {
    $con = obtenerConexion();
    $where = ["1=1"];
    $params = [];
    $types  = "";

    if ($idCiclo) {
        $where[] = "m.idCiclo = ?";
        $types .= "i"; $params[] = $idCiclo;
    }
    if ($idModulo) {
        $where[] = "a.idModulo = ?";
        $types .= "i"; $params[] = $idModulo;
    }
    if ($idEstudiante) {
        $where[] = "a.idEstudiante = ?";
        $types .= "i"; $params[] = $idEstudiante;
    }
    if ($fechaDesde) {
        $where[] = "a.fecha >= ?";
        $types .= "s"; $params[] = $fechaDesde;
    }
    if ($fechaHasta) {
        $where[] = "a.fecha <= ?";
        $types .= "s"; $params[] = $fechaHasta;
    }

    $sql = "SELECT a.idAsistencia, a.fecha, a.estado, a.observacion, a.fechaRegistro,
                   e.idEstudiante, e.nombreEstudiante,
                   m.idModulo, m.nombreModulo,
                   c.nombreCiclo
            FROM   asistencias a
            JOIN   estudiantes e ON e.idEstudiante = a.idEstudiante
            JOIN   modulos m     ON m.idModulo     = a.idModulo
            JOIN   ciclos c      ON c.idCiclo      = m.idCiclo
            WHERE  " . implode(" AND ", $where) . "
            ORDER BY a.fecha DESC, e.nombreEstudiante, m.nombreModulo
            LIMIT 500";

    $stmt = mysqli_prepare($con, $sql);
    if (!empty($types)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) $rows[] = $row;
    return $rows;
}

function contarResumenAsistencia(int $idEstudiante, ?int $idModulo = null): array {
    $con = obtenerConexion();
    $sql = "SELECT estado, COUNT(*) AS total
            FROM asistencias
            WHERE idEstudiante = ?" . ($idModulo ? " AND idModulo = ?" : "") . "
            GROUP BY estado";
    $stmt = mysqli_prepare($con, $sql);
    if ($idModulo) {
        mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idModulo);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $out = ['presente' => 0, 'ausente' => 0, 'retraso' => 0, 'justificado' => 0];
    while ($row = mysqli_fetch_assoc($res)) {
        if (isset($out[$row['estado']])) $out[$row['estado']] = (int)$row['total'];
    }
    return $out;
}

// Calcula el porcentaje de absentismo injustificado de un estudiante en un módulo.
// umbral: porcentaje (0-100) a partir del cual se considera absentismo excesivo (típico: 15%).
// horasModulo: horas totales del módulo (de modulos.horasMaximas).
// Devuelve ['porcentaje'=>float, 'excede'=>bool, 'ausencias'=>int, 'justificadas'=>int, 'total'=>int]
function calcularAbsentismoModulo(int $idEstudiante, int $idModulo, int $horasModulo, float $umbral = 15.0): array {
    $resumen = contarResumenAsistencia($idEstudiante, $idModulo);
    $totalRegistros = array_sum($resumen);
    $ausencias      = $resumen['ausente'];
    // Cada sesión registrada = 1 hora (si no hay correspondencia real, es una aproximación válida).
    $porcentaje = $horasModulo > 0 ? round(($ausencias / $horasModulo) * 100, 1) : 0;
    return [
        'porcentaje'   => $porcentaje,
        'excede'       => $porcentaje >= $umbral,
        'ausencias'    => $ausencias,
        'justificadas' => $resumen['justificado'],
        'total'        => $totalRegistros,
    ];
}

function listarFechasConRegistro(int $idModulo): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT DISTINCT fecha FROM asistencias WHERE idModulo = ? ORDER BY fecha DESC");
    if (!$stmt) return [];
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (!$res) return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) $rows[] = $row['fecha'];
    return $rows;
}
