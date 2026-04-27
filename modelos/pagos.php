<?php
require_once("conectar.php");

// Ver pagos
function listarTodosLosPagos() {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo FROM pagos JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo ORDER BY idPago DESC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Pagos por ciclo
function listarPagosFiltrados($idCic) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT pagos.*, estudiantes.nombreEstudiante, ciclos.nombreCiclo FROM pagos JOIN estudiantes ON pagos.idEstudiante = estudiantes.idEstudiante JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idCiclo = $idCic ORDER BY idPago DESC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Historial alumno
function obtenerPagosPorEstudiante($idEst) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM pagos WHERE idEstudiante = $idEst ORDER BY fechaPago DESC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Meter pago
function insertarPagoCompleto($idEst, $mon, $tipo, $fecP, $fecProx) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "INSERT INTO pagos (idEstudiante, monto, tipoPago, fechaPago, fechaProximoPago) VALUES ($idEst, $mon, '$tipo', '$fecP', '$fecProx')");
    mysqli_close($db);
    return $res;
}

// Actualizar pago
function actualizarPago($id, $idEst, $mon, $tipo, $fecP, $fecProx, $comp = "") {
    $db = obtenerConexion();
    $sql = "UPDATE pagos SET idEstudiante=$idEst, monto=$mon, tipoPago='$tipo', fechaPago='$fecP', fechaProximoPago='$fecProx'";
    if ($comp != "") { $sql = $sql . ", comprobante = '$comp'"; }
    $sql = $sql . " WHERE idPago = $id";
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}

// Mirar deuda
function obtenerEstadoFinancieroEstudiante($idEst) {
    $db = obtenerConexion();
    $resP = mysqli_query($db, "SELECT SUM(monto) as sum FROM pagos WHERE idEstudiante = $idEst");
    $fP = mysqli_fetch_assoc($resP);
    $pagado = isset($fP['sum']) ? $fP['sum'] : 0;

    $resC = mysqli_query($db, "SELECT precioCiclo FROM estudiantes JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE estudiantes.idEstudiante = $idEst");
    $precio = 0;
    if ($resC) {
        $fC = mysqli_fetch_assoc($resC);
        if ($fC && isset($fC['precioCiclo'])) { $precio = $fC['precioCiclo']; }
    }
    mysqli_close($db);
    return ['totalPagado' => $pagado, 'precioCiclo' => $precio, 'restante' => ($precio - $pagado)];
}

// Borrar
function eliminarPago($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM pagos WHERE idPago = $id");
    mysqli_close($db);
    return $res;
}

// Coger por ID
function obtenerPagoPorId($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM pagos WHERE idPago = $id");
    $fila = mysqli_fetch_assoc($res);
    if (isset($fila)) {
        $fila['conceptoPago'] = $fila['tipoPago'];
        $fila['cantidadPago'] = $fila['monto'];
    }
    mysqli_close($db);
    return $fila;
}
// Contar cuántos pagos ha hecho un estudiante
function contarPagosEstudiante($idEst) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT COUNT(*) as total FROM pagos WHERE idEstudiante = $idEst");
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['total']) ? $fila['total'] : 0;
}
?>