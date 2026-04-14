<?php
require_once "conexion.php";

class estudiante {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarEstudiantesModelo() {
        // Using subqueries instead of JOIN
        $sql = "SELECT e.*, 
                (SELECT nombreCurso FROM cursos WHERE idCurso = e.idCurso) as nombreCurso,
                (SELECT nombreEstado FROM estados WHERE idEstado = e.idEstado) as nombreEstado
                FROM estudiantes e 
                ORDER BY e.idEstudiante ASC";
        
        $resultado = $this->conexion->query($sql);
        $estudiantes = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $estudiantes[] = $fila;
            }
        }
        return $estudiantes;
    }

    public function insertarEstudianteModelo($datos) {
        $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante,
                                       fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante,
                                       direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante,
                                       nivelEstudiante, observacionesEstudiante, idCurso, idEstado)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssssssssissii",
            $datos['nombreEstudiante'],
            $datos['emailEstudiante'],
            $datos['telefonoEstudiante'],
            $datos['fechaNacimientoEstudiante'],
            $datos['dniEstudiante'],
            $datos['fechaAltaEstudiante'],
            $datos['direccionEstudiante'],
            $datos['ciudadEstudiante'],
            $datos['codigoPostalEstudiante'],
            $datos['nivelEstudiante'],
            $datos['observacionesEstudiante'],
            $datos['idCurso'],
            $datos['idEstado']
        );
        return $stmt->execute();
    }

    public function actualizarEstudianteModelo($datos) {
        $sql = "UPDATE estudiantes SET nombreEstudiante = ?, emailEstudiante = ?, telefonoEstudiante = ?,
                                      fechaNacimientoEstudiante = ?, dniEstudiante = ?, fechaAltaEstudiante = ?,
                                      direccionEstudiante = ?, ciudadEstudiante = ?, codigoPostalEstudiante = ?,
                                      nivelEstudiante = ?, observacionesEstudiante = ?, idCurso = ?, idEstado = ?
                WHERE idEstudiante = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssssssssissiii",
            $datos['nombreEstudiante'],
            $datos['emailEstudiante'],
            $datos['telefonoEstudiante'],
            $datos['fechaNacimientoEstudiante'],
            $datos['dniEstudiante'],
            $datos['fechaAltaEstudiante'],
            $datos['direccionEstudiante'],
            $datos['ciudadEstudiante'],
            $datos['codigoPostalEstudiante'],
            $datos['nivelEstudiante'],
            $datos['observacionesEstudiante'],
            $datos['idCurso'],
            $datos['idEstado'],
            $datos['idEstudiante']
        );
        return $stmt->execute();
    }

    public function eliminarEstudianteModelo($id) {
        $sql = "DELETE FROM estudiantes WHERE idEstudiante = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function obtenerEstudiantePorIdModelo($id) {
        $sql = "SELECT * FROM estudiantes WHERE idEstudiante = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
?>
