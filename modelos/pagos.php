<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarTodosLosPagos() {
    $con = obtenerConexion();
    $sql = "SELECT p.*, e.nombreEstudiante, e.curso, e.idCiclo, c.nombreCiclo, c.idNivel, n.nombreNivel
            FROM estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            JOIN niveles n ON c.idNivel = n.idNivel
            STRAIGHT_JOIN pagos p ON p.idEstudiante = e.idEstudiante
            WHERE e.deleted_at IS NULL
            ORDER BY p.idPago DESC";
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
    $sql = "SELECT p.*, e.nombreEstudiante, e.curso, e.idCiclo, c.nombreCiclo, c.idNivel, n.nombreNivel
            FROM estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            JOIN niveles n ON c.idNivel = n.idNivel
            STRAIGHT_JOIN pagos p ON p.idEstudiante = e.idEstudiante
            WHERE e.idCiclo = ? AND e.deleted_at IS NULL
            ORDER BY p.idPago DESC";
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

function insertarPagoCompleto($idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $comprobante = null) {
    $con = obtenerConexion();
    $sql = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago, comprobante) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "idssss", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $comprobante);
    return mysqli_stmt_execute($stmt);
}

function registrarCobroPago(int $idEstudiante, float $monto, string $tipoPago, string $fechaPago, ?string $fechaProximo, ?string $comprobante = null): bool {
    $con = obtenerConexion();
    $estadoComprobante = 'aprobado';
    $sql = "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago, comprobante, estadoComprobante) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "idsssss", $idEstudiante, $monto, $tipoPago, $fechaPago, $fechaProximo, $comprobante, $estadoComprobante);
    return mysqli_stmt_execute($stmt);
}

// Sube/reemplaza el comprobante de un pago existente (autoservicio estudiante/tutor
// para un pago vencido) — vuelve a 'verificando' y limpia un rechazo anterior,
// para que un segundo intento no quede colgado con el motivoRechazo viejo.
function subirComprobantePago(int $idPago, string $archivo): bool {
    $con = obtenerConexion();
    $sql = "UPDATE pagos SET comprobante = ?, estadoComprobante = 'verificando', motivoRechazoComprobante = NULL WHERE idPago = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $archivo, $idPago);
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

// Aprueba/rechaza un comprobante subido por el estudiante/tutor (secretaría/director).
function resolverComprobantePago(int $idPago, bool $aprobar, ?string $motivoRechazo = null): bool {
    $con = obtenerConexion();
    $estado = $aprobar ? 'aprobado' : 'rechazado';
    $sql = "UPDATE pagos SET estadoComprobante = ?, motivoRechazoComprobante = ? WHERE idPago = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $estado, $motivoRechazo, $idPago);
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


// Pagos recurrentes vencidos: solo el ÚLTIMO pago de cada estudiante cuenta
// (los recibos antiguos ya renovados no son deuda) y una prórroga vigente
// deja de considerarse vencido. Misma lógica que el dashboard de secretaría.
function listarPagosPendientes() {
    $con = obtenerConexion();
    $sql = "SELECT p.idPago, p.idEstudiante, p.monto, p.tipoPago, p.fechaPago,
                   p.fechaProximoPago, p.prorrogaHasta,
                   e.nombreEstudiante, e.curso, c.nombreCiclo
            FROM pagos p
            INNER JOIN (
                SELECT idEstudiante, MAX(idPago) AS max_id
                FROM pagos GROUP BY idEstudiante
            ) ultimo ON p.idPago = ultimo.max_id
            JOIN estudiantes e ON e.idEstudiante = p.idEstudiante AND e.deleted_at IS NULL
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE p.tipoPago IN ('mensual', 'trimestral', 'semestral')
              AND p.fechaProximoPago IS NOT NULL
              AND p.fechaProximoPago < CURDATE()
              AND (p.prorrogaHasta IS NULL OR p.prorrogaHasta < CURDATE())
            ORDER BY p.fechaProximoPago ASC";
    $res = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarEstudiantesConPagosPendientes() {
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, c.nombreCiclo, c.precioCiclo,
            IFNULL(SUM(p.monto), 0) AS totalPagado,
            (c.precioCiclo - IFNULL(SUM(p.monto), 0)) AS deuda
            FROM estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            LEFT JOIN pagos p ON e.idEstudiante = p.idEstudiante
            WHERE e.deleted_at IS NULL
            GROUP BY e.idEstudiante, e.nombreEstudiante, c.nombreCiclo, c.precioCiclo
            HAVING (c.precioCiclo - IFNULL(SUM(p.monto), 0)) > 0.05
            ORDER BY deuda DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

