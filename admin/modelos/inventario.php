<?php
require_once("conectar.php");

function listarArticulos() {
    $conexion = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, estadoDispositivo as estado, numeroSerie 
            FROM dispositivos ORDER BY nombreDispositivo ASC";
    $datos = [];
    if ($resultado = mysqli_query($conexion, $sql)) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $fila['cantidadTotal'] = 1;
            $fila['cantidadDisponible'] = ($fila['estado'] == 'disponible') ? 1 : 0;
            $datos[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $datos;
}

function insertarArticulo($nombre, $numeroSerie) {
    $conexion = obtenerConexion();
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $numeroSerie = strtoupper(mysqli_real_escape_string($conexion, $numeroSerie));
    $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) VALUES ('$nombre', '$numeroSerie', 'disponible')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function borrarArticulo($idArticulo) {
    $conexion = obtenerConexion();
    $idArticulo = (int)$idArticulo;
    $sql = "DELETE FROM dispositivos WHERE idDispositivo = $idArticulo";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function listarPrestamosActivos() {
    $conexion = obtenerConexion();
    // Simplificado con subconsulta
    $sql = "SELECT *, 
            (SELECT nombreDispositivo FROM dispositivos WHERE dispositivos.numeroSerie = prestamos.numeroSerie) as nombreArticulo,
            (SELECT nombreEstudiante FROM estudiantes WHERE estudiantes.idEstudiante = prestamos.idEstudiante) as nombreEstudiante
            FROM prestamos 
            WHERE estadoPrestamo = 'en curso' OR estadoPrestamo = 'activo'";
    $datos = [];
    if ($resultado = mysqli_query($conexion, $sql)) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $datos[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $datos;
}

function listarHistorialPrestamos() {
    $conexion = obtenerConexion();
    // Simplificado con subconsulta
    $sql = "SELECT *, 
            (SELECT nombreDispositivo FROM dispositivos WHERE dispositivos.numeroSerie = prestamos.numeroSerie) as nombreArticulo,
            (SELECT nombreEstudiante FROM estudiantes WHERE estudiantes.idEstudiante = prestamos.idEstudiante) as nombreEstudiante
            FROM prestamos 
            WHERE estadoPrestamo = 'devuelto' 
            ORDER BY fechaDevolucion DESC";
    $datos = [];
    if ($resultado = mysqli_query($conexion, $sql)) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $datos[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $datos;
}

function realizarPrestamo($idArticulo, $idEstudiante, $fecha) {
    $conexion = obtenerConexion();
    $idArticulo = (int)$idArticulo;
    $idEstudiante = (int)$idEstudiante;
    $fecha = mysqli_real_escape_string($conexion, $fecha);

    $sqlInfo = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = $idArticulo";
    $resInfo = mysqli_query($conexion, $sqlInfo);
    $dispositivo = mysqli_fetch_assoc($resInfo);
    $numeroSerie = $dispositivo['numeroSerie'];

    $sqlPres = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) VALUES ($idEstudiante, '$numeroSerie', '$fecha', 'en curso')";
    $exitoPres = mysqli_query($conexion, $sqlPres);

    $sqlAct = "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = $idArticulo";
    $exitoAct = mysqli_query($conexion, $sqlAct);

    mysqli_close($conexion);
    return ($exitoPres && $exitoAct);
}

function devolverPrestamo($idPrestamo) {
    $conexion = obtenerConexion();
    $idPrestamo = (int)$idPrestamo;

    $sqlInfo = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = $idPrestamo";
    $resInfo = mysqli_query($conexion, $sqlInfo);
    $prestamo = mysqli_fetch_assoc($resInfo);
    $numeroSerie = $prestamo['numeroSerie'];

    $hoy = date("Y-m-d");
    $sqlPres = "UPDATE prestamos SET estadoPrestamo = 'devuelto', fechaDevolucion = '$hoy' WHERE idPrestamo = $idPrestamo";
    $exitoPres = mysqli_query($conexion, $sqlPres);

    $sqlAct = "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = '$numeroSerie'";
    $exitoAct = mysqli_query($conexion, $sqlAct);

    mysqli_close($conexion);
    return ($exitoPres && $exitoAct);
}
?>
