<?php
require_once "conexion.php";

class nivel {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarNivelesModelo() {
        $sql = "SELECT * FROM niveles ORDER BY nombreNivel ASC";
        $resultado = $this->conexion->query($sql);
        $niveles = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $niveles[] = $fila;
            }
        }
        return $niveles;
    }
}
?>