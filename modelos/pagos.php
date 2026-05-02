<?php
require_once __DIR__ . "/conectar.php";

// Ver todos los pagos registrados
function listarTodosLosPagos() {
    $con = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo 
            FROM pagos 
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            ORDER BY idPago DESC";
            
    $resultado = mysqli_query($con, $sql);
    $listaPagos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaPagos[] = $fila; 
    }
    mysqli_close($con);
    return $listaPagos;
}

// Listar pagos filtrados por ciclo
function listarPagosFiltrados($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo 
            FROM pagos 
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.idCiclo = $idCiclo 
            ORDER BY idPago DESC";
            
    $resultado = mysqli_query($con, $sql);
    $listaPagos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaPagos[] = $fila; 
    }
    mysqli_close($con);
    return $listaPagos;
}

// Obtener el historial de pagos de un alumno
function obtenerPagosPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idEstudiante = $idEstudiante ORDER BY fechaPago DESC";
    $resultado = mysqli_query($con, $sql);
    $listaPagos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaPagos[] = $fila; 
    }
    mysqli_close($con);
    return $listaPagos;
}

// Registrar un nuevo pago
function insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo) {
    $con = obtenerConexion();
    $sql = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago) 
            VALUES ($idEstudiante, $monto, '$tipoPago', '$fechaPago', '$fechaProximo')";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Actualizar un pago existente
function actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $comprobante = "") {
    $con = obtenerConexion();
    $sql = "UPDATE pagos 
            SET idEstudiante=$idEstudiante, monto=$monto, tipoPago='$tipoPago', 
                fechaPago='$fechaPago', fechaProximoPago='$fechaProximo'";
                
    if (!empty($comprobante)) { 
        $sql .= ", comprobante = '$comprobante'"; 
    }
    
    $sql .= " WHERE idPago = $idPago";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener el balance financiero (total, pagado, restante) de un estudiante
function obtenerEstadoFinancieroEstudiante($idEstudiante) {
    $con = obtenerConexion();
    
    // Suma de lo ya pagado
    $sql = "SELECT SUM(monto) as total FROM pagos WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    $filaPagos = mysqli_fetch_assoc($resultado);
    $pagado = (float)($filaPagos['total'] ?? 0);

    // Precio total del ciclo que cursa
    $sql = "SELECT precioCiclo FROM estudiantes 
                  JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
                  WHERE estudiantes.idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    $precio = 0;
    
    if ($resultado) {
        $filaPrecio = mysqli_fetch_assoc($resultado);
        $precio = (float)($filaPrecio['precioCiclo'] ?? 0);
    }
    
    mysqli_close($con);
    return [
        'totalPagado' => $pagado, 
        'precioCiclo' => $precio, 
        'restante' => ($precio - $pagado)
    ];
}

// Eliminar un registro de pago
function eliminarPago($idPago) {
    $con = obtenerConexion();
    $sql = "DELETE FROM pagos WHERE idPago = $idPago";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener un pago por su ID
function obtenerPagoPorId($idPago) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idPago = $idPago";
    $resultado = mysqli_query($con, $sql);
    $pago = mysqli_fetch_assoc($resultado);
    
    if ($pago) {
        // Mantenemos alias por compatibilidad
        $pago['conceptoPago'] = $pago['tipoPago'];
        $pago['cantidadPago'] = $pago['monto'];
    }
    
    mysqli_close($con);
    return $pago;
}

// Contar cuántos pagos ha realizado un estudiante
function contarPagosEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM pagos WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}
?>