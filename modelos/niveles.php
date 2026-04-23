<?php
require_once("conectar.php");

function listarNiveles() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM niveles ORDER BY idNivel ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function borrarNivelPorNombre($nombre) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM niveles WHERE nombreNivel = '$nombre'";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>