<?php
require_once("conectar.php");

class ciclo {
    public function listarCiclosModelo() {
        $bd = getConnection();
        $sql = "SELECT c.*, 
                (SELECT nombreNivel FROM niveles WHERE idNivel = c.idNivel) as nombreNivel
                FROM ciclos c ORDER BY c.idCiclo ASC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $fila['profesores'] = $this->obtenerProfesoresDeCiclo($fila['idCiclo']);
                $fila['aulas'] = $this->obtenerAulasDeCiclo($fila['idCiclo']);
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function obtenerProfesoresDeCiclo($idCiclo) {
        $bd = getConnection();
        $sql = "SELECT p.* FROM profesores p JOIN ciclo_profesor cp ON p.idProfesor = cp.idProfesor WHERE cp.idCiclo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $idCiclo);
        $stmt->execute();
        $res = $stmt->get_result();
        $profesores = [];
        while($f = $res->fetch_assoc()) { $profesores[] = $f; }
        $bd->close();
        return $profesores;
    }

    public function obtenerAulasDeCiclo($idCiclo) {
        $bd = getConnection();
        $sql = "SELECT a.* FROM aulas a JOIN ciclo_aula ca ON a.idAula = ca.idAula WHERE ca.idCiclo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $idCiclo);
        $stmt->execute();
        $res = $stmt->get_result();
        $aulas = [];
        while($f = $res->fetch_assoc()) { $aulas[] = $f; }
        $bd->close();
        return $aulas;
    }

    public function insertarCicloModelo($nombre, $descripcion, $idNivel, $idEstado, $profesores = [], $aulas = []) {
        $bd = getConnection();
        $sql = "INSERT INTO ciclos (nombreCiclo, descripcionCiclo, idNivel, idEstado) VALUES (?, ?, ?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ssii", $nombre, $descripcion, $idNivel, $idEstado);
        
        if ($stmt->execute()) {
            $idCiclo = $bd->insert_id;
            
            foreach ($profesores as $idP) {
                $sqlP = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)";
                $stmtP = $bd->prepare($sqlP);
                $stmtP->bind_param("ii", $idCiclo, $idP);
                $stmtP->execute();
            }

            foreach ($aulas as $idA) {
                $sqlA = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES (?, ?)";
                $stmtA = $bd->prepare($sqlA);
                $stmtA->bind_param("ii", $idCiclo, $idA);
                $stmtA->execute();
            }

            $bd->close();
            return true;
        }
        $bd->close();
        return false;
    }

    public function actualizarCicloModelo($id, $nombre, $descripcion, $idNivel, $idEstado, $profesores = [], $aulas = []) {
        $bd = getConnection();
        $sql = "UPDATE ciclos SET nombreCiclo = ?, descripcionCiclo = ?, idNivel = ?, idEstado = ? WHERE idCiclo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ssiii", $nombre, $descripcion, $idNivel, $idEstado, $id);
        
        if ($stmt->execute()) {
            $bd->query("DELETE FROM ciclo_profesor WHERE idCiclo = $id");
            foreach ($profesores as $idP) {
                $sqlP = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)";
                $stmtP = $bd->prepare($sqlP);
                $stmtP->bind_param("ii", $id, $idP);
                $stmtP->execute();
            }

            $bd->query("DELETE FROM ciclo_aula WHERE idCiclo = $id");
            foreach ($aulas as $idA) {
                $sqlA = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES (?, ?)";
                $stmtA = $bd->prepare($sqlA);
                $stmtA->bind_param("ii", $id, $idA);
                $stmtA->execute();
            }

            $bd->close();
            return true;
        }
        $bd->close();
        return false;
    }

    public function eliminarCicloModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM ciclos WHERE idCiclo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function obtenerCicloPorIdModelo($id) {
        $bd = getConnection();
        $sql = "SELECT * FROM ciclos WHERE idCiclo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $result = $resultado->fetch_assoc();
        $bd->close();
        return $result;
    }
}
?>
