<?php
require_once "conexion.php";

class aula {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarAulasModelo() {
        $sql = "SELECT * FROM aulas ORDER BY nombreAula ASC";
        $resultado = $this->conexion->query($sql);
        $aulas = [];
        
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $aulas[] = $fila;
            }
        }
        return $aulas;
    }

    public function insertarAulasModelo($datos) {
        $sql = "INSERT INTO aulas (nombreAula) VALUES (?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('s', $datos['nombreAula']);
        return $stmt->execute();
    }

    public function eliminarAulasModelo($id) {
        $sql = "DELETE FROM aulas WHERE idAula = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    // Métodos simples para rellenar desplegables (ComboBox)
    public function listarEstadosModelo() {
        $sql = "SELECT * FROM estados ORDER BY nombreEstado ASC";
        $resultado = $this->conexion->query($sql);
        $estados = [];
        while($fila = $resultado->fetch_assoc()) { $estados[] = $fila; }
        return $estados;
    }
}
?>