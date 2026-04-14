<?php
require_once "conexion.php";

class ciclo {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarCiclosModelo() {
        $sql = "SELECT * FROM ciclos ORDER BY idCiclo ASC";
        $resultado = $this->conexion->query($sql);
        $ciclos = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $ciclos[] = $fila;
            }
        }
        return $ciclos;
    }

    public function insertarCicloModelo($datos) {
        $sql = "INSERT INTO ciclos (nombreCiclo, descripcionCiclo) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ss", $datos['nombreCiclo'], $datos['descripcionCiclo']);
        return $stmt->execute();
    }

    public function actualizarCicloModelo($datos) {
        $sql = "UPDATE ciclos SET nombreCiclo = ?, descripcionCiclo = ? WHERE idCiclo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssi", $datos['nombreCiclo'], $datos['descripcionCiclo'], $datos['idCiclo']);
        return $stmt->execute();
    }

    public function eliminarCicloModelo($id) {
        $sql = "DELETE FROM ciclos WHERE idCiclo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function obtenerCicloPorIdModelo($id) {
        $sql = "SELECT * FROM ciclos WHERE idCiclo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
?>
