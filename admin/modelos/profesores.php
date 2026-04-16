<?php
require_once("conectar.php");

class profesor {
    public function listarProfesoresModelo() {
        $bd = getConnection();
        $sql = "SELECT p.*, e.nombreEstado 
                FROM profesores p 
                LEFT JOIN estados e ON p.idEstado = e.idEstado 
                ORDER BY p.idProfesor ASC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function insertarProfesoresModelo($nombre, $email, $telefono, $dni, $especialidad, $direccion, $estado = 1) {
        $bd = getConnection();
        $sql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, especialidad, direccionProfesor, idEstado) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ssssssi", $nombre, $email, $telefono, $dni, $especialidad, $direccion, $estado);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function actualizarProfesoresModelo($id, $nombre, $email, $telefono, $dni, $especialidad, $direccion, $estado) {
        $bd = getConnection();
        $sql = "UPDATE profesores SET nombreProfesor = ?, emailProfesor = ?, telefonoProfesor = ?, dniProfesor = ?, especialidad = ?, direccionProfesor = ?, idEstado = ? WHERE idProfesor = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ssssssii", $nombre, $email, $telefono, $dni, $especialidad, $direccion, $estado, $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function eliminarProfesoresModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM profesores WHERE idProfesor = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function obtenerProfesorPorIdModelo($id) {
        $bd = getConnection();
        $sql = "SELECT p.*, e.nombreEstado 
                FROM profesores p 
                LEFT JOIN estados e ON p.idEstado = e.idEstado 
                WHERE p.idProfesor = ?";
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
