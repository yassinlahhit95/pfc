<?php

require_once "conexion.php";
class curso
{

	protected $conexion;

	public function __construct($conexion)
	{
		$this->conexion = $conexion;
	}

	public function listarCursosModelo()
	{
		$sql1 = "SELECT cursos.*, niveles.nombreNivel, profesores.nombreProfesor, aulas.nombreAula, estados.nombreEstado
                FROM cursos
                JOIN niveles ON cursos.idNivel = niveles.idNivel
                LEFT JOIN profesores ON cursos.idProfesor = profesores.idProfesor
                LEFT JOIN aulas ON cursos.idAula = aulas.idAula
                JOIN estados ON cursos.idEstado = estados.idEstado
				ORDER BY cursos.idCurso ASC
		
		";
		$resultado1 = $this->conexion->query($sql1);
		$cursos = [];
		while ($fila = $resultado1->fetch_assoc()) {
			$cursos[] = $fila;
		}
		return $cursos;
	}

	
	public function insertarCursosModelo($datos){
		$sql2 = "INSERT INTO cursos (nombreCurso,descripcionCurso,idNivel,idProfesor,idAula,idEstado)
				VALUES (?,?,?,?,?,?)	
		";
		$stmt = $this->conexion->prepare($sql2);
		$stmt->bind_param('ssiiii',
				$datos['nombreCurso'],
				$datos['descripcionCurso'],
				$datos['idNivel'],
				$datos['idProfesor'],
				$datos['idAula'],
				$datos['idEstado']
				);
		return $stmt->execute();
		
	}
	


	


}
