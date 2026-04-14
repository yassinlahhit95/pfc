<?php
require_once "conexion.php";

class modulo {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarModulosModelo() {
        $sql = "SELECT m.*, (SELECT nombreCiclo FROM ciclos WHERE idCiclo = m.idCiclo) as nombreCiclo FROM modulos m ORDER BY m.idModulo ASC";
        $resultado = $this->conexion->query($sql);
        $modulos = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $modulos[] = $fila;
            }
        }
        return $modulos;
    }

    public function listarModulosPorCicloModelo($idCiclo) {
        $sql = "SELECT * FROM modulos WHERE idCiclo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idCiclo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $modulos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $modulos[] = $fila;
        }
        return $modulos;
    }

    public function insertarModuloModelo($datos) {
        $sql = "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sii", $datos['nombreModulo'], $datos['idCiclo'], $datos['horasMaximas']);
        return $stmt->execute();
    }

    public function actualizarModuloModelo($datos) {
        $sql = "UPDATE modulos SET nombreModulo = ?, idCiclo = ?, horasMaximas = ? WHERE idModulo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("siii", $datos['nombreModulo'], $datos['idCiclo'], $datos['horasMaximas'], $datos['idModulo']);
        return $stmt->execute();
    }

    public function eliminarModuloModelo($id) {
        $sql = "DELETE FROM modulos WHERE idModulo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function obtenerModuloPorIdModelo($id) {
        $sql = "SELECT * FROM modulos WHERE idModulo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    public function obtenerHorasTotalesRetosModulo($idModulo) {
        $sql = "SELECT SUM(r.horasReto) as total FROM retos r 
                JOIN modulo_reto mr ON r.idReto = mr.idReto 
                WHERE mr.idModulo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $idModulo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        return $fila['total'] ?? 0;
    }
}
?>
