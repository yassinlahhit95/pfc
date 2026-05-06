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

// Comprobar si ya existe un artículo con el mismo número de serie
function checkArticuloExistente($numeroSerie, $idExcluir = null) {
    $con = obtenerConexion();
    $serieUppercase = strtoupper($numeroSerie);
    if ($idExcluir) {
        $stmt = mysqli_prepare($con, "SELECT idDispositivo FROM dispositivos WHERE numeroSerie = ? AND idDispositivo != ?");
        mysqli_stmt_bind_param($stmt, "si", $serieUppercase, $idExcluir);
    } else {
        $stmt = mysqli_prepare($con, "SELECT idDispositivo FROM dispositivos WHERE numeroSerie = ?");
        mysqli_stmt_bind_param($stmt, "s", $serieUppercase);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

// Registrar un nuevo dispositivo en el inventario
function insertarArticulo($nombreArticulo, $numeroSerie) {
    if (checkArticuloExistente($numeroSerie)) {
        return false;
    }
    $con = obtenerConexion();
    $serieMayusculas = strtoupper($numeroSerie);
    $stmt = mysqli_prepare($con, "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) VALUES (?, ?, 'disponible')");
    mysqli_stmt_bind_param($stmt, "ss", $nombreArticulo, $serieMayusculas);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un dispositivo del inventario por su ID
function eliminarArticulo($idArticulo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM dispositivos WHERE idDispositivo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Registrar la salida de un dispositivo (préstamo a un estudiante)
function registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo) {
    $con = obtenerConexion();

    // Obtenemos el número de serie del dispositivo para el registro del préstamo
    $stmt = mysqli_prepare($con, "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $numeroSerie = $fila['numeroSerie'];

    // Insertamos el registro del préstamo
    $stmt = mysqli_prepare($con, "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) VALUES (?, ?, ?, 'en curso')");
    mysqli_stmt_bind_param($stmt, "iss", $idEstudiante, $numeroSerie, $fechaPrestamo);
    mysqli_stmt_execute($stmt);

    // Actualizamos el estado del dispositivo a 'prestado'
    $stmt = mysqli_prepare($con, "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    $resultado = mysqli_stmt_execute($stmt);

    mysqli_close($con);
    return $resultado;
}

// Procesar la devolución de un dispositivo prestado
function devolverPrestamo($idPrestamo) {
    $con = obtenerConexion();

    // Localizamos el dispositivo vinculado al préstamo
    $stmt = mysqli_prepare($con, "SELECT numeroSerie FROM prestamos WHERE idPrestamo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idPrestamo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $numeroSerie = $fila['numeroSerie'];
    $fechaHoy = date('Y-m-d');

    // Marcamos el préstamo como finalizado
    $stmt = mysqli_prepare($con, "UPDATE prestamos SET fechaDevolucion = ?, estadoPrestamo = 'devuelto' WHERE idPrestamo = ?");
    mysqli_stmt_bind_param($stmt, "si", $fechaHoy, $idPrestamo);
    $resultado = mysqli_stmt_execute($stmt);

    // Volvemos a poner el dispositivo como disponible en el inventario
    $stmt = mysqli_prepare($con, "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = ?");
    mysqli_stmt_bind_param($stmt, "s", $numeroSerie);
    mysqli_stmt_execute($stmt);

    mysqli_close($con);
    return $resultado;
}

// Obtener la información de un artículo específico por su ID
function obtenerArticuloPorId($idArticulo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, numeroSerie, estadoDispositivo as estado FROM dispositivos WHERE idDispositivo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $articulo = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $articulo;
}

// Actualizar los datos técnicos o el estado de un dispositivo
function actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoDispositivo) {
    if (checkArticuloExistente($numeroSerie, $idArticulo)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE dispositivos SET nombreDispositivo=?, numeroSerie=?, estadoDispositivo=? WHERE idDispositivo=?");
    mysqli_stmt_bind_param($stmt, "sssi", $nombreArticulo, $numeroSerie, $estadoDispositivo, $idArticulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}
