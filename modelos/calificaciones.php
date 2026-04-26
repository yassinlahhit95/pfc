<?php
require_once("conectar.php");

/**
 * Obtiene las notas de un estudiante en un módulo específico
 */
function obtenerNotasModulo($idEstudianteBuscado, $idModuloBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEstudianteBuscado AND idModulo = $idModuloBuscado";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosEncontrados = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosEncontrados;
}

/**
 * Lista todas las calificaciones registradas en el sistema
 */
function listarCalificacionesGeneral() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo 
                    FROM calificaciones_modulos 
                    JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante 
                    JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo 
                    ORDER BY estudiantes.idEstudiante ASC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalCalificaciones = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalCalificaciones[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalCalificaciones;
}

/**
 * Obtiene una calificación específica por su ID
 */
function obtenerCalificacionPorId($idCalificacionBuscada) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM calificaciones_modulos WHERE idCalificacion = $idCalificacionBuscada";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosCalificacion = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosCalificacion;
}

/**
 * Elimina un registro de calificación
 */
function eliminarCalificacion($idCalificacionABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM calificaciones_modulos WHERE idCalificacion = $idCalificacionABorrar";
    $resultadoEliminacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    mysqli_close($conexionBaseDatos);
    return $resultadoEliminacion;
}

/**
 * Lista las calificaciones de un estudiante en particular
 */
function listarCalificacionesPorEstudiante($idDelEstudianteAConsultar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT calificaciones_modulos.*, modulos.nombreModulo 
                    FROM calificaciones_modulos 
                    JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo 
                    WHERE idEstudiante = $idDelEstudianteAConsultar";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaCalificacionesEstudiante = array();
    
    while($datosEstudiante = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaCalificacionesEstudiante[] = $datosEstudiante;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaCalificacionesEstudiante;
}

/**
 * Lista calificaciones filtradas para un profesor, ciclo y módulo
 */
function listarCalificacionesPorProfesorFiltrado($idProfesorRecibido, $idCicloElegido = 0, $idModuloElegido = 0) {
    $conexionBaseDatos = obtenerConexion();
    
    // Filtro base: solo ciclos que imparte el profesor
    $condicionWhere = "WHERE modulos.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesorRecibido)";
    
    if ($idCicloElegido > 0) {
        $condicionWhere = $condicionWhere . " AND modulos.idCiclo = $idCicloElegido";
    }
    
    if ($idModuloElegido > 0) {
        $condicionWhere = $condicionWhere . " AND modulos.idModulo = $idModuloElegido";
    }
    
    $sentenciaSQL = "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo 
                    FROM calificaciones_modulos 
                    JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante 
                    JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo 
                    $condicionWhere
                    ORDER BY estudiantes.nombreEstudiante ASC";
            
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFiltradaFinal = array();
    
    while($filaResultados = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFiltradaFinal[] = $filaResultados;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFiltradaFinal;
}

/**
 * Crea o actualiza una calificación completa de un estudiante en un módulo
 */
function actualizarOCrearNotaCompleta($idEstudianteRecibido, $idModuloRecibido, $nota1evRecibida, $nota1finalRecibida, $nota2evRecibida, $nota2finalRecibida, $observacionesRecibidas) {
    $conexionBaseDatos = obtenerConexion();
    
    // Aseguramos valores por defecto si vienen vacíos
    if ($nota1evRecibida == "") { $nota1evRecibida = "0.00"; }
    if ($nota1finalRecibida == "") { $nota1finalRecibida = "0.00"; }
    if ($nota2evRecibida == "") { $nota2evRecibida = "0.00"; }
    if ($nota2finalRecibida == "") { $nota2finalRecibida = "0.00"; }

    // Comprobar si ya existe la nota
    $sqlChequeo = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEstudianteRecibido AND idModulo = $idModuloRecibido";
    $resultadoChequeo = mysqli_query($conexionBaseDatos, $sqlChequeo);
    $cantidadRegistros = mysqli_num_rows($resultadoChequeo);
    
    if($cantidadRegistros > 0) {
        // Actualizar
        $sentenciaSQL = "UPDATE calificaciones_modulos SET 
                        nota_1ev = '$nota1evRecibida', 
                        nota_1final = '$nota1finalRecibida', 
                        nota_2ev = '$nota2evRecibida', 
                        nota_2final = '$nota2finalRecibida',
                        observaciones = '$observacionesRecibidas' 
                        WHERE idEstudiante = $idEstudianteRecibido AND idModulo = $idModuloRecibido";
    } else {
        // Insertar
        $sentenciaSQL = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final, observaciones) 
                        VALUES ($idEstudianteRecibido, $idModuloRecibido, '$nota1evRecibida', '$nota1finalRecibida', '$nota2evRecibida', '$nota2finalRecibida', '$observacionesRecibidas')";
    }
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Lista estudiantes de un módulo y sus notas actuales (para el formulario de ingreso masivo)
 */
function listarCalificacionesPorModulo($idModuloConsultado) {
    $conexionBaseDatos = obtenerConexion();
    
    // Buscamos el ID del ciclo al que pertenece el módulo
    $sqlModulo = "SELECT idCiclo FROM modulos WHERE idModulo = $idModuloConsultado";
    $resultadoModulo = mysqli_query($conexionBaseDatos, $sqlModulo);
    $datosDelModulo = mysqli_fetch_assoc($resultadoModulo);
    
    $idCicloAsociado = 0;
    if (!empty($datosDelModulo['idCiclo'])) {
        $idCicloAsociado = $datosDelModulo['idCiclo'];
    }
    
    // Obtenemos todos los estudiantes del ciclo y su nota en este módulo (si existe)
    $sentenciaSQL = "SELECT e.idEstudiante, e.nombreEstudiante, 
                           cm.nota_1ev as calificacion, cm.observaciones 
                    FROM estudiantes e 
                    LEFT JOIN calificaciones_modulos cm ON e.idEstudiante = cm.idEstudiante AND cm.idModulo = $idModuloConsultado 
                    WHERE e.idCiclo = $idCicloAsociado 
                    ORDER BY e.nombreEstudiante ASC";
            
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaEstudiantesNotas = array();
    
    while($datosFila = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaEstudiantesNotas[] = $datosFila;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaEstudiantesNotas;
}
?>
