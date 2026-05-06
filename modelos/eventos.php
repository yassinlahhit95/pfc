<?php
require_once __DIR__ . "/conectar.php";

// Obtener los próximos eventos (a partir de hoy)
function listarEventosProximos() {
    $con = obtenerConexion();
    $hoy = date('Y-m-d');

    $stmt = mysqli_prepare($con, "SELECT * FROM eventos WHERE fechaEvento >= ? ORDER BY fechaEvento ASC, horaEvento ASC");
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

// Insertar un nuevo evento en el calendario
function insertarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO eventos (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssss", $titulo, $descripcion, $fecha, $hora, $ubicacion);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un evento por su ID
function eliminarEvento($idEvento) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM eventos WHERE idEvento = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un evento específico
function obtenerEventoPorId($idEvento) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM eventos WHERE idEvento = ?");
    mysqli_stmt_bind_param($stmt, "i", $idEvento);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $evento = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $evento;
}

// Actualizar los datos de un evento existente
function actualizarEvento($idEvento, $titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE eventos SET tituloEvento=?, descripcionEvento=?, fechaEvento=?, horaEvento=?, ubicacionEvento=? WHERE idEvento=?");
    mysqli_stmt_bind_param($stmt, "sssssi", $titulo, $descripcion, $fecha, $hora, $ubicacion, $idEvento);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}
