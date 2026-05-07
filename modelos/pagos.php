<?php
require_once __DIR__ . "/conectar.php";

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

function listarPagosFiltrados($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo FROM pagos JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo = ? ORDER BY idPago DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaPagos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaPagos[] = $fila;
    }
    mysqli_close($con);
    return $listaPagos;
}

function obtenerPagosPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idEstudiante = ? ORDER BY fechaPago DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaPagos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaPagos[] = $fila;
    }
    mysqli_close($con);
    return $listaPagos;
}

function insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo) {
    $con = obtenerConexion();
    $sql = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "idsss", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $comprobante = "") {
    $con = obtenerConexion();
    if (!empty($comprobante)) {
        $sql = "UPDATE pagos SET idEstudiante=?, monto=?, tipoPago=?, fechaPago=?, fechaProximoPago=?, comprobante=? WHERE idPago=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "idsssssi", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $comprobante, $idPago);
    } else {
        $sql = "UPDATE pagos SET idEstudiante=?, monto=?, tipoPago=?, fechaPago=?, fechaProximoPago=? WHERE idPago=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "idsssi", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $idPago);
    }
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerEstadoFinancieroEstudiante($idEstudiante) {
    $con = obtenerConexion();

    $sql = "SELECT SUM(monto) as total FROM pagos WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $filaPagos = mysqli_fetch_assoc($resultado);
    $pagado = (float)($filaPagos['total'] ?? 0);

    $sql = "SELECT precioCiclo FROM estudiantes JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
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

function eliminarPago($idPago) {
    $con = obtenerConexion();
    $sql = "DELETE FROM pagos WHERE idPago = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPago);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerPagoPorId($idPago) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idPago = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPago);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $pago = mysqli_fetch_assoc($resultado);

    if ($pago) {
        // Mantenemos alias por compatibilidad
        $pago['conceptoPago'] = $pago['tipoPago'];
        $pago['cantidadPago'] = $pago['monto'];
    }

    mysqli_close($con);
    return $pago;
}

function contarPagosEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM pagos WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($fila['total'] ?? 0);
}
?>
