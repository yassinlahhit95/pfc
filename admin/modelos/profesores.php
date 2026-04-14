<?php
/**
 * Professor Model
 */
require_once "conexion.php";

class profesor {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // List professors using subquery
    public function listarProfesoresModelo() {
        $sql = "SELECT p.*, 
                (SELECT nombreEstado FROM estados WHERE idEstado = p.idEstado) as nombreEstado
                FROM profesores p 
                ORDER BY p.idProfesor ASC";
        
        $resultado = $this->conexion->query($sql);
        $profesores = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $profesores[] = $fila;
            }
        }
        return $profesores;
    }

    // Insert using Prepared Statements
    public function insertarProfesoresModelo($datos) {
        $sql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, especialidad, direccionProfesor, idEstado) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssssssi", 
            $datos['nombreProfesor'], 
            $datos['emailProfesor'], 
            $datos['telefonoProfesor'], 
            $datos['dniProfesor'], 
            $datos['especialidad'], 
            $datos['direccionProfesor'], 
            $datos['idEstado']
        );
        return $stmt->execute();
    }

    // Update using Prepared Statements
    public function actualizarProfesoresModelo($datos) {
        $sql = "UPDATE profesores SET nombreProfesor = ?, emailProfesor = ?, telefonoProfesor = ?, dniProfesor = ?, especialidad = ?, direccionProfesor = ?, idEstado = ? WHERE idProfesor = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssssssii", 
            $datos['nombreProfesor'], 
            $datos['emailProfesor'], 
            $datos['telefonoProfesor'], 
            $datos['dniProfesor'], 
            $datos['especialidad'], 
            $datos['direccionProfesor'], 
            $datos['idEstado'], 
            $datos['idProfesor']
        );
        return $stmt->execute();
    }

    public function eliminarProfesoresModelo($id) {
        $sql = "DELETE FROM profesores WHERE idProfesor = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function obtenerProfesorPorIdModelo($id) {
        $sql = "SELECT * FROM profesores WHERE idProfesor = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
?>
