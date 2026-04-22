<?php
require_once("conectar.php");

function listarModulos() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT modulos.*, ciclos.nombreCiclo FROM modulos LEFT JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo ORDER BY idModulo ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarModulosPorCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM modulos WHERE idCiclo = $idCiclo");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarModulosPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT modulos.*, ciclos.nombreCiclo FROM modulos JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo WHERE modulos.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor) ORDER BY nombreModulo ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarModulo($nombre, $idCiclo, $horasMaximas) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) VALUES ('$nombre', $idCiclo, $horasMaximas)");
    mysqli_close($conexion);
    return $resultado;
}

function actualizarModulo($idModulo, $nombre, $idCiclo, $horasMaximas) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "UPDATE modulos SET nombreModulo = '$nombre', idCiclo = $idCiclo, horasMaximas = $horasMaximas WHERE idModulo = $idModulo");
    mysqli_close($conexion);
    return $resultado;
}

function eliminarModulo($idModulo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM modulos WHERE idModulo = $idModulo");
    mysqli_close($conexion);
    return $resultado;
}

function obtenerModuloPorId($idModulo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM modulos WHERE idModulo = $idModulo");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function obtenerHorasTotalesRetosModulo($idModulo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT SUM(retos.horasReto) as total FROM retos JOIN modulo_reto ON retos.idReto = modulo_reto.idReto WHERE modulo_reto.idModulo = $idModulo");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila['total'];
}
?>
