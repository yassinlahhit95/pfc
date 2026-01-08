<?php
require_once "conexion.php";
class asignatura {
    
    protected $conexion;
    
    public function __construct($conexion){
        $this->conexion = $conexion;
    }
    public function listarAsignaturasModelo(){
        
        $sql1 = "SELECT * FROM asignaturas";
        $resultado1 = $this->conexion->query($sql1);
        $asignaturas = [];
        while ($fila = $resultado1->fetch_assoc()){
            $asignaturas[]= $fila;
        }
        return $asignaturas;
    }

	public function insertarAsignaturasModelo($datos){
		$sql2 = "INSERT INTO asignaturas (nombreAsignatura) VALUES (?)";
		$stmt = $this->conexion->prepare($sql2);
		$stmt->bind_param('s',$datos['nombreAsignatura']);
		return $stmt->execute();
	}



}
?>