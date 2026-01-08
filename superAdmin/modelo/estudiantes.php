<?php
require_once "conexion.php";

class estudiante {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarEstudiantesModelo() {
        $sql1 = "SELECT estudiantes.*, cursos.nombreCurso, estados.nombreEstado
                 FROM estudiantes
                 LEFT JOIN cursos ON estudiantes.idCurso = cursos.idCurso
                 LEFT JOIN estados ON estudiantes.idEstado = estados.idEstado
                 ORDER BY estudiantes.idEstudiante ASC";
        $resultado1 = $this->conexion->query($sql1);
        $estudiantes = [];
        while ($fila = $resultado1->fetch_assoc()) {
            $estudiantes[] = $fila;
        }
        return $estudiantes;
    }
    
    public function insertarEstudianteModelo($datos) {
        $sql2 = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, 
                                        fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, 
                                        direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, 
                                        nivelEstudiante, observacionesEstudiante, idCurso, idEstado) 
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
        
        $stmt = $this->conexion->prepare($sql2);
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
}
?>