<?php
require_once("conectar.php");

function listarTodosLosPrestamos() {
    $conexion = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante, dispositivos.nombreDispositivo as nombreArticulo, dispositivos.idDispositivo as idArticulo 
            FROM prestamos 
            JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante 
            JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie 
            ORDER BY idPrestamo DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarArticulos() {
    $conexion = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, numeroSerie, estadoDispositivo as estado 
            FROM dispositivos ORDER BY idDispositivo ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarPrestamosActivos() {
    $conexion = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante, dispositivos.nombreDispositivo as nombreArticulo 
            FROM prestamos 
            JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante 
            JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie 
            WHERE prestamos.estadoPrestamo = 'en curso' ORDER BY idPrestamo DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarArticulo($nombre, $numeroSerie) {
    $conexion = obtenerConexion();
    $numeroSerie = strtoupper($numeroSerie);
    $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) 
            VALUES ('$nombre', '$numeroSerie', 'disponible')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarArticulo($idArticulo) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM dispositivos WHERE idDispositivo = $idArticulo";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function registrarPrestamo($idEstudiante, $idArticulo, $fecha) {
    $conexion = obtenerConexion();
    
    $sqlSerie = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = $idArticulo";
    $resultadoSerie = mysqli_query($conexion, $sqlSerie);
    $fila = mysqli_fetch_assoc($resultadoSerie);
    $numeroSerie = $fila['numeroSerie'];

    $sqlInsert = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) 
                  VALUES ($idEstudiante, '$numeroSerie', '$fecha', 'en curso')";
    mysqli_query($conexion, $sqlInsert);

    $sqlUpdate = "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = $idArticulo";
    $resultado = mysqli_query($conexion, $sqlUpdate);

    mysqli_close($conexion);
    return $resultado;
}

function devolverPrestamo($idPrestamo) {
    $conexion = obtenerConexion();
    $fecha = date('Y-m-d');
    
    $sqlSerie = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = $idPrestamo";
    $resultadoSerie = mysqli_query($conexion, $sqlSerie);
    $fila = mysqli_fetch_assoc($resultadoSerie);
    $numeroSerie = $fila['numeroSerie'];

    $sqlPrestamo = "UPDATE prestamos SET fechaDevolucion = '$fecha', estadoPrestamo = 'devuelto' WHERE idPrestamo = $idPrestamo";
    mysqli_query($conexion, $sqlPrestamo);

    $sqlArticulo = "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = '$numeroSerie'";
    $resultado = mysqli_query($conexion, $sqlArticulo);

    mysqli_close($conexion);
    return $resultado;
}
?>