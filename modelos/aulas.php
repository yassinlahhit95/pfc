<?php
require_once("conectar.php");

/**
 * Obtiene el listado completo de aulas disponibles
 */
function listarAulas() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM aulas ORDER BY idAula ASC";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    $listaFinalAulas = array();
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalAulas[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalAulas;
}

/**
 * Registra una nueva aula en el sistema
 */
function insertarAula($nombreDelAula) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "INSERT INTO aulas (nombreAula) VALUES ('$nombreDelAula')";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Elimina una aula por su ID
 */
function eliminarAula($idAulaABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM aulas WHERE idAula = $idAulaABorrar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Actualiza el nombre de una aula existente
 */
function actualizarAula($idAulaAEditar, $nombreNuevo) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE aulas SET nombreAula = '$nombreNuevo' WHERE idAula = $idAulaAEditar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Obtiene los datos de una aula específica por su ID
 */
function obtenerAulaPorId($idAulaBuscada) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM aulas WHERE idAula = $idAulaBuscada";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosEncontrados = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosEncontrados;
}
?>