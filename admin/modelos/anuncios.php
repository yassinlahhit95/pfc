<?php
require_once("conectar.php");

function listarTodosLosAnuncios() {
    $conexion = obtenerConexion();
    $consulta = "SELECT * FROM anuncios ORDER BY idAnuncio DESC";
    $listaDeAnuncios = [];
    $resultado = mysqli_query($conexion, $consulta);
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $listaDeAnuncios[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $listaDeAnuncios;
}

function insertarNuevoAnuncio($titulo, $mensaje, $fecha) {
    $conexion = obtenerConexion();
    $titulo = mysqli_real_escape_string($conexion, $titulo);
    $mensaje = mysqli_real_escape_string($conexion, $mensaje);
    $fecha = mysqli_real_escape_string($conexion, $fecha);
    $consulta = "INSERT INTO anuncios (titulo, mensaje, fechaExpiracion) VALUES ('$titulo', '$mensaje', '$fecha')";
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function borrarAnuncioPorId($idAnuncio) {
    $conexion = obtenerConexion();
    $idAnuncio = (int)$idAnuncio;
    $consulta = "DELETE FROM anuncios WHERE idAnuncio = $idAnuncio";
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerAnuncioPorId($idAnuncio) {
    $conexion = obtenerConexion();
    $idAnuncio = (int)$idAnuncio;
    $consulta = "SELECT * FROM anuncios WHERE idAnuncio = $idAnuncio";
    $resultado = mysqli_query($conexion, $consulta);
    $datosDelAnuncio = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $datosDelAnuncio;
}

function contarAnunciosQueEstanActivos() {
    $conexion = obtenerConexion();
    $hoy = date("Y-m-d");
    $consulta = "SELECT COUNT(*) as total FROM anuncios WHERE fechaExpiracion >= '$hoy'";
    $resultado = mysqli_query($conexion, $consulta);
    $fila = mysqli_fetch_assoc($resultado);
    $total = 0;
    if ($fila) {
        $total = $fila['total'];
    }
    mysqli_close($conexion);
    return $total;
}

function listarAnunciosConPaginas($limite) {
    $conexion = obtenerConexion();
    $hoy = date("Y-m-d");
    $consulta = "SELECT * FROM anuncios WHERE fechaExpiracion >= '$hoy' ORDER BY idAnuncio DESC LIMIT $limite";
    $resultado = mysqli_query($conexion, $consulta);
    $listaPaginada = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $listaPaginada[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $listaPaginada;
}
?>
