<?php
require_once __DIR__ . "/conectar.php";

function listarRetosPorModulo($idModulo, $soloPublicados = true) {
    $con = obtenerConexion();
    $sql = "SELECT r.*, p.nombreProfesor 
            FROM aula_retos r
            JOIN profesores p ON r.idProfesor = p.idProfesor
            WHERE r.idModulo = ?";
    
    if ($soloPublicados) {
        $sql .= " AND r.publicado = 1";
    }
    $sql .= " ORDER BY r.fechaCreacion DESC";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    $retos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $retos[] = $fila;
    }
    return $retos;
}

function obtenerRetoPorId($idReto) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aula_retos WHERE idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReto);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}

function obtenerEntregaReto($idReto, $idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aula_retos_entregas WHERE idReto = ? AND idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idReto, $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}
