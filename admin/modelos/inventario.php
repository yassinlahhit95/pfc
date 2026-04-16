<?php
require_once("conectar.php");

class inventario {
    public function listarArticulosModelo() {
        $bd = getConnection();
        $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, estadoDispositivo as estado, numeroSerie 
                FROM dispositivos ORDER BY nombreDispositivo ASC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $fila['cantidadTotal'] = 1;
                $fila['cantidadDisponible'] = ($fila['estado'] == 'disponible') ? 1 : 0;
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function insertarArticuloModelo($nombre, $nSerie) {
        $bd = getConnection();
        $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) VALUES (?, ?, 'disponible')";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("ss", $nombre, $nSerie);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function eliminarArticuloModelo($id) {
        $bd = getConnection();
        $sql = "DELETE FROM dispositivos WHERE idDispositivo = ?";
        $stmt = $bd->prepare($sql);
        $stmt->bind_param("i", $id);
        $resultado = $stmt->execute();
        $bd->close();
        return $resultado;
    }

    public function listarPrestamosActivos() {
        $bd = getConnection();
        $sql = "SELECT p.*, d.nombreDispositivo as nombreArticulo, e.nombreEstudiante 
                FROM prestamos p 
                JOIN dispositivos d ON p.numeroSerie = d.numeroSerie 
                JOIN estudiantes e ON p.idEstudiante = e.idEstudiante 
                WHERE p.estadoPrestamo = 'en curso' OR p.estadoPrestamo = 'activo'";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function listarHistorialPrestamosModelo() {
        $bd = getConnection();
        $sql = "SELECT p.*, d.nombreDispositivo as nombreArticulo, e.nombreEstudiante 
                FROM prestamos p 
                JOIN dispositivos d ON p.numeroSerie = d.numeroSerie 
                JOIN estudiantes e ON p.idEstudiante = e.idEstudiante 
                WHERE p.estadoPrestamo = 'devuelto' 
                ORDER BY p.fechaDevolucion DESC";
        $datos = [];
        if ($resultado = $bd->query($sql)) {
            while ($fila = $resultado->fetch_assoc()) {
                $datos[] = $fila;
            }
        }
        $bd->close();
        return $datos;
    }

    public function realizarPrestamoModelo($idArticulo, $idEstudiante, $fecha) {
        $bd = getConnection();
        $sqlInfo = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = ?";
        $stmtInfo = $bd->prepare($sqlInfo);
        $stmtInfo->bind_param("i", $idArticulo);
        $stmtInfo->execute();
        $resultado = $stmtInfo->get_result();
        $dispositivo = $resultado->fetch_assoc();
        $nSerie = $dispositivo['numeroSerie'];

        $sqlPres = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) VALUES (?, ?, ?, 'en curso')";
        $stmtPres = $bd->prepare($sqlPres);
        $stmtPres->bind_param("iss", $idEstudiante, $nSerie, $fecha);
        $exitoPres = $stmtPres->execute();

        $sqlAct = "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = ?";
        $stmtAct = $bd->prepare($sqlAct);
        $stmtAct->bind_param("i", $idArticulo);
        $exitoAct = $stmtAct->execute();

        $bd->close();
        return ($exitoPres && $exitoAct);
    }

    public function devolverPrestamoModelo($idPrestamo) {
        $bd = getConnection();
        $sqlInfo = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = ?";
        $stmtInfo = $bd->prepare($sqlInfo);
        $stmtInfo->bind_param("i", $idPrestamo);
        $stmtInfo->execute();
        $res = $stmtInfo->get_result();
        $prestamo = $res->fetch_assoc();
        $nSerie = $prestamo['numeroSerie'];

        $sqlPres = "UPDATE prestamos SET estadoPrestamo = 'devuelto', fechaDevolucion = CURDATE() WHERE idPrestamo = ?";
        $stmtPres = $bd->prepare($sqlPres);
        $stmtPres->bind_param("i", $idPrestamo);
        $exitoPres = $stmtPres->execute();

        $sqlAct = "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = ?";
        $stmtAct = $bd->prepare($sqlAct);
        $stmtAct->bind_param("s", $nSerie);
        $exitoAct = $stmtAct->execute();

        $bd->close();
        return ($exitoPres && $exitoAct);
    }
}
?>
