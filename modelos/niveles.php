<?php
require_once("conectar.php");

/**
 * Obtiene el listado completo de niveles académicos (Ej: Grado Medio, Grado Superior)
 */
function listarNiveles() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM niveles ORDER BY idNivel ASC";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    $listaFinalNiveles = array();
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalNiveles[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalNiveles;
}

/**
 * Elimina un nivel académico basándose en su nombre
 */
function borrarNivelPorNombre($nombreDelNivel) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM niveles WHERE nombreNivel = '$nombreDelNivel'";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}
?>