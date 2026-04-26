<?php
require_once("conectar.php");

/**
 * Lista el historial completo de préstamos de dispositivos
 */
function listarTodosLosPrestamos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT prestamos.*, estudiantes.nombreEstudiante, dispositivos.nombreDispositivo as nombreArticulo, dispositivos.idDispositivo as idArticulo 
                    FROM prestamos 
                    JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante 
                    JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie 
                    ORDER BY idPrestamo DESC";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalPrestamos = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalPrestamos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalPrestamos;
}

/**
 * Lista todos los artículos (dispositivos) registrados en el inventario
 */
function listarArticulos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, numeroSerie, estadoDispositivo as estado 
                    FROM dispositivos ORDER BY idDispositivo ASC";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaFinalArticulos = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaFinalArticulos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaFinalArticulos;
}

/**
 * Lista únicamente los préstamos que aún no han sido devueltos
 */
function listarPrestamosActivos() {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "SELECT prestamos.*, estudiantes.nombreEstudiante, dispositivos.nombreDispositivo as nombreArticulo 
                    FROM prestamos 
                    JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante 
                    JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie 
                    WHERE prestamos.estadoPrestamo = 'en curso' ORDER BY idPrestamo DESC";
                    
    $resultadoConsulta = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    $listaPrestamosActivos = array();
    
    while($filaDeDatos = mysqli_fetch_assoc($resultadoConsulta)) {
        $listaPrestamosActivos[] = $filaDeDatos;
    }
    
    mysqli_close($conexionBaseDatos);
    return $listaPrestamosActivos;
}

/**
 * Registra un nuevo artículo en el inventario
 */
function insertarArticulo($nombreRecibido, $numeroSerieRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $numeroSerieMayusculas = strtoupper($numeroSerieRecibido);
    
    $sentenciaSQL = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) 
                    VALUES ('$nombreRecibido', '$numeroSerieMayusculas', 'disponible')";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Elimina un artículo del inventario
 */
function eliminarArticulo($idArticuloABorrar) {
    $conexionBaseDatos = obtenerConexion();
    $sentenciaSQL = "DELETE FROM dispositivos WHERE idDispositivo = $idArticuloABorrar";
    
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sentenciaSQL);
    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Registra el préstamo de un dispositivo a un estudiante
 */
function registrarPrestamo($idEstudianteRecibido, $idArticuloRecibido, $fechaDelPrestamo) {
    $conexionBaseDatos = obtenerConexion();
    
    // 1. Buscamos el número de serie del dispositivo
    $sqlSerie = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = $idArticuloRecibido";
    $resSerie = mysqli_query($conexionBaseDatos, $sqlSerie);
    $datosArticulo = mysqli_fetch_assoc($resSerie);
    $numeroDeSerie = $datosArticulo['numeroSerie'];

    // 2. Insertamos el registro en la tabla de préstamos
    $sqlInsert = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) 
                  VALUES ($idEstudianteRecibido, '$numeroDeSerie', '$fechaDelPrestamo', 'en curso')";
    mysqli_query($conexionBaseDatos, $sqlInsert);

    // 3. Cambiamos el estado del dispositivo a 'prestado'
    $sqlUpdate = "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = $idArticuloRecibido";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sqlUpdate);

    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}

/**
 * Registra la devolución de un dispositivo
 */
function devolverPrestamo($idPrestamoRecibido) {
    $conexionBaseDatos = obtenerConexion();
    $fechaDeHoy = date('Y-m-d');
    
    // 1. Buscamos el número de serie asociado a este préstamo
    $sqlSerie = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = $idPrestamoRecibido";
    $resSerie = mysqli_query($conexionBaseDatos, $sqlSerie);
    $datosPrestamo = mysqli_fetch_assoc($resSerie);
    $numeroDeSerie = $datosPrestamo['numeroSerie'];

    // 2. Marcamos el préstamo como 'devuelto'
    $sqlPrestamo = "UPDATE prestamos SET fechaDevolucion = '$fechaDeHoy', estadoPrestamo = 'devuelto' WHERE idPrestamo = $idPrestamoRecibido";
    mysqli_query($conexionBaseDatos, $sqlPrestamo);

    // 3. Cambiamos el estado del dispositivo a 'disponible'
    $sqlArticulo = "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = '$numeroDeSerie'";
    $resultadoOperacion = mysqli_query($conexionBaseDatos, $sqlArticulo);

    mysqli_close($conexionBaseDatos);
    return $resultadoOperacion;
}
?>