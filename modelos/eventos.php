<?php
require_once __DIR__ . "/conectar.php";

function listarEventosProximos() {
    $con = obtenerConexion();
    $hoy = date('Y-m-d');

    $sql = "SELECT * FROM eventos WHERE fechaEvento >= ? ORDER BY fechaEvento ASC, horaEvento ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $hoy);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaEventos = [];

    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaEventos[] = $fila;
    }

    mysqli_close($con);
    return $listaEventos;
}

function insertarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    $sql = "INSERT INTO eventos (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $titulo, $descripcion, $fecha, $hora, $ubicacion);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function eliminarEvento($idEvento) {
    $con = obtenerConexion();
    $sql = "DELETE FROM eventos WHERE idEvento = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerEventoPorId($idEvento) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM eventos WHERE idEvento = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $evento = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $evento;
}

function actualizarEvento($idEvento, $titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    $sql = "UPDATE eventos SET tituloEvento=?, descripcionEvento=?, fechaEvento=?, horaEvento=?, ubicacionEvento=? WHERE idEvento=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $titulo, $descripcion, $fecha, $hora, $ubicacion, $idEvento);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

?>
