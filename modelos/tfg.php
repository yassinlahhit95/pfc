<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
//  UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function _sqlCiclosDeProfesor() {
    return "(e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
          OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?))";
}

// ══════════════════════════════════════════════════════════════════════
//  CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function obtenerTFGporEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT idEstudiante, nombreEstudiante, archivoTFG, tituloTFG, fechaSubidaTFG FROM estudiantes WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosTFG = mysqli_fetch_assoc($resultado);
    
    return $datosTFG;
}

// ══════════════════════════════════════════════════════════════════════
//  ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarTFG($idEstudiante, $nombreArchivo) {
    $con = obtenerConexion();
    $fechaHoraActual = date('Y-m-d H:i:s');
    $sql = "UPDATE estudiantes SET archivoTFG = ?, fechaSubidaTFG = ? WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $nombreArchivo, $fechaHoraActual, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function actualizarDatosTFG($idEstudiante, $tituloTFG, $nombreArchivo = null) {
    $con = obtenerConexion();
    $fechaHoraActual = date('Y-m-d H:i:s');

    if ($nombreArchivo) {
        $sql = "UPDATE estudiantes SET tituloTFG = ?, archivoTFG = ?, fechaSubidaTFG = ? WHERE idEstudiante = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $tituloTFG, $nombreArchivo, $fechaHoraActual, $idEstudiante);
    } else {
        $sql = "UPDATE estudiantes SET tituloTFG = ? WHERE idEstudiante = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $tituloTFG, $idEstudiante);
    }

    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function eliminarTFG($idEstudiante) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET archivoTFG = '', fechaSubidaTFG = NULL WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function contarTFGsDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(DISTINCT e.idEstudiante) as total
            FROM estudiantes e
            WHERE e.eliminado = 0
              AND (e.archivoTFG IS NOT NULL AND e.archivoTFG != '')
              AND " . _sqlCiclosDeProfesor();

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    
    $total = 0;
    if ($fila) {
        $total = intval($fila['total']);
    }
    return $total;
}

function listarTFGsPorProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, c.nombreCiclo, c.idCiclo
            FROM estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE e.eliminado = 0
              AND (e.archivoTFG IS NOT NULL AND e.archivoTFG != '')
              AND " . _sqlCiclosDeProfesor() . "
            ORDER BY e.nombreEstudiante ASC";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaTFGs = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaTFGs[] = $fila;
    }
    
    return $listaTFGs;
}

// Devuelve la convocatoria "vigente": si hay extraordinaria (una recuperación
// ya se hizo), esa es la oficial, gane o pierda respecto a la ordinaria —
// a diferencia de la recuperación de módulos (que solo sustituye si es
// mayor), una convocatoria extraordinaria de TFG es un intento deliberado y
// su resultado es el que cuenta. Antes de añadir la columna `convocatoria`
// (ver migrate_db.php sección 12) solo podía existir una fila por alumno, así
// que esto no cambia nada para los datos ya existentes.
function obtenerCalificacionTFG($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_tfg WHERE idEstudiante = ?
            ORDER BY (convocatoria = 'extraordinaria') DESC LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($resultado);

    return $datos;
}

function guardarCalificacionTFG($idEstudiante, $nota, $observaciones, string $convocatoria = 'ordinaria') {
    if (!in_array($convocatoria, ['ordinaria', 'extraordinaria'], true)) $convocatoria = 'ordinaria';
    $con = obtenerConexion();
    $sql = "INSERT INTO calificaciones_tfg (idEstudiante, convocatoria, nota, observaciones) VALUES (?, ?, ?, ?) AS new_val
            ON DUPLICATE KEY UPDATE nota = new_val.nota, observaciones = new_val.observaciones";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isds", $idEstudiante, $convocatoria, $nota, $observaciones);
    return mysqli_stmt_execute($stmt);
}

function eliminarArchivoTFG($idEstudiante) {
    $con = obtenerConexion();

    $sql = "SELECT archivoTFG FROM estudiantes WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultadoBusqueda = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultadoBusqueda);

    if ($fila && $fila['archivoTFG']) {
        $rutaFisica = __DIR__ . "/../public/uploads/pfc/" . $fila['archivoTFG'];
        if (file_exists($rutaFisica)) {
            unlink($rutaFisica);
        }
    }

    $sql = "UPDATE estudiantes SET archivoTFG = '', fechaSubidaTFG = NULL WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);

    
    return $resultado;
}

function listarEvaluacionTFG($idCiclo = null) {
    $con = obtenerConexion();

    if ($idCiclo) {
        $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, e.anioEstudio,
                       c.nombreCiclo, c.abreviaturaCiclo, ct.nota, ct.observaciones, ct.idCalificacion
                FROM estudiantes e
                JOIN ciclos c ON e.idCiclo = c.idCiclo
                LEFT JOIN calificaciones_tfg ct ON e.idEstudiante = ct.idEstudiante
                WHERE e.idCiclo = ? AND e.eliminado = 0
                ORDER BY c.nombreCiclo ASC, e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    } else {
        $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, e.anioEstudio,
                       c.nombreCiclo, c.abreviaturaCiclo, ct.nota, ct.observaciones, ct.idCalificacion
                FROM estudiantes e
                JOIN ciclos c ON e.idCiclo = c.idCiclo
                LEFT JOIN calificaciones_tfg ct ON e.idEstudiante = ct.idEstudiante
                WHERE e.eliminado = 0
                ORDER BY c.nombreCiclo ASC, e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql);
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    
    return $lista;
}

function listarEvaluacionTFGporProfesor($idProfesor, $idCiclo = null) {
    $con = obtenerConexion();

    if ($idCiclo) {
        $sql = "SELECT DISTINCT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG,
                                c.nombreCiclo, c.abreviaturaCiclo, ct.nota, ct.observaciones, ct.idCalificacion
                FROM estudiantes e
                JOIN ciclos c ON e.idCiclo = c.idCiclo
                LEFT JOIN calificaciones_tfg ct ON e.idEstudiante = ct.idEstudiante
                WHERE e.idCiclo = ? AND e.eliminado = 0
                  AND " . _sqlCiclosDeProfesor() . "
                ORDER BY c.nombreCiclo ASC, e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $idCiclo, $idProfesor, $idProfesor);
    } else {
        $sql = "SELECT DISTINCT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG,
                                c.nombreCiclo, c.abreviaturaCiclo, ct.nota, ct.observaciones, ct.idCalificacion
                FROM estudiantes e
                JOIN ciclos c ON e.idCiclo = c.idCiclo
                LEFT JOIN calificaciones_tfg ct ON e.idEstudiante = ct.idEstudiante
                WHERE e.eliminado = 0 AND " . _sqlCiclosDeProfesor() . "
                ORDER BY c.nombreCiclo ASC, e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    
    return $lista;
}

