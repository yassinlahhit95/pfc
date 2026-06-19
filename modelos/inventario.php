<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarTodosLosPrestamos() {
    $con = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante,
                   dispositivos.nombreDispositivo AS nombreArticulo,
                   dispositivos.idDispositivo AS idArticulo
            FROM prestamos
            JOIN estudiantes  ON prestamos.idEstudiante = estudiantes.idEstudiante
            JOIN dispositivos ON prestamos.numeroSerie  = dispositivos.numeroSerie
            ORDER BY idPrestamo DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarArticulos() {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo AS idArticulo, nombreDispositivo AS nombreArticulo,
                   numeroSerie, estadoDispositivo AS estado
            FROM dispositivos
            ORDER BY idDispositivo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerArticuloPorId($idArticulo) {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo AS idArticulo, nombreDispositivo AS nombreArticulo,
                   numeroSerie, estadoDispositivo AS estado
            FROM dispositivos WHERE idDispositivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarArticulo($nombreArticulo, $numeroSerie) {
    $con = obtenerConexion();
    $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) VALUES (?, ?, 'disponible')";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $nombreArticulo, $numeroSerie);
    return mysqli_stmt_execute($stmt);
}

function registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo) {
    $con = obtenerConexion();
    $sql = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $numeroSerie = $fila['numeroSerie'];
    $sql = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) VALUES (?, ?, ?, 'en curso')";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $idEstudiante, $numeroSerie, $fechaPrestamo);
    mysqli_stmt_execute($stmt);
    $sql = "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoDispositivo) {
    $con = obtenerConexion();
    $sql = "UPDATE dispositivos SET nombreDispositivo=?, numeroSerie=?, estadoDispositivo=? WHERE idDispositivo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombreArticulo, $numeroSerie, $estadoDispositivo, $idArticulo);
    return mysqli_stmt_execute($stmt);
}

function devolverPrestamo($idPrestamo) {
    $con = obtenerConexion();
    $sql = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPrestamo);
    mysqli_stmt_execute($stmt);
    $numeroSerie = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['numeroSerie'];
    $fechaHoy = date('Y-m-d');
    $sql = "UPDATE prestamos SET fechaDevolucion = ?, estadoPrestamo = 'devuelto' WHERE idPrestamo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $fechaHoy, $idPrestamo);
    $resultado = mysqli_stmt_execute($stmt);
    $sql = "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $numeroSerie);
    mysqli_stmt_execute($stmt);
    return $resultado;
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarArticulo($idArticulo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM dispositivos WHERE idDispositivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function checkArticuloExistente($numeroSerie, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo FROM dispositivos WHERE numeroSerie = ? AND idDispositivo != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $numeroSerie, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($resultado) > 0;
}
