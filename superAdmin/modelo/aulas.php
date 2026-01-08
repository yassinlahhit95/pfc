<?php
require_once "conexion.php";

class aula{
	
protected $conexion;

public function __construct($conexion){
	$this->conexion = $conexion;
}	

public function listarAulasModelo(){
	$sql1 = "SELECT * FROM aulas";
	$resultado1 = $this->conexion->query("sql1");
	$aulas = [];
	while($fila = $resultado1->fetch_assoc()){
		$aulas[]= $fila;
	}
	return $aulas;

}

public function insertarAulasModelo($datos){
	$sql2 = "INSERT INTO aulas (nombreAula) VALUES (?)";
	$stmt = $this->conexion->prepare($sql2);
	$stmt->bind_param('s', $datos['nombreAula']);
	return $stmt->execute();
}















	
}
?>