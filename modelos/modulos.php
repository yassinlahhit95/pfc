<?php
require_once("conectar.php");

/**
 * Lista todos los módulos profesionales registrados
 */
function listarModulos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo 
                    FROM modulos 
                    LEFT JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
                    ORDER BY idModulo ASC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalModulos = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalModulos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalModulos;
}

/**
 * Obtiene los módulos que imparte un profesor específico
 */
function obtenerModulosDeProfesor($idProfesorRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM modulos 
                    JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo 
                    JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo
                    WHERE profesor_modulo.idProfesor = $idProfesorRecibido";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaModulosProfesor = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaModulosProfesor[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaModulosProfesor;
}

/**
 * Lista módulos filtrados por ciclo formativo
 */
function listarModulosPorCiclo($idCicloRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM modulos WHERE idCiclo = $idCicloRecibido";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    $listaModulosCiclo = array();
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaModulosCiclo[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaModulosCiclo;
}

/**
 * Lista módulos asignados indirectamente a un profesor a través de sus ciclos
 */
function listarModulosPorProfesor($idProfesorRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo 
                    FROM modulos 
                    JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
                    WHERE modulos.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesorRecibido) 
                    ORDER BY nombreModulo ASC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalModulos = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalModulos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalModulos;
}

/**
 * Registra un nuevo módulo profesional
 */
function insertarModulo($nombreModuloRecibido, $idCicloAsociado, $horasMaximasRecibidas) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) 
                    VALUES ('$nombreModuloRecibido', $idCicloAsociado, $horasMaximasRecibidas)";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Actualiza los datos de un módulo existente
 */
function actualizarModulo($idModuloAEditar, $nombreNuevo, $idCicloNuevo, $horasNuevas) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "UPDATE modulos SET 
                    nombreModulo = '$nombreNuevo', 
                    idCiclo = $idCicloNuevo, 
                    horasMaximas = $horasNuevas 
                    WHERE idModulo = $idModuloAEditar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Elimina un módulo profesional
 */
function eliminarModulo($idModuloABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM modulos WHERE idModulo = $idModuloABorrar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Obtiene los datos de un módulo por su ID
 */
function obtenerModuloPorId($idModuloBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM modulos WHERE idModulo = $idModuloBuscado";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosModuloEncontrado = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosModuloEncontrado;
}

/**
 * Obtiene todos los módulos pertenecientes a un ciclo específico
 */
function obtenerModulosPorCiclo($idCicloAConsultar) {
    $conexionBaseDatos = obtenerConexion();
    $idCicloSeguro = (int)$idCicloAConsultar;
    $sentenciaSQL = "SELECT * FROM modulos WHERE idCiclo = $idCicloSeguro";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalModulos = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalModulos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalModulos;
}

/**
 * Obtiene los IDs de los profesores que imparten un módulo
 */
function obtenerProfesoresDeModulo($idModuloAConsultar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT idProfesor FROM profesor_modulo WHERE idModulo = $idModuloAConsultar";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaDeIdsProfesores = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaDeIdsProfesores[] = $filaDeDatos['idProfesor'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaDeIdsProfesores;
}

/**
 * Elimina todas las asignaciones de profesores de un módulo
 */
function limpiarProfesoresModulo($idModuloALimpiar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM profesor_modulo WHERE idModulo = $idModuloALimpiar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Obtiene la suma total de horas de los retos asociados a un módulo
 */
function obtenerHorasTotalesRetosModulo($idModuloAConsultar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT SUM(retos.horasReto) as total_horas FROM retos 
                    JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
                    WHERE modulo_reto.idModulo = $idModuloAConsultar";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosSuma = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    
    $totalFinalHoras = 0;
    if ($datosSuma['total_horas'] > 0) {
        $totalFinalHoras = $datosSuma['total_horas'];
    }
    return $totalFinalHoras;
}
?>