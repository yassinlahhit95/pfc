<?php
require_once("conectar.php");

function listarDirectores() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM directores ORDER BY idDirector ASC";
    $resultado = mysqli_query($conexion, $sql);
    $datos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $datos[] = $fila;
    }
    mysqli_close($conexion);
    return $datos;
}

function insertarDirector($nombre, $email, $dni, $fechaAlta) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, fechaAltaDirector) 
            VALUES ('$nombre', '$email', '$dni', '$fechaAlta')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarDirector($id, $nombre, $email, $dni, $fechaAlta) {
    $conexion = obtenerConexion();
    $sql = "UPDATE directores SET nombreDirector = '$nombre', emailDirector = '$email', 
            dniDirector = '$dni', fechaAltaDirector = '$fechaAlta' 
            WHERE idDirector = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarDirector($id) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM directores WHERE idDirector = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerDirectorPorId($id) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM directores WHERE idDirector = $id";
    $resultado = mysqli_query($conexion, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $datos;
}
?>