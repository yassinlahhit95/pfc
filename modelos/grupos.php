<?php
require_once __DIR__ . "/conectar.php";

function listarTodosLosGrupos() {
    $con = obtenerConexion();
    $sql = "SELECT g.*, c.nombreCiclo, c.abreviaturaCiclo 
            FROM grupos g
            INNER JOIN ciclos c ON g.idCiclo = c.idCiclo
            ORDER BY c.nombreCiclo ASC, g.anioEstudio ASC, g.nombreGrupo ASC";
    $res = mysqli_query($con, $sql);
    $lista = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $lista[] = $row;
    }
    return $lista;
}

function listarGruposPorCicloYAnio($idCiclo, $anioEstudio) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM grupos WHERE idCiclo = ? AND anioEstudio = ? ORDER BY nombreGrupo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "is", $idCiclo, $anioEstudio);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $row['idGrupo'] = (int)$row['idGrupo'];
        $row['idCiclo'] = (int)$row['idCiclo'];
        $lista[] = $row;
    }
    return $lista;
}

function obtenerGrupoPorId($idGrupo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM grupos WHERE idGrupo = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idGrupo);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function insertarGrupo($nombreGrupo, $idCiclo, $anioEstudio) {
    $con = obtenerConexion();
    $sql = "INSERT INTO grupos (nombreGrupo, idCiclo, anioEstudio) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sis", $nombreGrupo, $idCiclo, $anioEstudio);
    return mysqli_stmt_execute($stmt);
}

function actualizarGrupo($idGrupo, $nombreGrupo, $idCiclo, $anioEstudio) {
    $con = obtenerConexion();
    $sql = "UPDATE grupos SET nombreGrupo = ?, idCiclo = ?, anioEstudio = ? WHERE idGrupo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sisi", $nombreGrupo, $idCiclo, $anioEstudio, $idGrupo);
    return mysqli_stmt_execute($stmt);
}

function eliminarGrupo($idGrupo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM grupos WHERE idGrupo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idGrupo);
    return mysqli_stmt_execute($stmt);
}
