<?php
require_once("conectar.php");

class pago {
    public function listarTodosLosPagosModelo() {
        $bd = getConnection();
        $sql = "SELECT p.*, e.nombreEstudiante 
                FROM pagos p 
                JOIN estudiantes e ON p.idEstudiante = e.idEstudiante 
                ORDER BY p.idPago DESC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function insertarPagoModelo($idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $comprobante = null) {
        $bd = getConnection();
        $sql = "INSERT INTO pagos (idEstudiante, concepto, monto, tipoPago, estadoPago, fechaPago, comprobante) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("isdssss", $idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $comprobante);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function actualizarPagoModelo($idPago, $idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $rutaComprobante = null) {
        $bd = getConnection();
        if ($rutaComprobante) {
            $sql = "UPDATE pagos SET idEstudiante = ?, concepto = ?, monto = ?, tipoPago = ?, estadoPago = ?, fechaPago = ?, comprobante = ? WHERE idPago = ?";
            $stmt = $bd->prepare($sql);
            $stmt->bind_param("isdssssi", $idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $rutaComprobante, $idPago);
        } else {
            $sql = "UPDATE pagos SET idEstudiante = ?, concepto = ?, monto = ?, tipoPago = ?, estadoPago = ?, fechaPago = ? WHERE idPago = ?";
            $stmt = $bd->prepare($sql);
            $stmt->bind_param("isdsssi", $idEstudiante, $concepto, $monto, $tipoPago, $estadoPago, $fechaPago, $idPago);
        }
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function obtenerPagoPorIdModelo($id) {
        $bd = getConnection();
        $sql = "SELECT p.*, e.nombreEstudiante 
                FROM pagos p 
                JOIN estudiantes e ON p.idEstudiante = e.idEstudiante 
                WHERE p.idPago = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_assoc();
        $bd->close();
        return $datos;
    }

    public function eliminarPagoModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM pagos WHERE idPago = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }
}
?>
