<?php
require_once("conectar.php");

/**
 * Obtiene el listado completo de TFGs subidos por los estudiantes
 */
function listarTodosLosTFGs() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT estudiantes.idEstudiante, estudiantes.nombreEstudiante, estudiantes.archivoTFG, estudiantes.fechaSubidaTFG, ciclos.nombreCiclo, ciclos.idCiclo 
                    FROM estudiantes 
                    JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                    WHERE estudiantes.archivoTFG != '' 
                    ORDER BY estudiantes.nombreEstudiante ASC";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalTFGs = array();
    
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalTFGs[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalTFGs;
}

/**
 * Filtra TFGs por ciclo formativo
 */
function listarTFGsFiltrados($idCicloRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT estudiantes.idEstudiante, estudiantes.nombreEstudiante, estudiantes.archivoTFG, estudiantes.fechaSubidaTFG, ciclos.nombreCiclo, ciclos.idCiclo 
                    FROM estudiantes 
                    JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                    WHERE estudiantes.archivoTFG != '' AND estudiantes.idCiclo = $idCicloRecibido
                    ORDER BY estudiantes.nombreEstudiante ASC";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFiltradaFinal = array();
    
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFiltradaFinal[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFiltradaFinal;
}

/**
 * Obtiene los detalles del TFG de un estudiante específico
 */
function obtenerTFGporEstudiante($idEstudianteBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT idEstudiante, nombreEstudiante, archivoTFG, fechaSubidaTFG 
                    FROM estudiantes 
                    WHERE idEstudiante = $idEstudianteBuscado";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosTFG = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosTFG;
}

/**
 * Actualiza o registra un archivo TFG para un estudiante
 */
function actualizarTFG($idEstudianteRecibido, $nombreArchivoRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $fechaActual = date('Y-m-d H:i:s');
    
    $sentenciaSQL = "UPDATE estudiantes SET 
                     archivoTFG = '$nombreArchivoRecibido', 
                     fechaSubidaTFG = '$fechaActual' 
                     WHERE idEstudiante = $idEstudianteRecibido";
                     
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Elimina la asociación del TFG de un estudiante
 */
function eliminarTFG($idEstudianteRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE estudiantes SET 
                     archivoTFG = '', 
                     fechaSubidaTFG = NULL 
                     WHERE idEstudiante = $idEstudianteRecibido";
                     
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Cuenta el número total de TFGs subidos al sistema
 */
function contarTFGsSubidos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT COUNT(*) as total_subidos FROM estudiantes WHERE archivoTFG != ''";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $filaDeDatos = mysqli_fetch_assoc($resultadoConsulta);
    
    $totalTFGs = 0;
    if (!empty($filaDeDatos)) {
        $totalTFGs = $filaDeDatos['total_subidos'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $totalTFGs;
}
?>