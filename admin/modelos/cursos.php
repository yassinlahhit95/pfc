<?php
/**
 * Course Model
 */
require_once "conexion.php";

class curso {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // List courses using subqueries
    public function listarCursosModelo() {
        $sql = "SELECT c.*, 
                (SELECT nombreNivel FROM niveles WHERE idNivel = c.idNivel) as nombreNivel,
                (SELECT nombreProfesor FROM profesores WHERE idProfesor = c.idProfesor) as nombreProfesor,
                (SELECT nombreAula FROM aulas WHERE idAula = c.idAula) as nombreAula,
                (SELECT nombreEstado FROM estados WHERE idEstado = c.idEstado) as nombreEstado
                FROM cursos c 
                ORDER BY c.idCurso ASC";
        
        $resultado = $this->conexion->query($sql);
        $cursos = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $cursos[] = $fila;
            }
        }
        return $cursos;
    }

    // Insert using Prepared Statements
    public function insertarCursosModelo($datos) {
        $sql = "INSERT INTO cursos (nombreCurso, descripcionCurso, idNivel, idProfesor, idAula, idEstado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssiiii", $datos['nombreCurso'], $datos['descripcionCurso'], $datos['idNivel'], $datos['idProfesor'], $datos['idAula'], $datos['idEstado']);
        return $stmt->execute();
    }

    // Update using Prepared Statements
    public function actualizarCursosModelo($datos) {
        $sql = "UPDATE cursos SET nombreCurso = ?, descripcionCurso = ?, idNivel = ?, idProfesor = ?, idAula = ?, idEstado = ? WHERE idCurso = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssiiiii", $datos['nombreCurso'], $datos['descripcionCurso'], $datos['idNivel'], $datos['idProfesor'], $datos['idAula'], $datos['idEstado'], $datos['idCurso']);
        return $stmt->execute();
    }

    public function eliminarCursosModelo($id) {
        $sql = "DELETE FROM cursos WHERE idCurso = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function obtenerCursoPorIdModelo($id) {
        $sql = "SELECT * FROM cursos WHERE idCurso = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
?>
