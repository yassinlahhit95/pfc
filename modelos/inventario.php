<?php
require_once __DIR__ . "/conectar.php";

function listarTodosLosPrestamos() {
    $con = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante,
                   dispositivos.nombreDispositivo as nombreArticulo,
                   dispositivos.idDispositivo as idArticulo
            FROM prestamos
            JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante
            JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie
            ORDER BY idPrestamo DESC";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaPrestamos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaPrestamos[] = $fila;
    }
    mysqli_close($con);
    return $listaPrestamos;
}

function listarArticulos() {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo,
                   numeroSerie, estadoDispositivo as estado
            FROM dispositivos
            ORDER BY idDispositivo ASC";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaArticulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaArticulos[] = $fila;
    }
    mysqli_close($con);
    return $listaArticulos;
}

function listarPrestamosActivos() {
    $con = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante,
                   dispositivos.nombreDispositivo as nombreArticulo
            FROM prestamos
            JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante
            JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie
            WHERE prestamos.estadoPrestamo = 'en curso'
            ORDER BY idPrestamo DESC";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaPrestamosActivos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaPrestamosActivos[] = $fila;
    }
    mysqli_close($con);
    return $listaPrestamosActivos;
}

function checkArticuloExistente($numeroSerie, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo FROM dispositivos WHERE numeroSerie = ? AND idDispositivo != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $numeroSerie, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

function insertarArticulo($nombreArticulo, $numeroSerie) {
    $con = obtenerConexion();
    $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) VALUES (?, ?, 'disponible')";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $nombreArticulo, $numeroSerie);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function eliminarArticulo($idArticulo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM dispositivos WHERE idDispositivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo) {
    $con = obtenerConexion();

    $sql = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $numeroSerie = $fila['numeroSerie'];

    $sql = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) VALUES (?, ?, ?, 'en curso')";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $idEstudiante, $numeroSerie, $fechaPrestamo);
    mysqli_stmt_execute($stmt);

    $sql = "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    $resultado = mysqli_stmt_execute($stmt);

    mysqli_close($con);
    return $resultado;
}

function devolverPrestamo($idPrestamo) {
    $con = obtenerConexion();

    $sql = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPrestamo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $numeroSerie = $fila['numeroSerie'];
    $fechaHoy = date('Y-m-d');

    $sql = "UPDATE prestamos SET fechaDevolucion = ?, estadoPrestamo = 'devuelto' WHERE idPrestamo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $fechaHoy, $idPrestamo);
    $resultado = mysqli_stmt_execute($stmt);

    $sql = "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $numeroSerie);
    mysqli_stmt_execute($stmt);

    mysqli_close($con);
    return $resultado;
}

function obtenerArticuloPorId($idArticulo) {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, numeroSerie, estadoDispositivo as estado FROM dispositivos WHERE idDispositivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $articulo = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $articulo;
}

function actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoDispositivo) {
    $con = obtenerConexion();
    $sql = "UPDATE dispositivos SET nombreDispositivo=?, numeroSerie=?, estadoDispositivo=? WHERE idDispositivo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombreArticulo, $numeroSerie, $estadoDispositivo, $idArticulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

?>
