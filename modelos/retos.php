<?php
require_once __DIR__ . "/conectar.php";

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

function listarRetos() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM retos ORDER BY nombreReto ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $retos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $retos[] = $fila;
    }
    return $retos;
}

function listarModulosDeReto($idReto) {
    $con = obtenerConexion();
    $sql = "SELECT m.* FROM modulos m
            INNER JOIN modulo_reto mr ON m.idModulo = mr.idModulo
            WHERE mr.idReto = ?
            ORDER BY m.nombreModulo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReto);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $modulos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $modulos[] = $fila;
    }
    return $modulos;
}

function obtenerArchivosReto($idReto) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM reto_archivos WHERE idReto = ? ORDER BY fechaSubida DESC";
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
