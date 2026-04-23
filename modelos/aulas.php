<?php
require_once("conectar.php");

function listarAulas() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM aulas ORDER BY idAula ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarAula($nombre) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO aulas (nombreAula) VALUES ('$nombre')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarAula($idAula) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM aulas WHERE idAula = $idAula";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarAula($idAula, $nombre) {
    $conexion = obtenerConexion();
    $sql = "UPDATE aulas SET nombreAula = '$nombre' WHERE idAula = $idAula";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerAulaPorId($idAula) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM aulas WHERE idAula = $idAula";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}
?>