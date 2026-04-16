<?php
require_once("conectar.php");

class estudiante {
    public function listarEstudiantesModelo() {
        $bd = getConnection();
        $sql = "SELECT e.*, 
                (SELECT nombreCiclo FROM ciclos WHERE idCiclo = e.idCiclo) as nombreCiclo
                FROM estudiantes e ORDER BY e.idEstudiante ASC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function insertarEstudianteModelo($nombre, $email, $telefono, $fechaNac, $dni, $fechaAlta, $direccion, $ciudad, $cp, $obs, $idCiclo, $idEstado) {
        $bd = getConnection();
        $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo, idEstado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ssssssssssii", $nombre, $email, $telefono, $fechaNac, $dni, $fechaAlta, $direccion, $ciudad, $cp, $obs, $idCiclo, $idEstado);
        
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function actualizarEstudianteModelo($id, $nombre, $email, $telefono, $fechaNac, $dni, $fechaAlta, $direccion, $ciudad, $cp, $obs, $idCiclo, $idEstado) {
        $bd = getConnection();
        $sql = "UPDATE estudiantes SET nombreEstudiante = ?, emailEstudiante = ?, telefonoEstudiante = ?, fechaNacimientoEstudiante = ?, dniEstudiante = ?, fechaAltaEstudiante = ?, direccionEstudiante = ?, ciudadEstudiante = ?, codigoPostalEstudiante = ?, observacionesEstudiante = ?, idCiclo = ?, idEstado = ? WHERE idEstudiante = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ssssssssssiii", $nombre, $email, $telefono, $fechaNac, $dni, $fechaAlta, $direccion, $ciudad, $cp, $obs, $idCiclo, $idEstado, $id);
        
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function eliminarEstudianteModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM estudiantes WHERE idEstudiante = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $bd->close();
        return $result;
    }

    public function obtenerEstudiantePorIdModelo($id) {
        $bd = getConnection();
        $sql = "SELECT * FROM estudiantes WHERE idEstudiante = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $result = $resultado->fetch_assoc();
        $bd->close();
        return $result;
    }
}
?>
