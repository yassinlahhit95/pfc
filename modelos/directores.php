<?php
require_once("conectar.php");

function listarDirectores() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM directores ORDER BY idDirector ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarDirector($nombreDirector, $emailDirector, $dniDirector, $telefonoDirector, $fechaAlta) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, telefonoDirector, fechaAltaDirector) 
            VALUES ('$nombreDirector', '$emailDirector', '$dniDirector', '$telefonoDirector', '$fechaAlta')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarDirector($idDirector, $nombreDirector, $emailDirector, $dniDirector, $telefonoDirector, $fechaAlta) {
    $conexion = obtenerConexion();
    $sql = "UPDATE directores SET nombreDirector = '$nombreDirector', emailDirector = '$emailDirector', 
            dniDirector = '$dniDirector', telefonoDirector = '$telefonoDirector', fechaAltaDirector = '$fechaAlta' 
            WHERE idDirector = $idDirector";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarDirector($idDirector) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM directores WHERE idDirector = $idDirector";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerDirectorPorId($idDirector) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM directores WHERE idDirector = $idDirector";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}
?>