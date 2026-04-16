<?php
require_once("conectar.php");

class modulo {
    public function listarModulosModelo() {
        $bd = getConnection();
        $sql = "SELECT m.*, (SELECT nombreCiclo FROM ciclos WHERE idCiclo = m.idCiclo) as nombreCiclo FROM modulos m ORDER BY m.idModulo ASC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function listarModulosPorCicloModelo($idCiclo) {
        $bd = getConnection();
        $sql = "SELECT * FROM modulos WHERE idCiclo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $idCiclo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $modulos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $modulos[] = $fila;
        }
        $bd->close();
        return $modulos;
    }

    public function insertarModuloModelo($nombre, $idCiclo, $horas) {
        $bd = getConnection();
        $sql = "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) VALUES (?, ?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("sii", $nombre, $idCiclo, $horas);
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function actualizarModuloModelo($id, $nombre, $idCiclo, $horas) {
        $bd = getConnection();
        $sql = "UPDATE modulos SET nombreModulo = ?, idCiclo = ?, horasMaximas = ? WHERE idModulo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("siii", $nombre, $idCiclo, $horas, $id);
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function eliminarModuloModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM modulos WHERE idModulo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param('i', $id);
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function obtenerModuloPorIdModelo($id) {
        $bd = getConnection();
        $sql = "SELECT * FROM modulos WHERE idModulo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $result = $resultado->fetch_assoc();
        $bd->close();
        return $result;
    }

    public function obtenerHorasTotalesRetosModulo($idModulo) {
        $bd = getConnection();
        $sql = "SELECT SUM(r.horasReto) as total FROM retos r 
                JOIN modulo_reto mr ON r.idReto = mr.idReto 
                WHERE mr.idModulo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param('i', $idModulo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado->fetch_assoc();
        $bd->close();
        return $fila['total'] ?? 0;
    }
}
?>
