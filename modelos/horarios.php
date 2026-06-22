<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// FRANJAS HORARIAS
// ══════════════════════════════════════════════════════════════════════

function _defaultFranjas() {
    return [
        ['inicio' => '08:00', 'fin' => '09:00', 'recreo' => false],
        ['inicio' => '09:00', 'fin' => '10:00', 'recreo' => false],
        ['inicio' => '10:00', 'fin' => '11:00', 'recreo' => false],
        ['inicio' => '11:00', 'fin' => '11:30', 'recreo' => true],
        ['inicio' => '11:30', 'fin' => '12:30', 'recreo' => false],
        ['inicio' => '12:30', 'fin' => '13:30', 'recreo' => false],
        ['inicio' => '13:30', 'fin' => '14:30', 'recreo' => false],
    ];
}

// Inserta las franjas predeterminadas para un ciclo si aún no tiene ninguna.
function _seedDefaultFranjas($idCiclo) {
    $con  = obtenerConexion();
    $sql  = "INSERT IGNORE INTO horario_franjas (idCiclo, horaInicio, horaFin, esReceso) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) return;
    foreach (_defaultFranjas() as $f) {
        $rec = $f['recreo'] ? 1 : 0;
        mysqli_stmt_bind_param($stmt, "issi", $idCiclo, $f['inicio'], $f['fin'], $rec);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
}

// Devuelve las franjas del ciclo; si no tiene, siembra las predeterminadas.
function obtenerFranjasHorario($idCiclo = 0) {
    if (!$idCiclo) return _defaultFranjas();
    $con  = obtenerConexion();
    $sql  = "SELECT horaInicio, horaFin, esReceso FROM horario_franjas WHERE idCiclo = ? ORDER BY horaInicio";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $franjas = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $franjas[] = [
            'inicio' => substr($fila['horaInicio'], 0, 5),
            'fin'    => substr($fila['horaFin'],    0, 5),
            'recreo' => (bool)$fila['esReceso'],
        ];
    }
    mysqli_stmt_close($stmt);
    if (empty($franjas)) {
        _seedDefaultFranjas($idCiclo);
        return _defaultFranjas();
    }
    return $franjas;
}

function agregarFranjaHorario($idCiclo, $inicio, $fin, $esReceso) {
    _seedDefaultFranjas($idCiclo);
    $con      = obtenerConexion();
    $esRecInt = $esReceso ? 1 : 0;
    $sql      = "INSERT INTO horario_franjas (idCiclo, horaInicio, horaFin, esReceso) VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE horaFin = VALUES(horaFin), esReceso = VALUES(esReceso)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "issi", $idCiclo, $inicio, $fin, $esRecInt);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

// Devuelve true si la franja tiene al menos una celda con módulo asignado.
function tieneCeldasEnFranja($idCiclo, $inicio) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT COUNT(*) FROM horarios WHERE idCiclo = ? AND horaInicio = ? AND idModulo > 0");
    mysqli_stmt_bind_param($stmt, "is", $idCiclo, $inicio);
    mysqli_stmt_execute($stmt);
    $row  = mysqli_fetch_row(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return (int)$row[0] > 0;
}

function eliminarFranjaHorario($idCiclo, $inicio) {
    $con = obtenerConexion();
    // 1. Limpiar asignaciones de la franja antes de eliminarla
    $stmt = mysqli_prepare($con, "DELETE FROM horarios WHERE idCiclo = ? AND horaInicio = ?");
    mysqli_stmt_bind_param($stmt, "is", $idCiclo, $inicio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    // 2. Eliminar la franja
    $stmt = mysqli_prepare($con, "DELETE FROM horario_franjas WHERE idCiclo = ? AND horaInicio = ?");
    mysqli_stmt_bind_param($stmt, "is", $idCiclo, $inicio);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS DEL HORARIO
// ══════════════════════════════════════════════════════════════════════

// Devuelve los días laborables del horario.
function obtenerDiasHorario() {
    return ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
}

// Devuelve las celdas asignadas de un ciclo, indexadas por "Dia|HH:MM".
function listarHorarioPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT h.idHorario, h.diaSemana, h.horaInicio, h.horaFin,
                   h.idModulo, h.idProfesor, h.idAula,
                   m.nombreModulo, p.nombreProfesor,
                   a.codigoAula, a.nombreAula
            FROM horarios h
            LEFT JOIN modulos m    ON h.idModulo   = m.idModulo
            LEFT JOIN profesores p ON h.idProfesor = p.idProfesor
            LEFT JOIN aulas a      ON h.idAula     = a.idAula
            WHERE h.idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $celdas = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $clave = $fila['diaSemana'] . '|' . substr($fila['horaInicio'], 0, 5);
        $celdas[$clave] = $fila;
    }
    mysqli_stmt_close($stmt);
    return $celdas;
}

// Devuelve los pares módulo+profesor disponibles para un ciclo.
function listarAsignacionesPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT m.idModulo, m.nombreModulo, p.idProfesor, p.nombreProfesor
            FROM modulos m
            JOIN modulo_profesor mp ON mp.idModulo = m.idModulo
            JOIN profesores p       ON p.idProfesor = mp.idProfesor
            WHERE m.idCiclo = ?
            ORDER BY m.nombreModulo ASC, p.nombreProfesor ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_stmt_close($stmt);
    return $lista;
}

// Devuelve todas las ocupaciones de aulas en todo el centro (para tutores).
// Resultado: [ 'Lunes|08:00' => [ idAula => ['codigoAula'=>…,'nombreCiclo'=>…,'nombreModulo'=>…], … ], … ]
// Devuelve las franjas horarias de un módulo en un día concreto (para asistencias).
function listarClasesDeModuloPorDia(int $idModulo, string $diaSemana): array {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT h.horaInicio, h.horaFin,
                a.codigoAula, a.nombreAula,
                p.nombreProfesor
         FROM   horarios h
         LEFT JOIN aulas     a ON a.idAula     = h.idAula
         LEFT JOIN profesores p ON p.idProfesor = h.idProfesor
         WHERE  h.idModulo = ? AND h.diaSemana = ?
         ORDER BY h.horaInicio");
    if (!$stmt) return [];
    mysqli_stmt_bind_param($stmt, "is", $idModulo, $diaSemana);
    mysqli_stmt_execute($stmt);
    $res  = mysqli_stmt_get_result($stmt);
    if (!$res) return [];
    $rows = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $r['horaInicio'] = substr($r['horaInicio'], 0, 5);
        $r['horaFin']    = substr($r['horaFin'],    0, 5);
        $rows[] = $r;
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

function listarOcupacionAulasEscuela() {
    $con = obtenerConexion();
    $sql = "SELECT h.diaSemana, h.horaInicio, h.idAula,
                   a.codigoAula, a.nombreAula,
                   c.nombreCiclo, c.abreviaturaCiclo,
                   m.nombreModulo
            FROM horarios h
            JOIN aulas a      ON h.idAula    = a.idAula
            JOIN ciclos c     ON h.idCiclo   = c.idCiclo
            LEFT JOIN modulos m ON h.idModulo = m.idModulo
            WHERE h.idAula IS NOT NULL
            ORDER BY h.diaSemana, h.horaInicio, a.codigoAula";
    $resultado = mysqli_query($con, $sql);
    $mapa = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $clave = $fila['diaSemana'] . '|' . substr($fila['horaInicio'], 0, 5);
        $mapa[$clave][(int)$fila['idAula']] = $fila;
    }
    return $mapa;
}

// Devuelve el conflicto si el aula ya está ocupada en esa franja por otro ciclo, o null si está libre.
function aulaOcupadaPorOtro($idAula, $dia, $horaInicio, $idCicloActual) {
    if (empty($idAula)) return null;
    $con = obtenerConexion();
    $sql = "SELECT h.idCiclo, c.nombreCiclo, c.abreviaturaCiclo, m.nombreModulo
            FROM horarios h
            JOIN ciclos c       ON h.idCiclo  = c.idCiclo
            LEFT JOIN modulos m ON h.idModulo = m.idModulo
            WHERE h.idAula = ? AND h.diaSemana = ? AND h.horaInicio = ? AND h.idCiclo <> ?
            LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "issi", $idAula, $dia, $horaInicio, $idCicloActual);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $fila;
}

// Devuelve el conflicto si el profesor ya imparte en esa franja en otro ciclo, o null si está libre.
function profesorOcupadoPorOtro($idProfesor, $dia, $horaInicio, $idCicloActual) {
    if (empty($idProfesor)) return null;
    $con = obtenerConexion();
    $sql = "SELECT h.idCiclo, c.nombreCiclo, c.abreviaturaCiclo, m.nombreModulo
            FROM horarios h
            JOIN ciclos c       ON h.idCiclo  = c.idCiclo
            LEFT JOIN modulos m ON h.idModulo = m.idModulo
            WHERE h.idProfesor = ? AND h.diaSemana = ? AND h.horaInicio = ? AND h.idCiclo <> ?
            LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "issi", $idProfesor, $dia, $horaInicio, $idCicloActual);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return $fila;
}

// Devuelve la ocupación semanal de un aula concreta, indexada por "Dia|HH:MM".
function listarOcupacionAula($idAula) {
    $con = obtenerConexion();
    $sql = "SELECT h.diaSemana, h.horaInicio, h.idModulo,
                   c.nombreCiclo, c.abreviaturaCiclo,
                   m.nombreModulo, p.nombreProfesor
            FROM horarios h
            JOIN ciclos c          ON h.idCiclo  = c.idCiclo
            LEFT JOIN modulos m    ON h.idModulo = m.idModulo
            LEFT JOIN profesores p ON h.idProfesor = p.idProfesor
            WHERE h.idAula = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idAula);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $celdas = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $clave = $fila['diaSemana'] . '|' . substr($fila['horaInicio'], 0, 5);
        $celdas[$clave] = $fila;
    }
    mysqli_stmt_close($stmt);
    return $celdas;
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES / ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

// Inserta o actualiza la celda de una franja (una por ciclo+día+franja). $idAula puede ser null.
function guardarCeldaHorario($idCiclo, $dia, $horaInicio, $horaFin, $idModulo, $idProfesor, $idAula = null) {
    $con = obtenerConexion();
    $sql = "INSERT INTO horarios (idCiclo, diaSemana, horaInicio, horaFin, idModulo, idProfesor, idAula)
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                horaFin    = VALUES(horaFin),
                idModulo   = VALUES(idModulo),
                idProfesor = VALUES(idProfesor),
                idAula     = VALUES(idAula)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isssiii", $idCiclo, $dia, $horaInicio, $horaFin, $idModulo, $idProfesor, $idAula);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function borrarCeldaHorario($idCiclo, $dia, $horaInicio) {
    $con = obtenerConexion();
    $sql = "DELETE FROM horarios WHERE idCiclo = ? AND diaSemana = ? AND horaInicio = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $idCiclo, $dia, $horaInicio);
    $res = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $res;
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES DE PRESENTACIÓN
// ══════════════════════════════════════════════════════════════════════

// Color estable por módulo basado en la paleta del proyecto.
function horarioColorModulo($idModulo) {
    $paleta = ['#667eea', '#0ea5e9', '#10b981', '#e482ae', '#5260b2', '#f59e0b', '#ef4444', '#14b8a6'];
    return $paleta[((int)$idModulo) % count($paleta)];
}

// Iniciales de hasta 2 letras para el avatar de la tarjeta del horario.
function horarioIniciales($texto) {
    $palabras = preg_split('/\s+/', trim($texto));
    $ini = '';
    foreach ($palabras as $p) {
        if ($p !== '' && strlen($ini) < 2) $ini .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $ini ?: '?';
}
