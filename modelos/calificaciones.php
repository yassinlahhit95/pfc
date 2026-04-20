<?php
require_once("conectar.php");

function obtenerNotasModulo($idEstudiante, $idModulo) {
    $conexion = obtenerConexion();
    $consulta = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    $resultado = mysqli_query($conexion, $consulta);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function listarCalificacionesGeneral() {
    $conexion = obtenerConexion();
    $sql = "SELECT *, 
            (SELECT nombreEstudiante FROM estudiantes WHERE idEstudiante = calificaciones_modulos.idEstudiante) as nombreEstudiante,
            (SELECT nombreModulo FROM modulos WHERE idModulo = calificaciones_modulos.idModulo) as nombreModulo
            FROM calificaciones_modulos 
            ORDER BY idEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerCalificacionPorId($id) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idCalificacion = $id";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function eliminarCalificacionModulo($id) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM calificaciones_modulos WHERE idCalificacion = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function calificarModuloCompleto($idEstudiante, $idModulo, $n1ev, $n1f, $n2ev, $n2f) {
    $conexion = obtenerConexion();
    $consultaBusqueda = "SELECT idCalificacion FROM calificaciones_modulos WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    $resultadoBusqueda = mysqli_query($conexion, $consultaBusqueda);
    
    if (mysqli_num_rows($resultadoBusqueda) > 0) {
        $consulta = "UPDATE calificaciones_modulos 
                SET nota_1ev = '$n1ev', nota_1final = '$n1f', nota_2ev = '$n2ev', nota_2final = '$n2f' 
                WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    } else {
        $consulta = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final) 
                VALUES ($idEstudiante, $idModulo, '$n1ev', '$n1f', '$n2ev', '$n2f')";
    }
    
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}
?>