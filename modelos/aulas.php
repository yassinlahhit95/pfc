<?php
require_once("conectar.php");

// Ver todas las aulas
function listarAulas() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM aulas ORDER BY idAula ASC";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    $listaFinalAulas = [];
    while($fila = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalAulas[] = $fila;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalAulas;
}

// Meter aula nueva
function insertarAula($nombreDelAula) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "INSERT INTO aulas (nombreAula) VALUES ('$nombreDelAula')";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

// Borrar un aula
function eliminarAula($idAulaABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM aulas WHERE idAula = $idAulaABorrar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

// Cambiar nombre del aula
function actualizarAula($idAulaAEditar, $nombreNuevo) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE aulas SET nombreAula = '$nombreNuevo' WHERE idAula = $idAulaAEditar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

// Coger datos de una aula por ID
function obtenerAulaPorId($idAulaBuscada) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM aulas WHERE idAula = $idAulaBuscada";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosEncontrados = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosEncontrados;
}
?>