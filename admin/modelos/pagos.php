<?php
require_once "conexion.php";

class pago {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarTodosLosPagosModelo() {
        $sql = "SELECT p.*, e.nombreEstudiante 
                FROM pagos p 
                JOIN estudiantes e ON p.idEstudiante = e.idEstudiante 
                ORDER BY p.idPago DESC";
        $resultado = $this->conexion->query($sql);
        $pagos = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $pagos[] = $fila;
            }
        }
        return $pagos;
    }

    public function insertarPagoModelo($datos) {
        $sql = "INSERT INTO pagos (idEstudiante, concepto, monto, tipoPago, estadoPago, fechaPago) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("isdsss", 
            $datos['idEstudiante'], 
            $datos['concepto'], 
            $datos['monto'], 
            $datos['tipoPago'], 
            $datos['estadoPago'], 
            $datos['fechaPago']
        );
        return $stmt->execute();
    }

    public function obtenerPagoPorIdModelo($id) {
        $sql = "SELECT * FROM pagos WHERE idPago = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function eliminarPagoModelo($id) {
        $sql = "DELETE FROM pagos WHERE idPago = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>