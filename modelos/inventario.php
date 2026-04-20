<?php
require_once("conectar.php");

function listarArticulos() {
    $conexion = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, numeroSerie, estadoDispositivo as estado FROM dispositivos ORDER BY idDispositivo ASC";
    $resultado = mysqli_query($conexion, $sql);
    $datos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $datos[] = $fila;
    }
    mysqli_close($conexion);
    return $datos;
}

function listarPrestamosActivos() {
    $conexion = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante, dispositivos.nombreDispositivo as nombreArticulo 
            FROM prestamos 
            JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante
            JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie
            WHERE prestamos.estadoPrestamo = 'en curso'
            ORDER BY idPrestamo DESC";
            
    $resultado = mysqli_query($conexion, $sql);
    $datos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $datos[] = $fila;
    }
    mysqli_close($conexion);
    return $datos;
}

function insertarEquipo($nombre, $numeroSerie, $estado) {
    $conexion = obtenerConexion();
    $numeroSerie = strtoupper($numeroSerie);
    
    $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) 
            VALUES ('$nombre', '$numeroSerie', '$estado')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function insertarArticulo($nombre, $numeroSerie) {
    return insertarEquipo($nombre, $numeroSerie, 'disponible');
}

function eliminarEquipo($id) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM dispositivos WHERE idDispositivo = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function borrarArticulo($id) {
    return eliminarEquipo($id);
}

function devolverPrestamo($idPrestamo) {
    return devolverEquipo($idPrestamo, date('Y-m-d'));
}

function registrarPrestamo($idEstudiante, $idDispositivo, $fecha) {
    $conexion = obtenerConexion();
    
    // Obtener el número de serie del dispositivo
    $sqlSerie = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = $idDispositivo";
    $resSerie = mysqli_query($conexion, $sqlSerie);
    $filaSerie = mysqli_fetch_assoc($resSerie);
    $numeroSerie = $filaSerie['numeroSerie'];

    // Registrar el prestamo
    $sql = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) 
            VALUES ($idEstudiante, '$numeroSerie', '$fecha', 'en curso')";
    
    if (mysqli_query($conexion, $sql)) {
        // Actualizar estado del dispositivo
        mysqli_query($conexion, "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = $idDispositivo");
        mysqli_close($conexion);
        return true;
    }
    mysqli_close($conexion);
    return false;
}

function devolverEquipo($idPrestamo, $fecha) {
    $conexion = obtenerConexion();
    
    // Obtener el número de serie para liberar el dispositivo
    $sqlS = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = $idPrestamo";
    $resS = mysqli_query($conexion, $sqlS);
    $filaS = mysqli_fetch_assoc($resS);
    $serie = $filaS['numeroSerie'];

    $sql = "UPDATE prestamos SET fechaDevolucion = '$fecha', estadoPrestamo = 'devuelto' 
            WHERE idPrestamo = $idPrestamo";
            
    if (mysqli_query($conexion, $sql)) {
        // Actualizar estado del dispositivo
        mysqli_query($conexion, "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = '$serie'");
        mysqli_close($conexion);
        return true;
    }
    mysqli_close($conexion);
    return false;
}
?>