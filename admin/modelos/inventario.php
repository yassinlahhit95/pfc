<?php
require_once "conexion.php";

class inventario {
    protected $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    public function listarArticulosModelo() {
        $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, estadoDispositivo as estado 
                FROM dispositivos ORDER BY nombreDispositivo ASC";
        $resultado = $this->conexion->query($sql);
        $articulos = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $fila['cantidadTotal'] = 1;
                $fila['cantidadDisponible'] = ($fila['estado'] == 'disponible') ? 1 : 0;
                $articulos[] = $fila;
            }
        }
        return $articulos;
    }

    // Listar préstamos que están en uso actualmente
    public function listarPrestamosActivos() {
        $sql = "SELECT * FROM prestamos WHERE estadoPrestamo = 'en curso' OR estadoPrestamo = 'activo'";
        $resultado = $this->conexion->query($sql);
        $prestamos = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $nSerie = $fila['numeroSerie'];
                $sqlDev = "SELECT nombreDispositivo FROM dispositivos WHERE numeroSerie = '$nSerie' LIMIT 1";
                $resDev = $this->conexion->query($sqlDev);
                $dispositivo = $resDev->fetch_assoc();
                $fila['nombreArticulo'] = $dispositivo['nombreDispositivo'] ?? 'Desconocido';

                $idEst = $fila['idEstudiante'];
                $sqlEst = "SELECT nombreEstudiante FROM estudiantes WHERE idEstudiante = '$idEst' LIMIT 1";
                $resEst = $this->conexion->query($sqlEst);
                $estudiante = $resEst->fetch_assoc();
                $fila['nombreEstudiante'] = $estudiante['nombreEstudiante'] ?? 'Alumno';

                $prestamos[] = $fila;
            }
        }
        return $prestamos;
    }

    // NUEVO: Listar historial de préstamos (los ya devueltos)
    public function listarHistorialPrestamosModelo() {
        $sql = "SELECT * FROM prestamos WHERE estadoPrestamo = 'devuelto' ORDER BY fechaDevolucion DESC";
        $resultado = $this->conexion->query($sql);
        $historial = [];
        if ($resultado) {
            while ($fila = $resultado->fetch_assoc()) {
                $nSerie = $fila['numeroSerie'];
                $sqlDev = "SELECT nombreDispositivo FROM dispositivos WHERE numeroSerie = '$nSerie' LIMIT 1";
                $resDev = $this->conexion->query($sqlDev);
                $dispositivo = $resDev->fetch_assoc();
                $fila['nombreArticulo'] = $dispositivo['nombreDispositivo'] ?? 'Equipo';

                $idEst = $fila['idEstudiante'];
                $sqlEst = "SELECT nombreEstudiante FROM estudiantes WHERE idEstudiante = '$idEst' LIMIT 1";
                $resEst = $this->conexion->query($sqlEst);
                $estudiante = $resEst->fetch_assoc();
                $fila['nombreEstudiante'] = $estudiante['nombreEstudiante'] ?? 'Alumno';

                $historial[] = $fila;
            }
        }
        return $historial;
    }

    public function realizarPrestamoModelo($idArticulo, $idEstudiante, $fecha) {
        $sqlInfo = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = ?";
        $stmtInfo = $this->conexion->prepare($sqlInfo);
        $stmtInfo->bind_param("i", $idArticulo);
        $stmtInfo->execute();
        $resultado = $stmtInfo->get_result();
        $dispositivo = $resultado->fetch_assoc();
        $nSerie = $dispositivo['numeroSerie'];

        $sqlPres = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) VALUES (?, ?, ?, 'en curso')";
        $stmtPres = $this->conexion->prepare($sqlPres);
        $stmtPres->bind_param("iss", $idEstudiante, $nSerie, $fecha);
        $exitoPres = $stmtPres->execute();

        $sqlAct = "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = ?";
        $stmtAct = $this->conexion->prepare($sqlAct);
        $stmtAct->bind_param("i", $idArticulo);
        $exitoAct = $stmtAct->execute();

        return ($exitoPres && $exitoAct);
    }

    public function devolverPrestamoModelo($idPrestamo) {
        $sqlInfo = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = ?";
        $stmtInfo = $this->conexion->prepare($sqlInfo);
        $stmtInfo->bind_param("i", $idPrestamo);
        $stmtInfo->execute();
        $res = $stmtInfo->get_result();
        $prestamo = $res->fetch_assoc();
        $nSerie = $prestamo['numeroSerie'];

        $sqlPres = "UPDATE prestamos SET estadoPrestamo = 'devuelto', fechaDevolucion = CURDATE() WHERE idPrestamo = ?";
        $stmtPres = $this->conexion->prepare($sqlPres);
        $stmtPres->bind_param("i", $idPrestamo);
        $exitoPres = $stmtPres->execute();

        $sqlAct = "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = ?";
        $stmtAct = $this->conexion->prepare($sqlAct);
        $stmtAct->bind_param("s", $nSerie);
        $exitoAct = $stmtAct->execute();

        return ($exitoPres && $exitoAct);
    }

    public function insertarArticuloModelo($datos) {
        $sql = "INSERT INTO dispositivos (nombreDispositivo, estadoDispositivo) VALUES (?, 'disponible')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('s', $datos['nombreArticulo']);
        return $stmt->execute();
    }

    public function eliminarArticuloModelo($id) {
        $sql = "DELETE FROM dispositivos WHERE idDispositivo = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }
}
?>