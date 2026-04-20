<?php
require_once("conectar.php");

function listarAulas() {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT * FROM aulas ORDER BY idAula ASC";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $listaDeAulas = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeAulas[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeAulas;
}

function insertarAula($nombre) {
    $conexion = obtenerConexion();
    $sentenciaSql = "INSERT INTO aulas (nombreAula) VALUES ('$nombre')";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarAula($id) {
    $conexion = obtenerConexion();
    $sentenciaSql = "DELETE FROM aulas WHERE idAula = $id";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}
?>