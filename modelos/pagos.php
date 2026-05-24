<?php
require_once __DIR__ . "/conectar.php";

// todos los pagos con nombre del estudiante y ciclo
function listarTodosLosPagos() {
    $con = obtenerConexion();
    $sql1 = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo
            FROM pagos
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            ORDER BY idPago DESC";
    $resultado = mysqli_query($con, $sql1);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function listarPagosFiltrados($idCiclo) {
    $con = obtenerConexion();
    $sql1 = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo
            FROM pagos
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.idCiclo = ?
            ORDER BY idPago DESC";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idCiclo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function listarPagosPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql1 = "SELECT * FROM pagos WHERE idEstudiante = ? ORDER BY fechaPago DESC";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idEstudiante);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo) {
    $con = obtenerConexion();
    $sql1 = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago) VALUES (?, ?, ?, ?, ?)";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "idsss", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo);
    $ok = mysqli_stmt_execute($resultado);
    mysqli_close($con);
    return $ok;
}

function actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo) {
    $con = obtenerConexion();
    $sql1 = "UPDATE pagos SET idEstudiante=?, monto=?, tipoPago=?, fechaPago=?, fechaProximoPago=? WHERE idPago=?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "idsssi", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $idPago);
    $ok = mysqli_stmt_execute($resultado);
    mysqli_close($con);
    return $ok;
}

// calcula cuanto ha pagado y cuanto le queda por pagar
function obtenerEstadoFinancieroEstudiante($idEstudiante) {
    $con = obtenerConexion();

    $sql1 = "SELECT SUM(monto) AS totalPagado FROM pagos WHERE idEstudiante = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idEstudiante);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $fila = mysqli_fetch_assoc($res);
    $pagado = floatval($fila['totalPagado']);

    $sql2 = "SELECT c.precioCiclo FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE e.idEstudiante = ?";
    $resultado = mysqli_prepare($con, $sql2);
    mysqli_stmt_bind_param($resultado, "i", $idEstudiante);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $fila = mysqli_fetch_assoc($res);
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
    $sql1 = "DELETE FROM pagos WHERE idPago = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idPago);
    $ok = mysqli_stmt_execute($resultado);
    mysqli_close($con);
    return $ok;
}

function obtenerPagoPorId($idPago) {
    $con = obtenerConexion();
    $sql1 = "SELECT * FROM pagos WHERE idPago = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idPago);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $pago = mysqli_fetch_assoc($res);
    mysqli_close($con);
    return $pago;
}

function contarPagosEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql1 = "SELECT COUNT(*) as total FROM pagos WHERE idEstudiante = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idEstudiante);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($con);
    return intval($fila['total']);
}
