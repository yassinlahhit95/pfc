<?php
require_once("conectar.php");

function listarTodosLosAnuncios() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM anuncios ORDER BY idAnuncio DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarAnuncio($titulo, $mensaje, $fecha, $dirigidoA) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "INSERT INTO anuncios (titulo, mensaje, fechaExpiracion, dirigidoA) VALUES ('$titulo', '$mensaje', '$fecha', '$dirigidoA')");
    mysqli_close($conexion);
    return $resultado;
}

function eliminarAnuncio($idAnuncio) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM anuncios WHERE idAnuncio = $idAnuncio");
    mysqli_close($conexion);
    return $resultado;
}

function obtenerAnuncioPorId($idAnuncio) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM anuncios WHERE idAnuncio = $idAnuncio";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function actualizarAnuncio($idAnuncio, $titulo, $mensaje, $fecha, $dirigidoA) {
    $conexion = obtenerConexion();
    $sql = "UPDATE anuncios SET titulo = '$titulo', mensaje = '$mensaje', fechaExpiracion = '$fecha', dirigidoA = '$dirigidoA' WHERE idAnuncio = $idAnuncio";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function contarAnunciosQueEstanActivos() {
    $conexion = obtenerConexion();
    $hoy = date('Y-m-d');
    $resultado = mysqli_query($conexion, "SELECT COUNT(*) as total FROM anuncios WHERE fechaExpiracion >= '$hoy'");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function listarAnunciosConPaginas($cantidad) {
    $conexion = obtenerConexion();
    $hoy = date('Y-m-d');
    $resultado = mysqli_query($conexion, "SELECT * FROM anuncios WHERE fechaExpiracion >= '$hoy' ORDER BY idAnuncio DESC LIMIT $cantidad");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarAnunciosPorRol($rol) {
    $conexion = obtenerConexion();
    $hoy = date('Y-m-d');
    $sql = "SELECT * FROM anuncios WHERE fechaExpiracion >= '$hoy' AND (dirigidoA = '$rol' OR dirigidoA = 'todos') ORDER BY idAnuncio DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}
?>