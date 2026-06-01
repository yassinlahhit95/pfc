<?php
require_once __DIR__ . "/conectar.php";

function listarTodosLosAnuncios() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM anuncios ORDER BY idAnuncio DESC";
    $resultado = mysqli_query($con, $sql);

    $listaAnuncios = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $fila['tituloAnuncio'] = $fila['titulo'];
        $fila['contenidoAnuncio'] = $fila['mensaje'];
        $listaAnuncios[] = $fila;
    }
    
    return $listaAnuncios;
}

function insertarAnuncio($titulo, $mensaje, $dirigidoA = 'todos') {
    $con = obtenerConexion();
    $fechaActual = date('Y-m-d H:i:s');
    $fechaExpiracion = date('Y-m-d', strtotime('+1 month'));

    $sql = "INSERT INTO anuncios (titulo, mensaje, fechaAnuncio, fechaExpiracion, dirigidoA) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $titulo, $mensaje, $fechaActual, $fechaExpiracion, $dirigidoA);
    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function eliminarAnuncio($idAnuncio) {
    $con = obtenerConexion();
    $sql = "DELETE FROM anuncios WHERE idAnuncio = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idAnuncio);
    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function obtenerAnuncioPorId($idAnuncio) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM anuncios WHERE idAnuncio = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idAnuncio);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $anuncio = mysqli_fetch_assoc($resultado);

    if ($anuncio) {
        $anuncio['tituloAnuncio'] = $anuncio['titulo'];
        $anuncio['contenidoAnuncio'] = $anuncio['mensaje'];
    }

    
    return $anuncio;
}

function actualizarAnuncio($idAnuncio, $titulo, $mensaje, $fechaExpiracion, $dirigidoA) {
    $con = obtenerConexion();
    $sql = "UPDATE anuncios SET titulo=?, mensaje=?, fechaExpiracion=?, dirigidoA=? WHERE idAnuncio=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $titulo, $mensaje, $fechaExpiracion, $dirigidoA, $idAnuncio);
    $resultado = mysqli_stmt_execute($stmt);
    
    return $resultado;
}

function listarAnunciosPorRol($rolUsuario) {
    $con = obtenerConexion();
    $hoy = date('Y-m-d');
    $sql = "SELECT * FROM anuncios WHERE fechaExpiracion >= ? AND (dirigidoA = ? OR dirigidoA = 'todos') ORDER BY idAnuncio DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $hoy, $rolUsuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaAnuncios = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $fila['tituloAnuncio'] = $fila['titulo'];
        $fila['contenidoAnuncio'] = $fila['mensaje'];
        $listaAnuncios[] = $fila;
    }
    
    return $listaAnuncios;
}



