<?php

require_once "conexion.php";

class nivel{
	protected $conexion;
	
	public function __construct($conexion){
		$this->conexion = $conexion;
	}
	
	public function listarNivelesModelo(){
		$sql1 = "SELECT * FROM niveles";
		$resultado1 = $this->conexion->query($sql1);
		$niveles = [];
		while($fila = $resultado1->fetch_assoc()){
			$niveles[] = $fila ;
		}
		return $niveles;
	}
	
	public function insertarNivelesModelo($datos){
		$sql2 = "INSERT INTO niveles (nombreNivel) VALUES (?)";
		$stmt = $this->conexion->prepare($sql2);
		$stmt->bind_param('s',$datos['nombreNivel']);
		return $stmt->execute();
	}
	
	
	
	
}
?>