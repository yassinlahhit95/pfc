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
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function listarPagosFiltrados($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo
            FROM pagos
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.idCiclo = ?
            ORDER BY idPago DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function listarPagosPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idEstudiante = ? ORDER BY fechaPago DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo) {
    $con = obtenerConexion();
    $sql = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "idsss", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo) {
    $con = obtenerConexion();
    $sql = "UPDATE pagos SET idEstudiante=?, monto=?, tipoPago=?, fechaPago=?, fechaProximoPago=? WHERE idPago=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "idsssi", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $idPago);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function actualizarComprobantePago($idPago, $comprobante) {
    $con = obtenerConexion();
    $sql = "UPDATE pagos SET comprobante=? WHERE idPago=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $comprobante, $idPago);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function obtenerEstadoFinancieroEstudiante($idEstudiante) {
    $con = obtenerConexion();

    $sql = "SELECT SUM(monto) AS totalPagado FROM pagos WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $pagado = floatval($fila['totalPagado']);

    $sql = "SELECT c.precioCiclo FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE e.idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $precio = floatval($fila['precioCiclo']);

    mysqli_close($con);
    return [
        'totalPagado' => $pagado,
        'precioCiclo' => $precio,
        'restante' => $precio - $pagado
    ];
}

function eliminarPago($idPago) {
    $con = obtenerConexion();
    $sql = "DELETE FROM pagos WHERE idPago = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPago);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function obtenerPagoPorId($idPago) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idPago = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPago);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $pago = mysqli_fetch_assoc($resultado);

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
    return intval($fila['total']);
}
