<?php
require_once("conectar.php");

class aula {
    public function listarAulasModelo() {
        $bd = getConnection();
        $sql = "SELECT * FROM aulas ORDER BY nombreAula ASC";
        $resultado = $bd->query($sql);
        $aulas = [];
        
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $aulas[] = $fila;
            }
        }
        $bd->close();
        return $aulas;
    }

    public function insertarAulasModelo($nombre) {
        $bd = getConnection();
        
        $sql = "INSERT INTO aulas (nombreAula) VALUES (?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param('s', $nombre);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function eliminarAulasModelo($id) {
        $bd = getConnection();
        
        $sql = "DELETE FROM aulas WHERE idAula = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param('i', $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function listarEstadosModelo() {
        $bd = getConnection();
        $sql = "SELECT * FROM estados ORDER BY nombreEstado ASC";
        $resultado = $bd->query($sql);
        $estados = [];
        while($fila = $resultado->fetch_assoc()) { $estados[] = $fila; }
        $bd->close();
        return $estados;
    }
}
?>
