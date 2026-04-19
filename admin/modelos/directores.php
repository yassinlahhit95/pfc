<?php
require_once("conectar.php");

function listarDirectores() {
    $conexion = obtenerConexion();
    $sql = "SELECT *, (SELECT nombreEstado FROM estados WHERE estados.idEstado = directores.idEstado) as nombreEstado 
            FROM directores 
            ORDER BY idDirector ASC";
    $resultado = mysqli_query($conexion, $sql);
    $datos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $datos[] = $fila;
    }
    mysqli_close($conexion);
    return $datos;
}

function insertarDirector($nombre, $email, $dni, $fechaAlta, $idEstado = 1) {
    $conexion = obtenerConexion();
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $email = mysqli_real_escape_string($conexion, $email);
    $dni = mysqli_real_escape_string($conexion, $dni);
    $fechaAlta = mysqli_real_escape_string($conexion, $fechaAlta);
    
    $sql = "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, fechaAltaDirector, idEstado) 
            VALUES ('$nombre', '$email', '$dni', '$fechaAlta', $idEstado)";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarDirector($id, $nombre, $email, $dni, $fechaAlta, $idEstado) {
    $conexion = obtenerConexion();
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $email = mysqli_real_escape_string($conexion, $email);
    $dni = mysqli_real_escape_string($conexion, $dni);
    $fechaAlta = mysqli_real_escape_string($conexion, $fechaAlta);

    $sql = "UPDATE directores SET nombreDirector = '$nombre', emailDirector = '$email', 
            dniDirector = '$dni', fechaAltaDirector = '$fechaAlta', idEstado = $idEstado 
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
    $sql = "SELECT *, (SELECT nombreEstado FROM estados WHERE estados.idEstado = directores.idEstado) as nombreEstado 
            FROM directores 
            WHERE idDirector = $id";
    $resultado = mysqli_query($conexion, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $datos;
}
?>
