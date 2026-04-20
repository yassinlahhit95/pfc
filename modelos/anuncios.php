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

function insertarAnuncio($titulo, $mensaje, $fecha) {
    $conexion = obtenerConexion();
    $consulta = "INSERT INTO anuncios (titulo, mensaje, fechaExpiracion) VALUES ('$titulo', '$mensaje', '$fecha')";
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function borrarAnuncio($id) {
    $conexion = obtenerConexion();
    $consulta = "DELETE FROM anuncios WHERE idAnuncio = $id";
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function contarAnunciosQueEstanActivos() {
    $conexion = obtenerConexion();
    $consulta = "SELECT COUNT(*) as total FROM anuncios WHERE fechaExpiracion >= CURDATE()";
    $resultado = mysqli_query($conexion, $consulta);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    
    if (isset($fila['total'])) {
        return $fila['total'];
    } else {
        return 0;
    }
}

function listarAnunciosConPaginas($cantidad) {
    $conexion = obtenerConexion();
    $consulta = "SELECT * FROM anuncios WHERE fechaExpiracion >= CURDATE() ORDER BY idAnuncio DESC LIMIT $cantidad";
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