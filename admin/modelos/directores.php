<?php
/**
 * Director Model
 */
require_once "conexion.php";

class director {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // List all directors using subquery for state
    public function listarDirectoresModelo() {
        $sql = "SELECT d.*, 
                (SELECT nombreEstado FROM estados WHERE idEstado = d.idEstado) as nombreEstado
                FROM directores d 
                ORDER BY d.idDirector ASC";
        
        $resultado = $this->conexion->query($sql);
        $directores = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $directores[] = $fila;
            }
        }
        return $directores;
    }

    // Insert using Prepared Statements (No concatenation)
    public function insertarDirectoresModelo($datos) {
        $sql = "INSERT INTO directores (nombreDirector, emailDirector, ciudadDirector, 
                                       codigoPostalDirector, direccionDirector, telefonoDirector, 
                                       dniDirector, fechaAltaDirector, idEstado)
                VALUES (?,?,?,?,?,?,?,?,?)";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('sssissssi',
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

    // Update using Prepared Statements
    public function actualizarDirectoresModelo($datos) {
        $sql = "UPDATE directores SET nombreDirector = ?, emailDirector = ?, ciudadDirector = ?, 
                                      codigoPostalDirector = ?, direccionDirector = ?, telefonoDirector = ?, 
                                      dniDirector = ?, fechaAltaDirector = ?, idEstado = ?
                WHERE idDirector = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('sssissssii',
            $datos['nombreDirector'],
            $datos['emailDirector'],
            $datos['ciudadDirector'],
            $datos['codigoPostalDirector'],
            $datos['direccionDirector'],
            $datos['telefonoDirector'],
            $datos['dniDirector'],
            $datos['fechaAltaDirector'],
            $datos['idEstado'],
            $datos['idDirector']
        );
        return $stmt->execute();
    }

    public function eliminarDirectoresModelo($id) {
        $sql = "DELETE FROM directores WHERE idDirector = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    public function obtenerDirectorPorIdModelo($id) {
        $sql = "SELECT * FROM directores WHERE idDirector = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
?>
