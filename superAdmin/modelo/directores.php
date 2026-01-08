<?php
require_once("conexion.php");

class director {
    protected $conexion;
    
    public function __construct($conexion){
        $this->conexion = $conexion;
    }
    
    public function listarDirectoresModelo(){
        $sql1= "SELECT directores.*, estados.nombreEstado
                FROM directores
                LEFT JOIN estados ON directores.idEstado = estados.idEstado
                ORDER BY directores.idDirector ASC";
        $resultado1=$this->conexion->query($sql1);
        $directores = [];
        
        while($fila=$resultado1->fetch_assoc()){
            $directores[] = $fila;
        }
        return $directores; 
    }
    
    public function insertarDirectoresModelo($datos){
        $sql2 = "INSERT INTO directores (nombreDirector, emailDirector, ciudadDirector, codigoPostalDirector,
                                         direccionDirector, telefonoDirector, dniDirector, fechaAltaDirector, idEstado)
                 VALUES(?,?,?,?,?,?,?,?,?)";
        $stmt = $this->conexion->prepare($sql2);
        $stmt->bind_param("sssisissi",
            $datos['nombreDirector'],
            $datos['emailDirector'],
            $datos['ciudadDirector'],
            $datos['codigoPostalDirector'],
            $datos['direccionDirector'],
            $datos['telefonoDirector'],
            $datos['dniDirector'],
            $datos['fechaAltaDirector'],
            $datos['idEstado']
        );
        return $stmt->execute();
    }
}
?>