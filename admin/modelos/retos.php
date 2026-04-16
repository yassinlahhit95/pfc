<?php
require_once("conectar.php");

class reto {
    public function listarRetosModelo() {
        $bd = getConnection();
        $sql = "SELECT * FROM retos ORDER BY idReto ASC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function insertarRetoModelo($nombre, $fechaInicio, $fechaFin, $horas) {
        $bd = getConnection();
        $sql = "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) VALUES (?, ?, ?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $fechaInicio, $fechaFin, $horas);
        
        if ($stmt->execute()) {
            $lastId = $bd->insert_id;
            $bd->close();
            return $lastId;
        }
        $bd->close();
        return false;
    }

    public function actualizarRetoModelo($id, $nombre, $fechaInicio, $fechaFin, $horas) {
        $bd = getConnection();
        $sql = "UPDATE retos SET nombreReto = ?, fechaInicio = ?, fechaFin = ?, horasReto = ? WHERE idReto = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("sssii", $nombre, $fechaInicio, $fechaFin, $horas, $id);
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function eliminarRetoModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM retos WHERE idReto = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function obtenerRetoPorIdModelo($id) {
        $bd = getConnection();
        $sql = "SELECT * FROM retos WHERE idReto = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $result = $resultado->fetch_assoc();
        $bd->close();
        return $result;
    }

    public function asociarModuloReto($idModulo, $idReto) {
        $bd = getConnection();
        $sql = "INSERT IGNORE INTO modulo_reto (idModulo, idReto) VALUES (?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ii", $idModulo, $idReto);
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function limpiarAsociacionesReto($idReto) {
        $bd = getConnection();
        $sql = "DELETE FROM modulo_reto WHERE idReto = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $idReto);
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function obtenerModulosDeReto($idReto) {
        $bd = getConnection();
        $sql = "SELECT m.* FROM modulos m JOIN modulo_reto mr ON m.idModulo = mr.idModulo WHERE mr.idReto = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $idReto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $modulos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $modulos[] = $fila;
        }
        $bd->close();
        return $modulos;
    }

    public function calificarRetoEstudiante($idEstudiante, $idReto, $nota) {
        $bd = getConnection();
        $sql_check = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
        $stmt_check = $bd->prepare($sql_check);
        $stmt_check->bind_param("ii", $idEstudiante, $idReto);
        $stmt_check->execute();
        $res = $stmt_check->get_result();
        
        if ($res->num_rows > 0) {
            $sql = "UPDATE calificaciones_retos SET nota = ? WHERE idEstudiante = ? AND idReto = ?";
            $stmt = $bd->prepare($sql);
            $stmt->bind_param("dii", $nota, $idEstudiante, $idReto);
        } else {
            $sql = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES (?, ?, ?)";
            $stmt = $bd->prepare($sql);
            $stmt->bind_param("iid", $idEstudiante, $idReto, $nota);
        }
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function obtenerCalificacion($idEstudiante, $idReto) {
        $bd = getConnection();
        $sql = "SELECT nota FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ii", $idEstudiante, $idReto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $bd->close();
        return $fila ? $fila['nota'] : null;
    }
}
?>
