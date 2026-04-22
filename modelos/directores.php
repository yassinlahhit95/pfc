<?php
require_once("conectar.php");

function listarDirectores() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM directores ORDER BY idDirector ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarDirector($nombre, $email, $dni, $fechaAlta) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, fechaAltaDirector) VALUES ('$nombre', '$email', '$dni', '$fechaAlta')");
    mysqli_close($conexion);
    return $resultado;
}

function actualizarDirector($idDirector, $nombre, $email, $dni, $fechaAlta) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "UPDATE directores SET nombreDirector = '$nombre', emailDirector = '$email', dniDirector = '$dni', fechaAltaDirector = '$fechaAlta' WHERE idDirector = $idDirector");
    mysqli_close($conexion);
    return $resultado;
}

function eliminarDirector($idDirector) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM directores WHERE idDirector = $idDirector");
    mysqli_close($conexion);
    return $resultado;
}

function obtenerDirectorPorId($idDirector) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM directores WHERE idDirector = $idDirector");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}
?>
