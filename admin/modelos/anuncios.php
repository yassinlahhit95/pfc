<?php
require_once("conectar.php");

class anuncio {
    public function listarAnunciosModelo() {
        $bd = getConnection();
        $sql = "SELECT * FROM anuncios ORDER BY idAnuncio DESC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function insertarAnuncioModelo($titulo, $mensaje, $fecha) {
        $bd = getConnection();
        $sql = "INSERT INTO anuncios (titulo, mensaje, fechaExpiracion) VALUES (?, ?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("sss", $titulo, $mensaje, $fecha);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function eliminarAnuncioModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM anuncios WHERE idAnuncio = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function obtenerAnuncioPorIdModelo($id) {
        $bd = getConnection();
        $sql = "SELECT * FROM anuncios WHERE idAnuncio = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_assoc();
        $bd->close();
        return $datos;
    }

    public function contarAnunciosActivos() {
        $bd = getConnection();
        $sql = "SELECT COUNT(*) as total FROM anuncios WHERE fechaExpiracion >= CURDATE()";
        $resultado = $bd->query($sql);
        $fila = $resultado->fetch_assoc();
        $bd->close();
        return $fila['total'] ?? 0;
    }

    public function listarAnunciosPaginados($limite, $inicio) {
        $bd = getConnection();
        $sql = "SELECT * FROM anuncios WHERE fechaExpiracion >= CURDATE() ORDER BY idAnuncio DESC LIMIT ? OFFSET ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ii", $limite, $inicio);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        $bd->close();
        return $datos;
    }
}
?>
