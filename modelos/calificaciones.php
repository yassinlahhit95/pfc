<?php
require_once("conectar.php");

function obtenerNotasModulo($idEstudiante, $idModulo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function listarCalificacionesGeneral() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo FROM calificaciones_modulos JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo ORDER BY idEstudiante ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerCalificacionPorId($idCalificacion) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM calificaciones_modulos WHERE idCalificacion = $idCalificacion");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function eliminarCalificacion($idCalificacion) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM calificaciones_modulos WHERE idCalificacion = $idCalificacion");
    mysqli_close($conexion);
    return $resultado;
}

function calificarModuloCompleto($idEstudiante, $idModulo, $nota1eva, $nota1final, $nota2eva, $nota2final) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo");
    if(mysqli_num_rows($resultado) > 0) {
        $resultado = mysqli_query($conexion, "UPDATE calificaciones_modulos SET nota_1ev = '$nota1eva', nota_1final = '$nota1final', nota_2ev = '$nota2eva', nota_2final = '$nota2final' WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo");
    } else {
        $resultado = mysqli_query($conexion, "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final) VALUES ($idEstudiante, $idModulo, '$nota1eva', '$nota1final', '$nota2eva', '$nota2final')");
    }
    mysqli_close($conexion);
    return $resultado;
}

function listarCalificacionesPorEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT calificaciones_modulos.*, modulos.nombreModulo FROM calificaciones_modulos JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo WHERE idEstudiante = $idEstudiante");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarCalificacionesPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo FROM calificaciones_modulos JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo WHERE modulos.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor) ORDER BY estudiantes.nombreEstudiante ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}
?>
