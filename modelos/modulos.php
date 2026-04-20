<?php
require_once("conectar.php");

function listarModulos() {
    $conexion = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo 
            FROM modulos 
            LEFT JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            ORDER BY idModulo ASC";
    $resultado = mysqli_query($conexion, $sql);
    $listaDeModulos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeModulos[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeModulos;
}

function listarModulosPorCiclo($idDelCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idCiclo = $idDelCiclo";
    $resultado = mysqli_query($conexion, $sql);
    $listaDeModulos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeModulos[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeModulos;
}

function listarModulosPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo 
            FROM modulos 
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            WHERE modulos.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor)
            ORDER BY nombreModulo ASC";
    $resultado = mysqli_query($conexion, $sql);
    $listaDeModulos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeModulos[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeModulos;
}

function insertarModulo($nombre, $idDelCiclo, $horas) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) VALUES ('$nombre', $idDelCiclo, $horas)";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarModulo($idDelModulo, $nombre, $idDelCiclo, $horas) {
    $conexion = obtenerConexion();
    $sql = "UPDATE modulos SET nombreModulo = '$nombre', idCiclo = $idDelCiclo, horasMaximas = $horas WHERE idModulo = $idDelModulo";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarModulo($idDelModulo) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM modulos WHERE idModulo = $idDelModulo";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerModuloPorId($idDelModulo) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idModulo = $idDelModulo";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $modulo = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $modulo;
}

function obtenerHorasTotalesRetosModulo($idModulo) {
    $conexion = obtenerConexion();
    // Simplificamos la consulta para evitar funciones complejas dentro si es posible
    $sql = "SELECT SUM(retos.horasReto) as total 
            FROM retos 
            JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
            WHERE modulo_reto.idModulo = $idModulo";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    
    if (isset($fila['total'])) {
        return $fila['total'];
    } else {
        return 0;
    }
}
?>