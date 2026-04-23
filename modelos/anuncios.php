<?php
require_once("conectar.php");

function listarTodosLosAnuncios() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM anuncios ORDER BY idAnuncio DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $fila['tituloAnuncio'] = $fila['titulo'];
        $fila['contenidoAnuncio'] = $fila['mensaje'];
        $fila['fechaAnuncio'] = $fila['fechaExpiracion']; 
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarAnuncio($titulo, $mensaje) {
    $conexion = obtenerConexion();
    $fechaExpiracion = date('Y-m-d', strtotime('+1 month'));
    $sql = "INSERT INTO anuncios (titulo, mensaje, fechaExpiracion, dirigidoA) 
            VALUES ('$titulo', '$mensaje', '$fechaExpiracion', 'todos')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarAnuncio($idAnuncio) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM anuncios WHERE idAnuncio = $idAnuncio";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerAnuncioPorId($idAnuncio) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM anuncios WHERE idAnuncio = $idAnuncio";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    if ($fila) {
        $fila['tituloAnuncio'] = $fila['titulo'];
        $fila['contenidoAnuncio'] = $fila['mensaje'];
        $fila['fechaAnuncio'] = $fila['fechaExpiracion'];
    }
    mysqli_close($conexion);
    return $fila;
}

function actualizarAnuncio($idAnuncio, $titulo, $mensaje, $fecha, $dirigidoA) {
    $conexion = obtenerConexion();
    $sql = "UPDATE anuncios SET titulo = '$titulo', mensaje = '$mensaje', fechaExpiracion = '$fecha', dirigidoA = '$dirigidoA' 
            WHERE idAnuncio = $idAnuncio";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function contarAnunciosQueEstanActivos() {
    $conexion = obtenerConexion();
    $hoy = date('Y-m-d');
    $sql = "SELECT COUNT(*) as total FROM anuncios WHERE fechaExpiracion >= '$hoy'";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}

function listarAnunciosConPaginas($cantidad) {
    $conexion = obtenerConexion();
    $hoy = date('Y-m-d');
    $sql = "SELECT * FROM anuncios WHERE fechaExpiracion >= '$hoy' ORDER BY idAnuncio DESC LIMIT $cantidad";
    $resultado = mysqli_query($conexion, $sql);
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
    $sql = "SELECT * FROM anuncios WHERE fechaExpiracion >= '$hoy' AND (dirigidoA = '$rol' OR dirigidoA = 'todos') 
            ORDER BY idAnuncio DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}
?>