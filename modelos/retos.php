<?php
require_once("conectar.php");

/**
 * Obtiene el listado completo de todos los retos registrados
 */
function listarRetos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM retos ORDER BY idReto ASC";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    $listaFinalRetos = array();
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalRetos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalRetos;
}

/**
 * Lista los retos asociados a un módulo profesional específico
 */
function listarRetosFiltrados($idModuloRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT DISTINCT retos.* FROM retos 
                    JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
                    WHERE modulo_reto.idModulo = $idModuloRecibido
                    ORDER BY retos.idReto ASC";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaRetosFiltrados = array();
    
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaRetosFiltrados[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaRetosFiltrados;
}

/**
 * Obtiene los retos que pertenecen a los módulos que imparte un profesor
 */
function obtenerRetosDeProfesor($idProfesorRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT DISTINCT retos.* FROM retos 
                    JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
                    JOIN profesor_modulo ON modulo_reto.idModulo = profesor_modulo.idModulo 
                    WHERE profesor_modulo.idProfesor = $idProfesorRecibido";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaRetosProfesor = array();
    
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaRetosProfesor[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaRetosProfesor;
}

/**
 * Registra un nuevo reto y lo asocia a uno o varios módulos
 */
function insertarReto($nombreRetoRecibido, $fechaInicioRecibida, $fechaFinRecibida, $horasRetoRecibidas, $listaIdsModulos = array()) {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) 
                     VALUES ('$nombreRetoRecibido', '$fechaInicioRecibida', '$fechaFinRecibida', $horasRetoRecibidas)";
                     
    if (mysqli_query($conexionBaseDatos, $sentenciaSQL)) {
        $idDelRetoCreado = mysqli_insert_id($conexionBaseDatos);
        
        // Asociar módulos seleccionados
        foreach ($listaIdsModulos as $idModuloIndividual) {
            $sqlAsociacion = "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($idModuloIndividual, $idDelRetoCreado)";
            mysqli_query($conexionBaseDatos, $sqlAsociacion);
        }
        
        mysqli_close($conexionBaseDatos);
        return $idDelRetoCreado;
    }
    
    mysqli_close($conexionBaseDatos);
    return false;
}

/**
 * Comprueba si un módulo tiene suficientes horas disponibles para un nuevo reto
 */
function comprobarHorasDisponiblesModulo($idModuloAConsultar, $cantidadHorasNuevas, $idRetoAExcluir = 0) {
    $conexionBaseDatos = obtenerConexion();
    
    // 1. Obtener horas máximas del módulo
    $sqlModulo = "SELECT horasMaximas FROM modulos WHERE idModulo = $idModuloAConsultar";
    $resModulo = mysqli_query($conexionBaseDatos, $sqlModulo);
    $datosModulo = mysqli_fetch_assoc($resModulo);
    $totalHorasMaximas = $datosModulo['horasMaximas'];

    // 2. Sumar horas de retos ya existentes (excluyendo el reto actual si es una edición)
    $sqlSuma = "SELECT SUM(r.horasReto) as total_ocupadas FROM retos r 
               JOIN modulo_reto mr ON r.idReto = mr.idReto 
               WHERE mr.idModulo = $idModuloAConsultar AND r.idReto != $idRetoAExcluir";
               
    $resSuma = mysqli_query($conexionBaseDatos, $sqlSuma);
    $datosSuma = mysqli_fetch_assoc($resSuma);
    
    $horasYaOcupadas = 0;
    if (!empty($datosSuma['total_ocupadas'])) {
        $horasYaOcupadas = $datosSuma['total_ocupadas'];
    }

    mysqli_close($conexionBaseDatos);

    // Verificamos si la suma supera el tope del módulo
    if (($horasYaOcupadas + $cantidadHorasNuevas) > $totalHorasMaximas) {
        return false;
    }
    return true;
}

/**
 * Actualiza los datos de un reto y refresca sus asociaciones con módulos
 */
function actualizarReto($idRetoAEditar, $nombreNuevo, $fechaInicioNueva, $fechaFinNueva, $horasNuevas, $listaModulosNuevos = array()) {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "UPDATE retos SET 
                     nombreReto = '$nombreNuevo', 
                     fechaInicio = '$fechaInicioNueva', 
                     fechaFin = '$fechaFinNueva', 
                     horasReto = $horasNuevas 
                     WHERE idReto = $idRetoAEditar";
                     
    $resultadoUpdate = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    if ($resultadoUpdate == true) {
        // Limpiar asociaciones antiguas
        mysqli_query($conexionBaseDatos, "DELETE FROM modulo_reto WHERE idReto = $idRetoAEditar");
        
        // Crear nuevas asociaciones
        foreach ($listaModulosNuevos as $idModuloItem) {
            mysqli_query($conexionBaseDatos, "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($idModuloItem, $idRetoAEditar)");
        }
    }

    mysqli_close($conexionBaseDatos);
    return $resultadoUpdate;
}

/**
 * Elimina un reto permanentemente
 */
function eliminarReto($idRetoABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM retos WHERE idReto = $idRetoABorrar";
    $resultadoEliminacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    mysqli_close($conexionBaseDatos);
    return $resultadoEliminacion;
}

/**
 * Obtiene los detalles de un reto por su ID
 */
function obtenerRetoPorId($idRetoBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM retos WHERE idReto = $idRetoBuscado";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosReto = mysqli_fetch_assoc($resultadoConsulta);
    
    mysqli_close($conexionBaseDatos);
    return $datosReto;
}

/**
 * Obtiene los módulos que están vinculados a un reto específico
 */
function obtenerModulosDeReto($idRetoConsultado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT modulos.*, ciclos.nombreCiclo FROM modulos 
                    JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
                    JOIN modulo_reto ON modulos.idModulo = modulo_reto.idModulo 
                    WHERE modulo_reto.idReto = $idRetoConsultado";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaModulosDelReto = array();
    
    while ($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaModulosDelReto[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaModulosDelReto;
}

/**
 * Registra o actualiza la calificación de un estudiante en un reto
 */
function calificarReto($idEstudianteRecibido, $idRetoRecibido, $notaRecibida) {
    $conexionBaseDatos = obtenerConexion();
    
    $sqlCheck = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = $idEstudianteRecibido AND idReto = $idRetoRecibido";
    $resCheck = mysqli_query($conexionBaseDatos, $sqlCheck);
    
    if (mysqli_num_rows($resCheck) > 0) {
        $sentenciaSQL = "UPDATE calificaciones_retos SET nota = $notaRecibida WHERE idEstudiante = $idEstudianteRecibido AND idReto = $idRetoRecibido";
    } else {
        $sentenciaSQL = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES ($idEstudianteRecibido, $idRetoRecibido, $notaRecibida)";
    }
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Obtiene la nota de un estudiante en un reto concreto
 */
function obtenerCalificacion($idEstudianteRecibido, $idRetoRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT nota FROM calificaciones_retos WHERE idEstudiante = $idEstudianteRecibido AND idReto = $idRetoRecibido";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosFila = mysqli_fetch_assoc($resultadoConsulta);
    
    $notaFinal = "";
    if (!empty($datosFila)) {
        $notaFinal = $datosFila['nota'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $notaFinal;
}

/**
 * Calcula el promedio de notas de retos para cada estudiante en un módulo
 */
function listarCalificacionesRetoPorModulo($idModuloConsultado) {
    $conexionBaseDatos = obtenerConexion();
    
    // 1. Buscamos los retos asociados a este módulo
    $sqlRetos = "SELECT idReto FROM modulo_reto WHERE idModulo = $idModuloConsultado";
    $resultadoRetos = mysqli_query($conexionBaseDatos, $sqlRetos);
    
    $listaIdsRetos = array();
    while($datosReto = mysqli_fetch_assoc($resultadoRetos)) {
        $listaIdsRetos[] = $datosReto['idReto'];
    }
    
    if (empty($listaIdsRetos)) {
        mysqli_close($conexionBaseDatos);
        return array();
    }
    
    $cadenaIdsRetos = implode(",", $listaIdsRetos);
    
    // 2. Calculamos el promedio para cada estudiante
    $sentenciaSQL = "SELECT idEstudiante, AVG(nota) as promedio_nota_reto 
                    FROM calificaciones_retos 
                    WHERE idReto IN ($cadenaIdsRetos) 
                    GROUP BY idEstudiante";
            
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $mapaResultadosEstudiante = array();
    
    while($datosFila = mysqli_fetch_assoc($resultadoConsulta)) {
        $mapaResultadosEstudiante[$datosFila['idEstudiante']] = $datosFila['promedio_nota_reto'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $mapaResultadosEstudiante;
}

/**
 * Historial de retos calificados de un estudiante
 */
function listarCalificacionesRetoPorEstudiante($idEstudianteRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT r.nombreReto, cr.nota, r.fechaInicio, r.fechaFin 
                    FROM calificaciones_retos cr 
                    JOIN retos r ON cr.idReto = r.idReto 
                    WHERE cr.idEstudiante = $idEstudianteRecibido 
                    ORDER BY r.fechaInicio DESC";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalRetosEstudiante = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalRetosEstudiante[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalRetosEstudiante;
}
?>