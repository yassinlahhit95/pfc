<?php
require_once("conectar.php");

/**
 * Lista todos los ciclos formativos con su nivel académico
 */
function listarTodosLosCiclos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT ciclos.*, niveles.nombreNivel FROM ciclos 
                     LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel 
                     ORDER BY ciclos.idCiclo ASC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalCiclos = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalCiclos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalCiclos;
}

/**
 * Obtiene los ciclos que imparte un profesor específico
 */
function obtenerCiclosDeProfesor($idProfesorRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT ciclos.*, niveles.nombreNivel FROM ciclos 
                     JOIN ciclo_profesor ON ciclos.idCiclo = ciclo_profesor.idCiclo 
                     LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel
                     WHERE ciclo_profesor.idProfesor = $idProfesorRecibido";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaCiclosProfesor = array();
    
    while($datosCiclo = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaCiclosProfesor[] = $datosCiclo;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaCiclosProfesor;
}

/**
 * Crea un nuevo ciclo formativo y asocia sus profesores y aulas
 */
function insertarNuevoCiclo($nombreNuevo, $abreviaturaNueva, $idNivelElegido, $listaIdsProfesores, $listaIdsAulas, $precioDelCiclo = 1000.00) {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "INSERT INTO ciclos (nombreCiclo, abreviaturaCiclo, descripcionCiclo, idNivel, precioCiclo) 
                     VALUES ('$nombreNuevo', '$abreviaturaNueva', '', $idNivelElegido, $precioDelCiclo)";
    
    if (mysqli_query($conexionBaseDatos, $sentenciaSQL)) {
        $idDelCicloCreado = mysqli_insert_id($conexionBaseDatos);
        
        // Asociar profesores
        foreach ($listaIdsProfesores as $idProfesorIndividual) {
            $sqlAsociarProf = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idDelCicloCreado, $idProfesorIndividual)";
            mysqli_query($conexionBaseDatos, $sqlAsociarProf);
        }
        
        // Asociar aulas
        foreach ($listaIdsAulas as $idAulaIndividual) {
            $sqlAsociarAula = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($idDelCicloCreado, $idAulaIndividual)";
            mysqli_query($conexionBaseDatos, $sqlAsociarAula);
        }
        
        mysqli_close($conexionBaseDatos);
        return true;
    }
    
    mysqli_close($conexionBaseDatos);
    return false;
}

/**
 * Actualiza los datos de un ciclo y refresca sus asociaciones
 */
function actualizarCicloExistente($idCicloAEditar, $nombreNuevo, $abreviaturaNueva, $idNivelNuevo, $listaProfesoresNuevos, $listaAulasNuevas, $precioNuevo = 1000.00) {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "UPDATE ciclos SET 
                     nombreCiclo = '$nombreNuevo', 
                     abreviaturaCiclo = '$abreviaturaNueva', 
                     idNivel = $idNivelNuevo, 
                     precioCiclo = $precioNuevo 
                     WHERE idCiclo = $idCicloAEditar";
    
    if (mysqli_query($conexionBaseDatos, $sentenciaSQL)) {
        // Limpiar y actualizar profesores
        mysqli_query($conexionBaseDatos, "DELETE FROM ciclo_profesor WHERE idCiclo = $idCicloAEditar");
        foreach ($listaProfesoresNuevos as $idProfesorItem) {
            mysqli_query($conexionBaseDatos, "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idCicloAEditar, $idProfesorItem)");
        }
        
        // Limpiar y actualizar aulas
        mysqli_query($conexionBaseDatos, "DELETE FROM ciclo_aula WHERE idCiclo = $idCicloAEditar");
        foreach ($listaAulasNuevas as $idAulaItem) {
            mysqli_query($conexionBaseDatos, "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($idCicloAEditar, $idAulaItem)");
        }
        
        mysqli_close($conexionBaseDatos);
        return true;
    }
    
    mysqli_close($conexionBaseDatos);
    return false;
}

/**
 * Elimina un ciclo formativo
 */
function eliminarCiclo($idCicloABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM ciclos WHERE idCiclo = $idCicloABorrar";
    
    $resultadoEliminacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoEliminacion;
}

/**
 * Obtiene los datos de un ciclo por su ID
 */
function obtenerCicloPorId($idCicloBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM ciclos WHERE idCiclo = $idCicloBuscado";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosCicloEncontrado = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosCicloEncontrado;
}

/**
 * Verifica si ya existe otro ciclo con el mismo nombre
 */
function comprobarNombreEnOtroCiclo($nombreAChequear, $idCicloActual) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT idCiclo FROM ciclos WHERE nombreCiclo = '$nombreAChequear' AND idCiclo != $idCicloActual";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $cantidadRegistros = mysqli_num_rows($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    
    if ($cantidadRegistros > 0) {
        return true;
    }
    return false;
}
?>
