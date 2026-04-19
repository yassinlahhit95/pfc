<?php
require_once("conectar.php");

function listarModulos() {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT *, (SELECT nombreCiclo FROM ciclos WHERE ciclos.idCiclo = modulos.idCiclo) as nombreCiclo 
            FROM modulos 
            ORDER BY idModulo ASC";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $listaDeModulos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeModulos[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeModulos;
}

function listarModulosPorCiclo($idDelCiclo) {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT * FROM modulos WHERE idCiclo = $idDelCiclo";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $listaDeModulos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeModulos[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeModulos;
}

function insertarModulo($nombre, $idDelCiclo, $horas) {
    $conexion = obtenerConexion();
    $sentenciaSql = "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) VALUES ('$nombre', $idDelCiclo, $horas)";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarModulo($idDelModulo, $nombre, $idDelCiclo, $horas) {
    $conexion = obtenerConexion();
    $sentenciaSql = "UPDATE modulos SET nombreModulo = '$nombre', idCiclo = $idDelCiclo, horasMaximas = $horas WHERE idModulo = $idDelModulo";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarModulo($idDelModulo) {
    $conexion = obtenerConexion();
    $sentenciaSql = "DELETE FROM modulos WHERE idModulo = $idDelModulo";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerModuloPorId($idDelModulo) {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT * FROM modulos WHERE idModulo = $idDelModulo";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $modulo = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $modulo;
}

function obtenerHorasTotalesRetosModulo($idDelModulo) {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT (SELECT SUM(horasReto) FROM retos WHERE idReto IN (SELECT idReto FROM modulo_reto WHERE idModulo = $idDelModulo)) as total";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    
    return $fila['total'] ?? 0;
}
?>
