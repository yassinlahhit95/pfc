<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// RESULTADOS DE APRENDIZAJE (RA)
// ══════════════════════════════════════════════════════════════════════

function listarRAPorModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM resultados_aprendizaje WHERE idModulo = ? ORDER BY codigo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function insertarRA($idModulo, $codigo, $descripcion, $porcentaje) {
    $con = obtenerConexion();
    $sql = "INSERT INTO resultados_aprendizaje (idModulo, codigo, descripcion, porcentaje) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "issi", $idModulo, $codigo, $descripcion, $porcentaje);
    return mysqli_stmt_execute($stmt);
}

function eliminarRA($idRA) {
    $con = obtenerConexion();
    $sql = "DELETE FROM resultados_aprendizaje WHERE idRA = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idRA);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// CRITERIOS DE EVALUACIÓN (CE)
// ══════════════════════════════════════════════════════════════════════

function listarCEPorRA($idRA) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM criterios_evaluacion WHERE idRA = ? ORDER BY codigo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idRA);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function insertarCE($idRA, $codigo, $descripcion) {
    $con = obtenerConexion();
    $sql = "INSERT INTO criterios_evaluacion (idRA, codigo, descripcion) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $idRA, $codigo, $descripcion);
    return mysqli_stmt_execute($stmt);
}

function eliminarCE($idCE) {
    $con = obtenerConexion();
    $sql = "DELETE FROM criterios_evaluacion WHERE idCE = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCE);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// CALIFICACIONES
// ══════════════════════════════════════════════════════════════════════

function guardarCalificacionCE($idEstudiante, $idCE, $nota) {
    $con = obtenerConexion();
    $sql = "INSERT INTO calificaciones_ce (idEstudiante, idCE, nota) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE nota = VALUES(nota)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iid", $idEstudiante, $idCE, $nota);
    return mysqli_stmt_execute($stmt);
}

function obtenerCalificacionesPorModuloYEstudiante($idModulo, $idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT c.idCE, c.nota 
            FROM calificaciones_ce c
            INNER JOIN criterios_evaluacion ce ON c.idCE = ce.idCE
            INNER JOIN resultados_aprendizaje ra ON ce.idRA = ra.idRA
            WHERE ra.idModulo = ? AND c.idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idModulo, $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $notas = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $notas[$fila['idCE']] = $fila['nota'];
    }
    return $notas;
}
