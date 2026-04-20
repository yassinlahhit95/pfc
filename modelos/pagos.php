<?php
require_once("conectar.php");

function listarTodosLosPagos() {
    $conexion = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante 
            FROM pagos 
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante 
            ORDER BY idPago DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $lista[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $lista;
}

function listarPagos() {
    return listarTodosLosPagos();
}

function insertarPago($idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $comprobante = null) {
    $conexion = obtenerConexion();
    
    if ($comprobante) {
        $sql = "INSERT INTO pagos (idEstudiante, concepto, monto, tipoPago, estadoPago, fechaPago, comprobante) 
                VALUES ($idEstudiante, '$concepto', $monto, '$tipoPago', '$estadoPago', '$fechaPago', '$comprobante')";
    } else {
        $sql = "INSERT INTO pagos (idEstudiante, concepto, monto, tipoPago, estadoPago, fechaPago) 
                VALUES ($idEstudiante, '$concepto', $monto, '$tipoPago', '$estadoPago', '$fechaPago')";
    }
    
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarPago($idPago, $idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $rutaComprobante = null) {
    $conexion = obtenerConexion();
    
    if ($rutaComprobante) {
        $sql = "UPDATE pagos SET idEstudiante = $idEstudiante, concepto = '$concepto', monto = $monto, 
                tipoPago = '$tipoPago', estadoPago = '$estadoPago', fechaPago = '$fechaPago', comprobante = '$rutaComprobante' 
                WHERE idPago = $idPago";
    } else {
        $sql = "UPDATE pagos SET idEstudiante = $idEstudiante, concepto = '$concepto', monto = $monto, 
                tipoPago = '$tipoPago', estadoPago = '$estadoPago', fechaPago = '$fechaPago' 
                WHERE idPago = $idPago";
    }
    
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarPago($idPago) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM pagos WHERE idPago = $idPago";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerPagoPorId($idPago) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idPago = $idPago";
    $resultado = mysqli_query($conexion, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $datos;
}
?>