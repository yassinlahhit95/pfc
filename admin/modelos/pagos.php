<?php
require_once("conectar.php");

function listarPagos() {
    $conexion = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante 
            FROM pagos 
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante 
            ORDER BY pagos.idPago DESC";
    $datos = [];
    if ($resultado = mysqli_query($conexion, $sql)) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $datos[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $datos;
}

function insertarPago($idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $comprobante = null) {
    $conexion = obtenerConexion();
    $idEstudiante = mysqli_real_escape_string($conexion, $idEstudiante);
    $concepto = mysqli_real_escape_string($conexion, $concepto);
    $monto = mysqli_real_escape_string($conexion, $monto);
    $tipoPago = mysqli_real_escape_string($conexion, $tipoPago);
    $estadoPago = mysqli_real_escape_string($conexion, $estadoPago);
    $fechaPago = mysqli_real_escape_string($conexion, $fechaPago);
    $comprobante = $comprobante ? "'" . mysqli_real_escape_string($conexion, $comprobante) . "'" : "NULL";
    
    $sql = "INSERT INTO pagos (idEstudiante, concepto, monto, tipoPago, estadoPago, fechaPago, comprobante) 
            VALUES ($idEstudiante, '$concepto', $monto, '$tipoPago', '$estadoPago', '$fechaPago', $comprobante)";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarPago($idPago, $idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $rutaComprobante = null) {
    $conexion = obtenerConexion();
    $idPago = mysqli_real_escape_string($conexion, $idPago);
    $idEstudiante = mysqli_real_escape_string($conexion, $idEstudiante);
    $concepto = mysqli_real_escape_string($conexion, $concepto);
    $monto = mysqli_real_escape_string($conexion, $monto);
    $tipoPago = mysqli_real_escape_string($conexion, $tipoPago);
    $estadoPago = mysqli_real_escape_string($conexion, $estadoPago);
    $fechaPago = mysqli_real_escape_string($conexion, $fechaPago);

    if ($rutaComprobante) {
        $rutaComprobante = mysqli_real_escape_string($conexion, $rutaComprobante);
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

function obtenerPagoPorId($idPago) {
    $conexion = obtenerConexion();
    $idPago = mysqli_real_escape_string($conexion, $idPago);
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante 
            FROM pagos 
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante 
            WHERE pagos.idPago = $idPago";
    $resultado = mysqli_query($conexion, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $datos;
}

function borrarPago($idPago) {
    $conexion = obtenerConexion();
    $idPago = mysqli_real_escape_string($conexion, $idPago);
    $sql = "DELETE FROM pagos WHERE idPago = $idPago";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>
