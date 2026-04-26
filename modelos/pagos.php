<?php
require_once("conectar.php");

/**
 * Lista todos los pagos registrados con información del estudiante y el ciclo
 */
function listarTodosLosPagos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo 
                    FROM pagos 
                    JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante 
                    JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
                    ORDER BY idPago DESC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalPagos = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalPagos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalPagos;
}

/**
 * Lista pagos filtrados por un ciclo específico
 */
function listarPagosFiltrados($idCicloRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo 
                    FROM pagos 
                    JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante 
                    JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
                    WHERE estudiantes.idCiclo = $idCicloRecibido
                    ORDER BY idPago DESC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaPagosFiltrados = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaPagosFiltrados[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaPagosFiltrados;
}

/**
 * Obtiene el historial de pagos de un estudiante concreto
 */
function obtenerPagosPorEstudiante($idEstudianteRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM pagos 
                    WHERE idEstudiante = $idEstudianteRecibido 
                    ORDER BY fechaPago DESC";
    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaPagosEstudiante = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaPagosEstudiante[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaPagosEstudiante;
}

/**
 * Registra un pago completo con todas las fechas necesarias
 */
function insertarPagoCompleto($idEstudianteRecibido, $montoRecibido, $tipoPagoRecibido, $fechaPagoRecibida, $fechaProximoRecibida) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago) 
                    VALUES ($idEstudianteRecibido, $montoRecibido, '$tipoPagoRecibido', '$fechaPagoRecibida', '$fechaProximoRecibida')";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Actualiza la información de un pago existente
 */
function actualizarPago($idPagoAEditar, $idEstudianteNuevo, $montoNuevo, $tipoPagoNuevo, $fechaPagoNueva, $fechaProximoNuevo, $comprobanteNuevo = "") {
    $conexionBaseDatos = obtenerConexion();
    
    $sentenciaSQL = "UPDATE pagos SET 
                     idEstudiante = $idEstudianteNuevo, 
                     monto = $montoNuevo, 
                     tipoPago = '$tipoPagoNuevo', 
                     fechaPago = '$fechaPagoNueva', 
                     fechaProximoPago = '$fechaProximoNuevo'";
    
    if (!empty($comprobanteNuevo)) {
        $sentenciaSQL = $sentenciaSQL . ", comprobante = '$comprobanteNuevo'";
    }
    
    $sentenciaSQL = $sentenciaSQL . " WHERE idPago = $idPagoAEditar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Calcula el estado financiero de un estudiante (Pagado vs Pendiente)
 */
function obtenerEstadoFinancieroEstudiante($idEstudianteACalcular) {
    $conexionBaseDatos = obtenerConexion();
    
    // Sumar todo lo que ha pagado
    $sqlPagado = "SELECT SUM(monto) as total_acumulado FROM pagos WHERE idEstudiante = $idEstudianteACalcular";
    $resultadoPagado = mysqli_query($conexionBaseDatos, $sqlPagado);
    $datosPagado = mysqli_fetch_assoc($resultadoPagado);
    
    $totalYaPagado = 0;
    if (!empty($datosPagado['total_acumulado'])) {
        $totalYaPagado = $datosPagado['total_acumulado'];
    }

    // Obtener el precio del ciclo que está cursando
    $sqlPrecio = "SELECT ciclos.precioCiclo 
                  FROM estudiantes 
                  JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                  WHERE estudiantes.idEstudiante = $idEstudianteACalcular";
                  
    $resultadoPrecio = mysqli_query($conexionBaseDatos, $sqlPrecio);
    $precioDelCiclo = 0;
    
    if ($resultadoPrecio) {
        $datosPrecio = mysqli_fetch_assoc($resultadoPrecio);
        if (isset($datosPrecio['precioCiclo']) && $datosPrecio['precioCiclo'] > 0) {
            $precioDelCiclo = $datosPrecio['precioCiclo'];
        }
    }

    mysqli_close($conexionBaseDatos);
    
    $dineroRestante = $precioDelCiclo - $totalYaPagado;
    
    $estadoFinal = array();
    $estadoFinal['totalPagado'] = $totalYaPagado;
    $estadoFinal['precioCiclo'] = $precioDelCiclo;
    $estadoFinal['restante'] = $dineroRestante;
    
    return $estadoFinal;
}

/**
 * Elimina un registro de pago por su ID
 */
function eliminarPago($idPagoABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM pagos WHERE idPago = $idPagoABorrar";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Obtiene los detalles de un pago específico
 */
function obtenerPagoPorId($idPagoBuscado) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT * FROM pagos WHERE idPago = $idPagoBuscado";
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $datosEncontrados = mysqli_fetch_assoc($resultadoConsulta);
    
    if (!empty($datosEncontrados)) {
        $datosEncontrados['conceptoPago'] = $datosEncontrados['tipoPago'];
        $datosEncontrados['cantidadPago'] = $datosEncontrados['monto'];
    }
    
    mysqli_close($conexionBaseDatos);
    return $datosEncontrados;
}
?>