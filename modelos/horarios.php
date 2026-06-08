<?php
require_once __DIR__ . "/conectar.php";

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

function _seedDefaultFranjas($idCiclo) {
    $con = obtenerConexion();
    foreach (_defaultFranjas() as $f) {
        $sql  = "INSERT IGNORE INTO horario_franjas (idCiclo, horaInicio, horaFin, esReceso) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        $rec  = $f['recreo'] ? 1 : 0;
        mysqli_stmt_bind_param($stmt, "issi", $idCiclo, $f['inicio'], $f['fin'], $rec);
        mysqli_stmt_execute($stmt);
    }
}

/**
 * Franjas horarias de un ciclo. Si no hay personalizadas, usa las predeterminadas.
 * Pasa $idCiclo = 0 para obtener solo las predeterminadas sin tocar la BD.
 */
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

    if (empty($franjas)) {
        _seedDefaultFranjas($idCiclo);
        return _defaultFranjas();
    }

    return $franjas;
}

function agregarFranjaHorario($idCiclo, $inicio, $fin, $esReceso) {
    _seedDefaultFranjas($idCiclo); // garantiza que los defaults ya están en BD
    $con       = obtenerConexion();
    $esRecInt  = $esReceso ? 1 : 0;
    $sql       = "INSERT INTO horario_franjas (idCiclo, horaInicio, horaFin, esReceso) VALUES (?, ?, ?, ?)
                  ON DUPLICATE KEY UPDATE horaFin = VALUES(horaFin), esReceso = VALUES(esReceso)";
    $stmt      = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "issi", $idCiclo, $inicio, $fin, $esRecInt);
    return mysqli_stmt_execute($stmt);
}

function tieneCeldasEnFranja($idCiclo, $inicio) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT COUNT(*) FROM horarios WHERE idCiclo = ? AND horaInicio = ?");
    mysqli_stmt_bind_param($stmt, "is", $idCiclo, $inicio);
    mysqli_stmt_execute($stmt);
    $row  = mysqli_fetch_row(mysqli_stmt_get_result($stmt));
    return (int)$row[0] > 0;
}

function eliminarFranjaHorario($idCiclo, $inicio) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM horario_franjas WHERE idCiclo = ? AND horaInicio = ?");
    mysqli_stmt_bind_param($stmt, "is", $idCiclo, $inicio);
    return mysqli_stmt_execute($stmt);
}

/**
 * Dias laborables del horario.
 */
function obtenerDiasHorario() {
    return ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
}

/**
 * Color estable para un modulo (paleta del proyecto). Usado por el panel y la tabla.
 */
function horarioColorModulo($idModulo) {
    $paleta = ['#667eea', '#0ea5e9', '#10b981', '#e482ae', '#5260b2', '#f59e0b', '#ef4444', '#14b8a6'];
    return $paleta[((int)$idModulo) % count($paleta)];
}

/**
 * Iniciales (max 2 letras) de un texto, para el avatar de la tarjeta.
 */
function horarioIniciales($texto) {
    $palabras = preg_split('/\s+/', trim($texto));
    $ini = '';
    foreach ($palabras as $p) {
        if ($p !== '' && strlen($ini) < 2) $ini .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $ini ?: '?';
}

/**
 * Devuelve las celdas asignadas de un ciclo, indexadas por "Dia|HH:MM"
 * para que la vista pueda localizar cada celda en O(1).
 */
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
    return $celdas;
}

/**
 * Pares modulo + profesor disponibles para un ciclo (tarjetas arrastrables del director).
 * Sale de modulo_profesor uniendo con los modulos de ese ciclo.
 */
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
    return $lista;
}

/**
 * Inserta o actualiza la asignacion de una celda (una por ciclo+dia+franja).
 * $idAula puede ser null (sin aula asignada todavia).
 */
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
    return mysqli_stmt_execute($stmt);
}

/**
 * Devuelve datos del conflicto si el AULA ya esta ocupada en esa franja por OTRO ciclo,
 * o null si esta libre. Se excluye la propia celda (mismo ciclo+dia+franja).
 */
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
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

/**
 * Devuelve datos del conflicto si el PROFESOR ya imparte en esa franja en OTRO ciclo,
 * o null si esta libre. Se excluye la propia celda.
 */
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
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

/**
 * Ocupacion semanal de un aula concreta (todos los ciclos), indexada por "Dia|HH:MM".
 */
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
    return $celdas;
}

/**
 * Elimina la asignacion de una celda concreta.
 */
function borrarCeldaHorario($idCiclo, $dia, $horaInicio) {
    $con = obtenerConexion();
    $sql = "DELETE FROM horarios WHERE idCiclo = ? AND diaSemana = ? AND horaInicio = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $idCiclo, $dia, $horaInicio);
    return mysqli_stmt_execute($stmt);
}
