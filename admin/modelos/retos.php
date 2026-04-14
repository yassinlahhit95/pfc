<?php
require_once "conexion.php";

class reto {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarRetosModelo() {
        $sql = "SELECT * FROM retos ORDER BY idReto ASC";
        $resultado = $this->conexion->query($sql);
        $retos = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $retos[] = $fila;
            }
        }
        return $retos;
    }

    public function insertarRetoModelo($datos) {
        $sql = "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sssi", $datos['nombreReto'], $datos['fechaInicio'], $datos['fechaFin'], $datos['horasReto']);
        if ($stmt->execute()) {
            return $this->conexion->insert_id;
        }
        return false;
    }

    public function actualizarRetoModelo($datos) {
        $sql = "UPDATE retos SET nombreReto = ?, fechaInicio = ?, fechaFin = ?, horasReto = ? WHERE idReto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sssii", $datos['nombreReto'], $datos['fechaInicio'], $datos['fechaFin'], $datos['horasReto'], $datos['idReto']);
        return $stmt->execute();
    }

    public function eliminarRetoModelo($id) {
        $sql = "DELETE FROM retos WHERE idReto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function obtenerRetoPorIdModelo($id) {
        $sql = "SELECT * FROM retos WHERE idReto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    // Many-to-Many associations
    public function asociarModuloReto($idModulo, $idReto) {
        $sql = "INSERT IGNORE INTO modulo_reto (idModulo, idReto) VALUES (?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idModulo, $idReto);
        return $stmt->execute();
    }

    public function desvincularModuloReto($idModulo, $idReto) {
        $sql = "DELETE FROM modulo_reto WHERE idModulo = ? AND idReto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idModulo, $idReto);
        return $stmt->execute();
    }

    public function obtenerModulosDeReto($idReto) {
        $sql = "SELECT m.* FROM modulos m JOIN modulo_reto mr ON m.idModulo = mr.idModulo WHERE mr.idReto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $idReto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $modulos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $modulos[] = $fila;
        }
        return $modulos;
    }

    // Grading
    public function calificarRetoEstudiante($idEstudiante, $idReto, $nota) {
        // Check if exists
        $sql_check = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
        $stmt_check = $this->conexion->prepare($sql_check);
        $stmt_check->bind_param("ii", $idEstudiante, $idReto);
        $stmt_check->execute();
        $res = $stmt_check->get_result();
        
        if ($res->num_rows > 0) {
            $sql = "UPDATE calificaciones_retos SET nota = ? WHERE idEstudiante = ? AND idReto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("dii", $nota, $idEstudiante, $idReto);
        } else {
            $sql = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES (?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("iid", $idEstudiante, $idReto, $nota);
        }
        return $stmt->execute();
    }

    public function obtenerCalificacion($idEstudiante, $idReto) {
        $sql = "SELECT nota FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ii", $idEstudiante, $idReto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        return $fila ? $fila['nota'] : null;
    }
}
?>
