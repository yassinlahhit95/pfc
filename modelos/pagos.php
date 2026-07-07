<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarTodosLosPagos() {
    $con = obtenerConexion();
    $sql = "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo
            FROM pagos
            JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            ORDER BY idPago DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
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
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarPagosPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idEstudiante = ? ORDER BY fechaPago DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerEstadoFinancieroEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT SUM(monto) AS totalPagado FROM pagos WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rowPago = mysqli_fetch_assoc($res);
    $pagado = floatval($rowPago['totalPagado'] ?? 0);
    $sql = "SELECT c.precioCiclo FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE e.idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rowCiclo = mysqli_fetch_assoc($res);
    $precio = floatval($rowCiclo['precioCiclo'] ?? 0);
    return [
        'totalPagado' => $pagado,
        'precioCiclo' => $precio,
        'restante'    => $precio - $pagado
    ];
}

function obtenerPagoPorId($idPago) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM pagos WHERE idPago = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPago);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}

function contarPagosEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) AS total FROM pagos WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return intval(mysqli_fetch_assoc($res)['total']);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo) {
    require_once __DIR__ . '/configuracion.php';
    $config = obtenerConfiguracion();
    $cursoEscolar = $config['cursoEscolar'] ?? (date('Y') . '-' . (date('Y') + 1));

    $con = obtenerConexion();
    $sql = "INSERT INTO pagos (idEstudiante, cursoEscolar, monto, tipoPago, fechaPago, fechaProximoPago) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isdsss", $idEstudiante, $cursoEscolar, $monto, $tipoPago, $fechaPago, $fechaProximo);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarPago($idPago, $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo) {
    $con = obtenerConexion();
    $sql = "UPDATE pagos SET idEstudiante=?, monto=?, tipoPago=?, fechaPago=?, fechaProximoPago=? WHERE idPago=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "idsssi", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $idPago);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarPago($idPago) {
    $con = obtenerConexion();
    $sql = "DELETE FROM pagos WHERE idPago = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPago);
    return mysqli_stmt_execute($stmt);
}
