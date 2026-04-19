<?php
require_once("conectar.php");

function listarTodosLosTFGs() {
    $conexion = obtenerConexion();
    $sql = "SELECT idEstudiante, nombreEstudiante, tituloTFG, archivoTFG 
            FROM estudiantes 
            WHERE archivoTFG IS NOT NULL AND archivoTFG <> '' 
            ORDER BY nombreEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $lista[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerTFGporEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "SELECT idEstudiante, nombreEstudiante, tituloTFG, archivoTFG 
            FROM estudiantes 
            WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $datos;
}

function actualizarDatosTFG($idEstudiante, $titulo, $archivo = null) {
    $conexion = obtenerConexion();
    $titulo = mysqli_real_escape_string($conexion, $titulo);
    
    if ($archivo) {
        $archivo = mysqli_real_escape_string($conexion, $archivo);
        $sql = "UPDATE estudiantes SET tituloTFG = '$titulo', archivoTFG = '$archivo' WHERE idEstudiante = $idEstudiante";
    } else {
        $sql = "UPDATE estudiantes SET tituloTFG = '$titulo' WHERE idEstudiante = $idEstudiante";
    }
    
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarArchivoTFG($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "UPDATE estudiantes SET archivoTFG = NULL WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function contarTFGsSubidos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes WHERE archivoTFG IS NOT NULL AND archivoTFG <> ''";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'] ?? 0;
}
?>
