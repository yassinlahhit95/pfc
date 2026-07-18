<?php
require_once __DIR__ . "/conectar.php";

const HORAS_DIA_LABORAL = 6;

function calcularMaxHorasLaborables(string $fechaInicio, string $fechaFin): int {
    $ini = new DateTime($fechaInicio);
    $fin = new DateTime($fechaFin);
    $dias = 0;
    $cur  = clone $ini;
    while ($cur <= $fin) {
        if ($cur->format('N') < 6) $dias++;
        $cur->modify('+1 day');
    }
    return $dias * HORAS_DIA_LABORAL;
}

// ══════════════════════════════════════════════════════════════════════
//  RETOS
// ══════════════════════════════════════════════════════════════════════

function listarRetos() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM retos ORDER BY idReto ASC";
    $resultado = mysqli_query($con, $sql);

    $listaRetos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaRetos[] = $fila;
    }
    
    return $listaRetos;
}

function retoPerteneceAProfesor($idReto, $idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM modulo_reto mr JOIN modulo_profesor pm ON mr.idModulo = pm.idModulo WHERE mr.idReto = ? AND pm.idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idReto, $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}

function retoPerteneceACiclo($idReto, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM modulo_reto mr JOIN modulos m ON mr.idModulo = m.idModulo WHERE mr.idReto = ? AND m.idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idReto, $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}

function listarRetosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT r.* FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto JOIN modulo_profesor pm ON mr.idModulo = pm.idModulo WHERE pm.idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $listaProfesor = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $listaProfesor[] = $fila;
    }
    
    return $listaProfesor;
}

function insertarReto($nombreReto, $fechaInicio, $fechaFin, $horasReto, $listaIdsModulos) {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $stmt = mysqli_prepare($con, "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssi", $nombreReto, $fechaInicio, $fechaFin, $horasReto);
        if (!mysqli_stmt_execute($stmt)) throw new \RuntimeException('insert reto');
        $idNuevoReto = mysqli_insert_id($con);

        $stmt2 = mysqli_prepare($con, "INSERT INTO modulo_reto (idModulo, idReto) VALUES (?, ?)");
        foreach ($listaIdsModulos as $idModulo) {
            mysqli_stmt_bind_param($stmt2, "ii", $idModulo, $idNuevoReto);
            if (!mysqli_stmt_execute($stmt2)) throw new \RuntimeException('insert modulo_reto');
        }

        mysqli_commit($con);
        return true;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        return false;
    }
}

function obtenerDetalleHorasModulo($idModulo, $idRetoAExcluir = 0) {
    $con = obtenerConexion();
    $idRetoAExcluir = intval($idRetoAExcluir);
    $sql = "SELECT m.nombreModulo, m.horasMaximas, SUM(r.horasReto) AS horasOcupadas
            FROM modulos m
            LEFT JOIN modulo_reto mr ON m.idModulo = mr.idModulo
            LEFT JOIN retos r ON mr.idReto = r.idReto AND r.idReto != ?
            WHERE m.idModulo = ?
            GROUP BY m.idModulo, m.nombreModulo, m.horasMaximas";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idRetoAExcluir, $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);


    $detalle = [
        'nombreModulo' => '',
        'maximo' => 0,
        'ocupadas' => 0,
        'disponibles' => 0
    ];

    if ($fila) {
        $detalle['nombreModulo'] = $fila['nombreModulo'];
        $detalle['maximo'] = intval($fila['horasMaximas']);
        $detalle['ocupadas'] = intval($fila['horasOcupadas'] ?? 0);
        $detalle['disponibles'] = $detalle['maximo'] - $detalle['ocupadas'];
    }

    return $detalle;
}

function actualizarReto($idReto, $nombreReto, $fechaInicio, $fechaFin, $horasReto, $listaIdsModulos = null) {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $stmt = mysqli_prepare($con, "UPDATE retos SET nombreReto=?, fechaInicio=?, fechaFin=?, horasReto=? WHERE idReto=?");
        mysqli_stmt_bind_param($stmt, "sssii", $nombreReto, $fechaInicio, $fechaFin, $horasReto, $idReto);
        if (!mysqli_stmt_execute($stmt)) throw new \RuntimeException('update reto');

        if ($listaIdsModulos !== null) {
            $stmt2 = mysqli_prepare($con, "DELETE FROM modulo_reto WHERE idReto = ?");
            mysqli_stmt_bind_param($stmt2, "i", $idReto);
            if (!mysqli_stmt_execute($stmt2)) throw new \RuntimeException('delete modulo_reto');

            $stmt3 = mysqli_prepare($con, "INSERT INTO modulo_reto (idModulo, idReto) VALUES (?, ?)");
            foreach ($listaIdsModulos as $idModulo) {
                mysqli_stmt_bind_param($stmt3, "ii", $idModulo, $idReto);
                if (!mysqli_stmt_execute($stmt3)) throw new \RuntimeException('insert modulo_reto');
            }
        }

        mysqli_commit($con);
        return true;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        return false;
    }
}

// reto_archivos no tiene FOREIGN KEY (solo un índice sobre idReto), así que sin
// esta limpieza previa cada borrado de reto dejaba filas huérfanas y archivos
// físicos abandonados en public/uploads/retos/ para siempre.
function eliminarReto($idReto) {
    $con = obtenerConexion();

    foreach (obtenerArchivosReto($idReto) as $archivo) {
        $rutaFisica = __DIR__ . '/../' . ltrim($archivo['rutaArchivo'], '/');
        if (is_file($rutaFisica)) @unlink($rutaFisica);
    }
    $stmtArchivos = mysqli_prepare($con, "DELETE FROM reto_archivos WHERE idReto = ?");
    mysqli_stmt_bind_param($stmtArchivos, "i", $idReto);
    mysqli_stmt_execute($stmtArchivos);

    $sql = "DELETE FROM retos WHERE idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReto);
    $ok = mysqli_stmt_execute($stmt);

    return $ok;
}

function obtenerRetoPorId($idReto) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM retos WHERE idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReto);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $reto = mysqli_fetch_assoc($res);

    return $reto;
}

// ══════════════════════════════════════════════════════════════════════
//  ARCHIVOS DE RETO
// ══════════════════════════════════════════════════════════════════════

function registrarArchivoReto($idReto, $nombre, $ruta, $tipo) {
    $con = obtenerConexion();
    $sql = "INSERT INTO reto_archivos (idReto, nombreArchivo, rutaArchivo, tipoArchivo) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $idReto, $nombre, $ruta, $tipo);
    return mysqli_stmt_execute($stmt);
}

function obtenerArchivosReto($idReto) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM reto_archivos WHERE idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReto);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $archivos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $archivos[] = $fila;
    }
    return $archivos;
}

/**
 * Obtiene un archivo por su ID
 */
function obtenerArchivoRetoPorId($idArchivo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM reto_archivos WHERE idArchivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArchivo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}

/**
 * Elimina un archivo de la base de datos
 */
function eliminarArchivoReto($idArchivo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM reto_archivos WHERE idArchivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArchivo);
    return mysqli_stmt_execute($stmt);
}

function listarModulosDeReto($idReto) {
    $con = obtenerConexion();
    $sql = "SELECT m.*, c.nombreCiclo FROM modulos m JOIN ciclos c ON m.idCiclo = c.idCiclo JOIN modulo_reto mr ON m.idModulo = mr.idModulo WHERE mr.idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReto);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $listaModulos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $listaModulos[] = $fila;
    }
    
    return $listaModulos;
}

// inserta la nota o la actualiza si ya exsite
function calificarReto($idEstudiante, $idReto, $notaObtenida) {
    $con = obtenerConexion();

    $sqlComprobar = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
    $stmt = mysqli_prepare($con, $sqlComprobar);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idReto);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $sqlGuardar = "UPDATE calificaciones_retos SET nota = ? WHERE idEstudiante = ? AND idReto = ?";
        $stmt = mysqli_prepare($con, $sqlGuardar);
        mysqli_stmt_bind_param($stmt, "dii", $notaObtenida, $idEstudiante, $idReto);
    } else {
        $sqlGuardar = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $sqlGuardar);
        mysqli_stmt_bind_param($stmt, "iid", $idEstudiante, $idReto, $notaObtenida);
    }

    $ok = mysqli_stmt_execute($stmt);

    return $ok;
}

function eliminarCalificacionReto($idEstudiante, $idReto) {
    $con = obtenerConexion();
    $sql = "DELETE FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idReto);
    $ok = mysqli_stmt_execute($stmt);

    return $ok;
}

function obtenerCalificacionReto($idEstudiante, $idReto) {
    $con = obtenerConexion();
    $sql = "SELECT nota FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idReto);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);

    $nota = "";
    if ($fila) {
        $nota = $fila['nota'];
    }


    return $nota;
}

// Notas de un reto para varios estudiantes a la vez => [idEstudiante => nota].
// Evita el patrón N+1 de llamar obtenerCalificacionReto() una vez por estudiante
// en las vistas de evaluación (calificacionesRetos.php de admin y secretaría).
function listarCalificacionesRetoPorEstudiantes(array $idsEstudiantes, $idReto): array {
    if (!$idsEstudiantes) return [];
    $con = obtenerConexion();
    $ph = implode(',', array_fill(0, count($idsEstudiantes), '?'));
    $types = str_repeat('i', count($idsEstudiantes)) . 'i';
    $params = array_merge($idsEstudiantes, [$idReto]);
    $sql = "SELECT idEstudiante, nota FROM calificaciones_retos WHERE idEstudiante IN ($ph) AND idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $notas = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $notas[$fila['idEstudiante']] = $fila['nota'];
    }
    return $notas;
}

function listarCalificacionesRetoPorModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT cr.idEstudiante, AVG(cr.nota) AS promedio
            FROM calificaciones_retos cr
            JOIN modulo_reto mr ON cr.idReto = mr.idReto
            WHERE mr.idModulo = ?
            GROUP BY cr.idEstudiante";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $medias = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $medias[$fila['idEstudiante']] = $fila['promedio'];
    }
    
    return $medias;
}

function listarCalificacionesRetoPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT r.nombreReto, cr.nota, r.fechaInicio, r.fechaFin FROM calificaciones_retos cr JOIN retos r ON cr.idReto = r.idReto WHERE cr.idEstudiante = ? ORDER BY r.fechaInicio DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $listaHistorial = [];
    while($fila = mysqli_fetch_assoc($res)) {
        $listaHistorial[] = $fila;
    }
    
    return $listaHistorial;
}

function listarRetosPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT r.* FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto JOIN modulos m ON mr.idModulo = m.idModulo WHERE m.idCiclo = ? ORDER BY r.idReto ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $listaRetos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $listaRetos[] = $fila;
    }
    
    return $listaRetos;
}

function listarRetosPorCicloDeProfesor($idCiclo, $idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT r.* FROM retos r
            JOIN modulo_reto mr ON r.idReto = mr.idReto
            JOIN modulos m ON mr.idModulo = m.idModulo
            JOIN modulo_profesor pm ON m.idModulo = pm.idModulo
            WHERE m.idCiclo = ? AND pm.idProfesor = ?
            ORDER BY r.idReto ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idCiclo, $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    
    return $lista;
}

function obtenerPromedioRetosEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT AVG(nota) as promedio FROM calificaciones_retos WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));


    return $row['promedio'] ? floatval($row['promedio']) : 0;
}
