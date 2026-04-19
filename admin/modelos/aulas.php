<?php
require_once("conectar.php");

function listarAulas() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM aulas ORDER BY nombreAula ASC";
    $resultado = mysqli_query($conexion, $sql);
    $aulas = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $aulas[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $aulas;
}

function insertarAula($nombre) {
    $conexion = obtenerConexion();
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $sql = "INSERT INTO aulas (nombreAula) VALUES ('$nombre')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarAula($id) {
    $conexion = obtenerConexion();
    $id = (int)$id;
    $sql = "DELETE FROM aulas WHERE idAula = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function listarEstados() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM estados ORDER BY nombreEstado ASC";
    $resultado = mysqli_query($conexion, $sql);
    $estados = [];
    if ($resultado) {
        while($fila = mysqli_fetch_assoc($resultado)) { 
            $estados[] = $fila; 
        }
    }
    mysqli_close($conexion);
    return $estados;
}
?>
