<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarEventosProximos() {
    $con = obtenerConexion();
    $hoy = date('Y-m-d');
    $sql = "SELECT * FROM eventos WHERE fechaEvento >= ? ORDER BY fechaEvento ASC, horaEvento ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $hoy);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerEventoPorId($idEvento) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM eventos WHERE idEvento = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    $sql = "INSERT INTO eventos (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $titulo, $descripcion, $fecha, $hora, $ubicacion);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarEvento($idEvento, $titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    $sql = "UPDATE eventos SET tituloEvento=?, descripcionEvento=?, fechaEvento=?, horaEvento=?, ubicacionEvento=? WHERE idEvento=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $titulo, $descripcion, $fecha, $hora, $ubicacion, $idEvento);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarEvento($idEvento) {
    $con = obtenerConexion();
    $sql = "DELETE FROM eventos WHERE idEvento = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    return mysqli_stmt_execute($stmt);
}
