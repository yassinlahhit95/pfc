<?php
require_once("conectar.php");

function listarRetos() {
    $conexion = obtenerConexion();
    $consulta = "SELECT * FROM retos ORDER BY idReto ASC";
    $resultado = mysqli_query($conexion, $consulta);
    $datos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $datos[] = $fila;
    }
    mysqli_close($conexion);
    return $datos;
}

function insertarReto($nombre, $fInicio, $fFin, $horas) {
    $conexion = obtenerConexion();
    $consulta = "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) 
            VALUES ('$nombre', '$fInicio', '$fFin', $horas)";
    if (mysqli_query($conexion, $consulta)) {
        $id = mysqli_insert_id($conexion);
        mysqli_close($conexion);
        return $id;
    }
    mysqli_close($conexion);
    return false;
}

function actualizarReto($id, $nombre, $fInicio, $fFin, $horas) {
    $conexion = obtenerConexion();
    $consulta = "UPDATE retos SET nombreReto = '$nombre', fechaInicio = '$fInicio', 
            fechaFin = '$fFin', horasReto = $horas WHERE idReto = $id";
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarReto($id) {
    $conexion = obtenerConexion();
    $consulta = "DELETE FROM retos WHERE idReto = $id";
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerRetoPorId($id) {
    $conexion = obtenerConexion();
    $consulta = "SELECT * FROM retos WHERE idReto = $id";
    $resultado = mysqli_query($conexion, $consulta);
    $dato = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $dato;
}

function obtenerDetallesReto($id) {
    $conexion = obtenerConexion();
    $consulta = "SELECT * FROM retos WHERE idReto = $id";
    $resultado = mysqli_query($conexion, $consulta);
    $dato = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $dato;
}

function asociarModuloReto($idModulo, $idReto) {
    $conexion = obtenerConexion();
    $consulta = "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($idModulo, $idReto)";
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function limpiarAsociacionesReto($idReto) {
    $conexion = obtenerConexion();
    $consulta = "DELETE FROM modulo_reto WHERE idReto = $idReto";
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerModulosDeReto($idReto) {
    $conexion = obtenerConexion();
    // Usamos JOIN simple en lugar de subconsultas difíciles
    $consulta = "SELECT modulos.*, ciclos.nombreCiclo 
            FROM modulos 
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo
            JOIN modulo_reto ON modulos.idModulo = modulo_reto.idModulo
            WHERE modulo_reto.idReto = $idReto";
            
    $resultado = mysqli_query($conexion, $consulta);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function calificarReto($idEstudiante, $idReto, $nota) {
    $conexion = obtenerConexion();
    $consultaBusqueda = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    $resultadoBusqueda = mysqli_query($conexion, $consultaBusqueda);
    
    if (mysqli_num_rows($resultadoBusqueda) > 0) {
        $consulta = "UPDATE calificaciones_retos SET nota = $nota WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    } else {
        $consulta = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES ($idEstudiante, $idReto, $nota)";
    }
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerCalificacion($idEstudiante, $idReto) {
    $conexion = obtenerConexion();
    $consulta = "SELECT nota FROM calificaciones_retos WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    $resultado = mysqli_query($conexion, $consulta);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    
    if ($fila) {
        return $fila['nota'];
    } else {
        return null;
    }
}
?>