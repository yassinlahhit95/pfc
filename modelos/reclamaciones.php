<?php
require_once("conectar.php");

function listarReclamaciones() {
    $conexion = obtenerConexion();
    $sql = "SELECT reclamaciones.*, estudiantes.nombreEstudiante, profesores.nombreProfesor 
            FROM reclamaciones 
            JOIN estudiantes ON reclamaciones.idEstudiante = estudiantes.idEstudiante
            LEFT JOIN profesores ON reclamaciones.idProfesor = profesores.idProfesor
            ORDER BY idReclamacion DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerReclamacionPorId($idReclamacion) {
    $conexion = obtenerConexion();
    $sql = "SELECT reclamaciones.*, estudiantes.nombreEstudiante, profesores.nombreProfesor 
            FROM reclamaciones 
            JOIN estudiantes ON reclamaciones.idEstudiante = estudiantes.idEstudiante
            LEFT JOIN profesores ON reclamaciones.idProfesor = profesores.idProfesor
            WHERE idReclamacion = $idReclamacion";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function cambiarEstadoReclamacion($idReclamacion, $nuevoEstado) {
    $conexion = obtenerConexion();
    $sql = "UPDATE reclamaciones SET estadoReclamacion = '$nuevoEstado' WHERE idReclamacion = $idReclamacion";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarReclamacion($idReclamacion, $asunto, $descripcion, $estado) {
    $conexion = obtenerConexion();
    $sql = "UPDATE reclamaciones SET asunto = '$asunto', descripcion = '$descripcion', estadoReclamacion = '$estado' WHERE idReclamacion = $idReclamacion";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function insertarReclamacion($idEstudiante, $idProfesor, $asunto, $descripcion, $fecha) {
    $conexion = obtenerConexion();
    $idProfVal = $idProfesor ? $idProfesor : "NULL";
    $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, asunto, descripcion, fecha) 
            VALUES ($idEstudiante, $idProfVal, '$asunto', '$descripcion', '$fecha')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function listarReclamacionesPorEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "SELECT reclamaciones.*, profesores.nombreProfesor 
            FROM reclamaciones 
            LEFT JOIN profesores ON reclamaciones.idProfesor = profesores.idProfesor
            WHERE reclamaciones.idEstudiante = $idEstudiante
            ORDER BY idReclamacion DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarReclamacionesPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT reclamaciones.*, estudiantes.nombreEstudiante 
            FROM reclamaciones 
            JOIN estudiantes ON reclamaciones.idEstudiante = estudiantes.idEstudiante
            WHERE reclamaciones.idProfesor = $idProfesor
            ORDER BY idReclamacion DESC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function eliminarReclamacion($idReclamacion) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM reclamaciones WHERE idReclamacion = $idReclamacion");
    mysqli_close($conexion);
    return $resultado;
}
?>