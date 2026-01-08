<?php
require_once "conexion.php";

class profesor {
    protected $conexion;
    
    public function __construct($conexion){
        $this->conexion = $conexion;
    }

    public function listarProfesoresModelo(){
        $sql1 = "SELECT profesores.*, estados.nombreEstado 
                 FROM profesores 
                 LEFT JOIN estados ON profesores.idEstado = estados.idEstado 
                 ORDER BY profesores.idProfesor ASC";
        $resultado1 = $this->conexion->query($sql1);
        $profesores = [];
        while ($fila = $resultado1->fetch_assoc()){
            $profesores[]= $fila;
        }
        return $profesores;
    }

    public function insertarProfesoresModelo($datos){
		$sql2 = "INSERT INTO profesores (nombreProfesor,fechaAltaProfesor,correoProfesor,telefonoProfesor,
										 dniProfesor,direccionProfesor,ciudadProfesor,codigoPostalProfesor,idEstado)
				VALUES (?,?,?,?,?,?,?,?,?,?)";
		$stmt= $this->conexion->prepare($sql2);
		$stmt->bind_param('sssssssii',
						$datos['nombreProfesor'],
						$datos['fechaAltaProfesor'],
						$datos['correoProfesor'],
						$datos['telefonoProfesor'],
						$datos['dniProfesor'],
						$datos['direccionProfesor'],
						$datos['ciudadProfesor'],
						$datos['codigoPostalProfesor'],
						$datos['idEstado']
						);
		return $stmt->execute();
		
	}


    public function asignarProfesorModelo($idProfesor, $idAsignatura, $idCurso, $idAula){
        $sql3 = "INSERT INTO profesor_asignatura_curso(idProfesor, idAsignatura, idCurso, idAula)
                 VALUES (?,?,?,?)";
        $stmt = $this->conexion->prepare($sql3);
        $stmt->bind_param("iiii", $idProfesor, $idAsignatura, $idCurso, $idAula);
        return $stmt->execute();
    }
}
?>