<?php
require_once("conectar.php");

class reclamacion {
    public function listarReclamacionesModelo() {
        $bd = getConnection();
        $sql = "SELECT r.*, 
                (SELECT nombreEstudiante FROM estudiantes WHERE idEstudiante = r.idEstudiante) as nombreEstudiante,
                (SELECT nombreProfesor FROM profesores WHERE idProfesor = r.idProfesor) as nombreProfesor 
                FROM reclamaciones r 
                ORDER BY r.idReclamacion DESC";
        $resultado = $bd->query($sql);
        $reclamaciones = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $reclamaciones[] = $fila;
            }
        }
        $bd->close();
        return $reclamaciones;
    }

    public function cambiarEstadoModelo($id, $nuevoEstado) {
        $bd = getConnection();
        $sql = "UPDATE reclamaciones SET estadoReclamacion = ? WHERE idReclamacion = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("si", $nuevoEstado, $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function insertarReclamacionModelo($idEstudiante, $idProfesor, $asunto, $descripcion, $gravedad, $fecha) {
        $bd = getConnection();
        $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, asunto, descripcion, gravedad, fecha) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("iissss", $idEstudiante, $idProfesor, $asunto, $descripcion, $gravedad, $fecha);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function eliminarReclamacionModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM reclamaciones WHERE idReclamacion = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }
}
?>
