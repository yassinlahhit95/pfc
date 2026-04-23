<?php
require_once("conectar.php");

function listarRetos() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM retos ORDER BY idReto ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerRetosDeProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT DISTINCT retos.* FROM retos 
            JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
            JOIN profesor_modulo ON modulo_reto.idModulo = profesor_modulo.idModulo 
            WHERE profesor_modulo.idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarReto($nombreReto, $fechaInicio, $fechaFin, $horasReto) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) 
            VALUES ('$nombreReto', '$fechaInicio', '$fechaFin', $horasReto)";
    if (mysqli_query($conexion, $sql)) {
        $idReto = mysqli_insert_id($conexion);
        mysqli_close($conexion);
        return $idReto;
    }
    mysqli_close($conexion);
    return false;
}

function actualizarReto($idReto, $nombreReto, $fechaInicio, $fechaFin, $horasReto) {
    $conexion = obtenerConexion();
    $sql = "UPDATE retos SET nombreReto = '$nombreReto', fechaInicio = '$fechaInicio', 
            fechaFin = '$fechaFin', horasReto = $horasReto WHERE idReto = $idReto";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarReto($idReto) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM retos WHERE idReto = $idReto");
    mysqli_close($conexion);
    return $resultado;
}

function obtenerRetoPorId($idReto) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM retos WHERE idReto = $idReto";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function asociarModuloReto($idModulo, $idReto) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($idModulo, $idReto)");
    mysqli_close($conexion);
    return $resultado;
}

function limpiarAsociacionesReto($idReto) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM modulo_reto WHERE idReto = $idReto");
    mysqli_close($conexion);
    return $resultado;
}

function obtenerModulosDeReto($idReto) {
    $conexion = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo FROM modulos JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo JOIN modulo_reto ON modulos.idModulo = modulo_reto.idModulo WHERE modulo_reto.idReto = $idReto";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function calificarReto($idEstudiante, $idReto, $nota) {
    $conexion = obtenerConexion();
    $sqlBusqueda = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    $resultadoBusqueda = mysqli_query($conexion, $sqlBusqueda);
    if (mysqli_num_rows($resultadoBusqueda) > 0) {
        $sql = "UPDATE calificaciones_retos SET nota = $nota WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    } else {
        $sql = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES ($idEstudiante, $idReto, $nota)";
    }
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerCalificacion($idEstudiante, $idReto) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT nota FROM calificaciones_retos WHERE idEstudiante = $idEstudiante AND idReto = $idReto");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    $nota = "";
    if ($fila) { $nota = $fila['nota']; }
    return $nota;
}
?>