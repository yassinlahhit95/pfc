<?php
require_once("conectar.php");

function listarModulos() {
    $conexion = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo 
            FROM modulos 
            LEFT JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            ORDER BY idModulo ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerModulosDeProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.abreviaturaCiclo FROM modulos 
            JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo 
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo
            WHERE profesor_modulo.idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarModulosPorCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarModulosPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo 
            FROM modulos 
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            WHERE modulos.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor) 
            ORDER BY nombreModulo ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarModulo($nombreModulo, $idCiclo, $horasMaximas) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) 
            VALUES ('$nombreModulo', $idCiclo, $horasMaximas)";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarModulo($idModulo, $nombreModulo, $idCiclo, $horasMaximas) {
    $conexion = obtenerConexion();
    $sql = "UPDATE modulos SET nombreModulo = '$nombreModulo', idCiclo = $idCiclo, horasMaximas = $horasMaximas 
            WHERE idModulo = $idModulo";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarModulo($idModulo) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM modulos WHERE idModulo = $idModulo";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerModuloPorId($idModulo) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idModulo = $idModulo";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function obtenerModulosPorCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerHorasTotalesRetosModulo($idModulo) {
    $conexion = obtenerConexion();
    $sql = "SELECT SUM(retos.horasReto) as total FROM retos 
            JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
            WHERE modulo_reto.idModulo = $idModulo";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    $total = 0;
    if ($fila['total'] > 0) { $total = $fila['total']; }
    return $total;
}
?>