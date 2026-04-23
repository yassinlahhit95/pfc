<?php
require_once("conectar.php");

function obtenerNotasModulo($idEstudiante, $idModulo) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function listarCalificacionesGeneral() {
    $conexion = obtenerConexion();
    $sql = "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo 
            FROM calificaciones_modulos 
            JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante 
            JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo 
            ORDER BY idEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerCalificacionPorId($idCalificacion) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idCalificacion = $idCalificacion";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function eliminarCalificacion($idCalificacion) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM calificaciones_modulos WHERE idCalificacion = $idCalificacion";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function calificarModuloCompleto($idEstudiante, $idModulo, $nota1eva, $nota1final, $nota2eva, $nota2final) {
    $conexion = obtenerConexion();
    $sqlCheck = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    $resultadoCheck = mysqli_query($conexion, $sqlCheck);
    
    if(mysqli_num_rows($resultadoCheck) > 0) {
        $sql = "UPDATE calificaciones_modulos SET 
                nota_1ev = '$nota1eva', nota_1final = '$nota1final', 
                nota_2ev = '$nota2eva', nota_2final = '$nota2final' 
                WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    } else {
        $sql = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final) 
                VALUES ($idEstudiante, $idModulo, '$nota1eva', '$nota1final', '$nota2eva', '$nota2final')";
    }
    
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function listarCalificacionesPorEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "SELECT calificaciones_modulos.*, modulos.nombreModulo 
            FROM calificaciones_modulos 
            JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo 
            WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarCalificacionesPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo 
            FROM calificaciones_modulos 
            JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante 
            JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo 
            WHERE modulos.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor) 
            ORDER BY estudiantes.nombreEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarCalificacionesPorModulo($idModulo) {
    $conexion = obtenerConexion();
    $sql = "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, estudiantes.idEstudiante 
            FROM calificaciones_modulos 
            JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante 
            WHERE calificaciones_modulos.idModulo = $idModulo 
            ORDER BY estudiantes.nombreEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}
?>