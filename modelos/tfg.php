<?php
require_once("conectar.php");

function listarTodosLosTFGs() {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.idEstudiante, estudiantes.nombreEstudiante, estudiantes.archivoTFG, estudiantes.fechaSubidaTFG, ciclos.nombreCiclo, ciclos.idCiclo 
            FROM estudiantes 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.archivoTFG != '' 
            ORDER BY estudiantes.nombreEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarTFGsFiltrados($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.idEstudiante, estudiantes.nombreEstudiante, estudiantes.archivoTFG, estudiantes.fechaSubidaTFG, ciclos.nombreCiclo, ciclos.idCiclo 
            FROM estudiantes 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.archivoTFG != '' AND estudiantes.idCiclo = $idCiclo
            ORDER BY estudiantes.nombreEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerTFGporEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "SELECT idEstudiante, nombreEstudiante, archivoTFG, fechaSubidaTFG FROM estudiantes WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function actualizarTFG($idEstudiante, $archivo) {
    $conexion = obtenerConexion();
    $fechaAhora = date('Y-m-d H:i:s');
    $sql = "UPDATE estudiantes SET archivoTFG = '$archivo', fechaSubidaTFG = '$fechaAhora' WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarTFG($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "UPDATE estudiantes SET archivoTFG = '', fechaSubidaTFG = NULL WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function contarTFGsSubidos() {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes WHERE archivoTFG != ''";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}
?>