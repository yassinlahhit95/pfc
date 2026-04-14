<?php
require_once "conexion.php";

class anuncio {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Listar todos los anuncios (Usando idAnuncio y fechaExpiracion)
    public function listarAnunciosModelo() {
        $sql = "SELECT * FROM anuncios ORDER BY idAnuncio DESC";
        $resultado = $this->conexion->query($sql);
        $anuncios = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $anuncios[] = $fila;
            }
        }
        return $anuncios;
    }

    // Listar solo los activos para el Dashboard
    public function listarAnunciosActivos() {
        $sql = "SELECT * FROM anuncios WHERE fechaExpiracion >= CURDATE() ORDER BY idAnuncio DESC";
        $resultado = $this->conexion->query($sql);
        $anuncios = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $anuncios[] = $fila;
            }
        }
        return $anuncios;
    }

    public function insertarAnuncio($titulo, $mensaje, $fecha) {
        $sql = "INSERT INTO anuncios (titulo, mensaje, fechaExpiracion) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sss", $titulo, $mensaje, $fecha);
        return $stmt->execute();
    }

    public function eliminarAnuncio($id) {
        $sql = "DELETE FROM anuncios WHERE idAnuncio = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function contarAnunciosActivos() {
        $sql = "SELECT COUNT(*) as total FROM anuncios WHERE fechaExpiracion >= CURDATE()";
        $res = $this->conexion->query($sql);
        $fila = $res->fetch_assoc();
        return $fila['total'];
    }

    public function listarAnunciosPaginados($limite, $inicio) {
        $sql = "SELECT * FROM anuncios WHERE fechaExpiracion >= CURDATE() ORDER BY idAnuncio DESC LIMIT $inicio, $limite";
        $resultado = $this->conexion->query($sql);
        $anuncios = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $anuncios[] = $fila;
            }
        }
        return $anuncios;
    }
}
?>