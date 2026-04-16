<?php
require_once("conectar.php");

class director {
    public function listarDirectoresModelo() {
        $bd = getConnection();
        $sql = "SELECT d.*, e.nombreEstado 
                FROM directores d 
                LEFT JOIN estados e ON d.idEstado = e.idEstado 
                ORDER BY d.idDirector ASC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function insertarDirectoresModelo($nombre, $email, $ciudad, $cp, $direccion, $telefono, $dni, $fechaAlta, $idEstado = 1) {
        $bd = getConnection();
        $sql = "INSERT INTO directores (nombreDirector, emailDirector, ciudadDirector, codigoPostalDirector, direccionDirector, telefonoDirector, dniDirector, fechaAltaDirector, idEstado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("sssissssi", $nombre, $email, $ciudad, $cp, $direccion, $telefono, $dni, $fechaAlta, $idEstado);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function actualizarDirectoresModelo($id, $nombre, $email, $ciudad, $cp, $direccion, $telefono, $dni, $fechaAlta, $idEstado) {
        $bd = getConnection();
        $sql = "UPDATE directores SET nombreDirector = ?, emailDirector = ?, ciudadDirector = ?, codigoPostalDirector = ?, direccionDirector = ?, telefonoDirector = ?, dniDirector = ?, fechaAltaDirector = ?, idEstado = ? WHERE idDirector = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("sssissssii", $nombre, $email, $ciudad, $cp, $direccion, $telefono, $dni, $fechaAlta, $idEstado, $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function eliminarDirectoresModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM directores WHERE idDirector = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function obtenerDirectorPorIdModelo($id) {
        $bd = getConnection();
        $sql = "SELECT d.*, e.nombreEstado 
                FROM directores d 
                LEFT JOIN estados e ON d.idEstado = e.idEstado 
                WHERE d.idDirector = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado->fetch_assoc();
        $bd->close();
        return $datos;
    }
}
?>
