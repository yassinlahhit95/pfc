<?php
require_once("conectar.php");

class nivel {
    private $idNivel;
    private $nombreNivel;

    public function __construct($idNivel = "", $nombreNivel = "") {
        $this->idNivel = $idNivel;
        $this->nombreNivel = $nombreNivel;
    }

    public function __set($propiedad, $valor) { $this->$propiedad = $valor; }
    public function __get($propiedad) {
        if (property_exists($this, $propiedad)) { return $this->$propiedad; }
    }

    public function listarNivelesModelo() {
        $bd = getConnection();
        $sql = "SELECT * FROM niveles ORDER BY nombreNivel ASC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }
}
?>
