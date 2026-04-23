<?php
require_once("conectar.php");

function listarTodosLosPagos() {
    $conexion = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo 
            FROM pagos 
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            ORDER BY idPago DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarPagosFiltrados($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo 
            FROM pagos 
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.idCiclo = $idCiclo
            ORDER BY idPago DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerPagosPorEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idEstudiante = $idEstudiante ORDER BY fechaPago DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago) 
            VALUES ($idEstudiante, $monto, '$tipoPago', '$fechaPago', '$fechaProximo')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function insertarPago($idEstudiante, $concepto, $cantidad, $fecha) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago) 
            VALUES ($idEstudiante, $cantidad, '$concepto', '$fecha', '$fecha')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $comprobante = "") {
    $conexion = obtenerConexion();
    $sql = "UPDATE pagos SET idEstudiante = $idEstudiante, monto = $monto, tipoPago = '$tipoPago', fechaPago = '$fechaPago', fechaProximoPago = '$fechaProximo' 
            WHERE idPago = $idPago";
    if (!empty($comprobante)) {
        $sql = "UPDATE pagos SET idEstudiante = $idEstudiante, monto = $monto, tipoPago = '$tipoPago', fechaPago = '$fechaPago', fechaProximoPago = '$fechaProximo', comprobante = '$comprobante' 
                WHERE idPago = $idPago";
    }
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerEstadoFinancieroEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    
    $sqlPagado = "SELECT SUM(monto) as total FROM pagos WHERE idEstudiante = $idEstudiante";
    $resPagado = mysqli_query($conexion, $sqlPagado);
    $filaPagado = mysqli_fetch_assoc($resPagado);
    $totalPagado = $filaPagado['total'] ? $filaPagado['total'] : 0;

    $sqlPrecio = "SELECT ciclos.precioCiclo FROM estudiantes JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                  WHERE estudiantes.idEstudiante = $idEstudiante";
    $resPrecio = mysqli_query($conexion, $sqlPrecio);
    $precioCiclo = 0;
    if ($resPrecio) {
        $filaPrecio = mysqli_fetch_assoc($resPrecio);
        $precioCiclo = (isset($filaPrecio['precioCiclo']) && $filaPrecio['precioCiclo'] > 0) ? $filaPrecio['precioCiclo'] : 0;
    }

    mysqli_close($conexion);
    
    return [
        'totalPagado' => $totalPagado,
        'precioCiclo' => $precioCiclo,
        'restante' => $precioCiclo - $totalPagado
    ];
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
    $fila = mysqli_fetch_assoc($resultado);
    if ($fila) {
        $fila['conceptoPago'] = $fila['tipoPago'];
        $fila['cantidadPago'] = $fila['monto'];
    }
    mysqli_close($conexion);
    return $fila;
}

function contarPagosEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM pagos WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}
?>