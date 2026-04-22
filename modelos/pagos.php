<?php
require_once("conectar.php");

function listarTodosLosPagos() {
    $conexion = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante FROM pagos JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante ORDER BY idPago DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarPago($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $comprobante = "") {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago, comprobante) VALUES ($idEstudiante, $monto, '$tipoPago', '$fechaPago', '$fechaProximo', '$comprobante')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $comprobante = "") {
    $conexion = obtenerConexion();
    $sql = "UPDATE pagos SET idEstudiante = $idEstudiante, monto = $monto, tipoPago = '$tipoPago', fechaPago = '$fechaPago', fechaProximoPago = '$fechaProximo' WHERE idPago = $idPago";
    if (!empty($comprobante)) {
        $sql = "UPDATE pagos SET idEstudiante = $idEstudiante, monto = $monto, tipoPago = '$tipoPago', fechaPago = '$fechaPago', fechaProximoPago = '$fechaProximo', comprobante = '$comprobante' WHERE idPago = $idPago";
    }
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarPago($idPago) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM pagos WHERE idPago = $idPago");
    mysqli_close($conexion);
    return $resultado;
}

function obtenerPagoPorId($idPago) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM pagos WHERE idPago = $idPago");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}
?>