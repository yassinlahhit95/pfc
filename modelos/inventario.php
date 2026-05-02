<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todos los préstamos realizados en el sistema
function listarTodosLosPrestamos() {
    $con = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante, 
                   dispositivos.nombreDispositivo as nombreArticulo, 
                   dispositivos.idDispositivo as idArticulo 
            FROM prestamos 
            JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante 
            JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie 
            ORDER BY idPrestamo DESC";
            
    $resultado = mysqli_query($con, $sql);
    $listaPrestamos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaPrestamos[] = $fila; 
    }
    mysqli_close($con);
    return $listaPrestamos;
}

// Obtener el inventario completo de dispositivos
function listarArticulos() {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, 
                   numeroSerie, estadoDispositivo as estado 
            FROM dispositivos 
            ORDER BY idDispositivo ASC";
            
    $resultado = mysqli_query($con, $sql);
    $listaArticulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaArticulos[] = $fila; 
    }
    mysqli_close($con);
    return $listaArticulos;
}

// Listar únicamente los préstamos que aún no han sido devueltos
function listarPrestamosActivos() {
    $con = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante, 
                   dispositivos.nombreDispositivo as nombreArticulo 
            FROM prestamos 
            JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante 
            JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie 
            WHERE prestamos.estadoPrestamo = 'en curso' 
            ORDER BY idPrestamo DESC";
            
    $resultado = mysqli_query($con, $sql);
    $listaPrestamosActivos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaPrestamosActivos[] = $fila; 
    }
    mysqli_close($con);
    return $listaPrestamosActivos;
}

// Registrar un nuevo dispositivo en el inventario
function insertarArticulo($nombreArticulo, $numeroSerie) {
    $con = obtenerConexion();
    $serieMayusculas = strtoupper($numeroSerie);
    $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) 
            VALUES ('$nombreArticulo', '$serieMayusculas', 'disponible')";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un dispositivo del inventario por su ID
function eliminarArticulo($idArticulo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM dispositivos WHERE idDispositivo = $idArticulo";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Registrar la salida de un dispositivo (préstamo a un estudiante)
function registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo) {
    $con = obtenerConexion();
    
    // Obtenemos el número de serie del dispositivo para el registro del préstamo
    $sql = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = $idArticulo";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    $numeroSerie = $fila['numeroSerie'];

    // Insertamos el registro del préstamo
    $sql = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) 
                    VALUES ($idEstudiante, '$numeroSerie', '$fechaPrestamo', 'en curso')";
    $resultado = mysqli_query($con, $sql);
    
    // Actualizamos el estado del dispositivo a 'prestado'
    $sql = "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = $idArticulo";
    $resultado = mysqli_query($con, $sql);
    
    mysqli_close($con);
    return $resultado;
}

// Procesar la devolución de un dispositivo prestado
function devolverPrestamo($idPrestamo) {
    $con = obtenerConexion();
    
    // Localizamos el dispositivo vinculado al préstamo
    $sql = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = $idPrestamo";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    $numeroSerie = $fila['numeroSerie'];
    $fechaHoy = date('Y-m-d');

    // Marcamos el préstamo como finalizado
    $sql = "UPDATE prestamos 
                     SET fechaDevolucion = '$fechaHoy', estadoPrestamo = 'devuelto' 
                     WHERE idPrestamo = $idPrestamo";
    $resultado = mysqli_query($con, $sql);
    
    // Volvemos a poner el dispositivo como disponible en el inventario
    $sql = "UPDATE dispositivos 
                   SET estadoDispositivo = 'disponible' 
                   WHERE numeroSerie = '$numeroSerie'";
    $resultado = mysqli_query($con, $sql);
    
    mysqli_close($con);
    return $resultado;
}

// Obtener la información de un artículo específico por su ID
function obtenerArticuloPorId($idArticulo) {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, 
                   numeroSerie, estadoDispositivo as estado 
            FROM dispositivos 
            WHERE idDispositivo = $idArticulo";
    $resultado = mysqli_query($con, $sql);
    $articulo = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $articulo;
}

// Actualizar los datos técnicos o el estado de un dispositivo
function actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoDispositivo) {
    $con = obtenerConexion();
    $sql = "UPDATE dispositivos 
            SET nombreDispositivo='$nombreArticulo', numeroSerie='$numeroSerie', 
                estadoDispositivo='$estadoDispositivo' 
            WHERE idDispositivo=$idArticulo";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}
