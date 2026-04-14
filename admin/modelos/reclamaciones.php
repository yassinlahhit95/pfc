<?php
require_once "conexion.php";

class reclamacion {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Listar todas las reclamaciones con nombres de alumno y profesor
    public function listarReclamacionesModelo() {
        $sql = "SELECT r.*, 
                (SELECT nombreEstudiante FROM estudiantes WHERE idEstudiante = r.idEstudiante) as nombreEstudiante,
                (SELECT nombreProfesor FROM profesores WHERE idProfesor = r.idProfesor) as nombreProfesor 
                FROM reclamaciones r 
                ORDER BY r.idReclamacion DESC";
        $resultado = $this->conexion->query($sql);
        $reclamaciones = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $reclamaciones[] = $fila;
            }
        }
        return $reclamaciones;
    }

    public function cambiarEstadoModelo($id, $nuevoEstado) {
        $sql = "UPDATE reclamaciones SET estadoReclamacion = ? WHERE idReclamacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $nuevoEstado, $id);
        return $stmt->execute();
    }

    public function insertarReclamacionModelo($datos) {
        $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, asunto, descripcion, gravedad, fecha) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("iissss", 
            $datos['idEstudiante'], 
            $datos['idProfesor'], 
            $datos['asunto'], 
            $datos['descripcion'], 
            $datos['gravedad'], 
            $datos['fecha']
        );
        return $stmt->execute();
    }

    public function eliminarReclamacionModelo($id) {
        $sql = "DELETE FROM reclamaciones WHERE idReclamacion = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>