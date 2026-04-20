<?php
require_once("conectar.php");

function listarReclamaciones() {
    $conexion = obtenerConexion();
    $sql = "SELECT reclamaciones.*, estudiantes.nombreEstudiante, profesores.nombreProfesor 
            FROM reclamaciones 
            JOIN estudiantes ON reclamaciones.idEstudiante = estudiantes.idEstudiante
            JOIN profesores ON reclamaciones.idProfesor = profesores.idProfesor
            ORDER BY idReclamacion DESC";
    $resultado = mysqli_query($conexion, $sql);
    $reclamaciones = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $reclamaciones[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $reclamaciones;
}

function cambiarEstadoReclamacion($id, $nuevoEstado) {
    $conexion = obtenerConexion();
    $sql = "UPDATE reclamaciones SET estadoReclamacion = '$nuevoEstado' WHERE idReclamacion = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function insertarReclamacion($idEstudiante, $idProfesor, $asunto, $descripcion, $gravedad, $fecha) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, asunto, descripcion, gravedad, fecha) 
            VALUES ($idEstudiante, $idProfesor, '$asunto', '$descripcion', '$gravedad', '$fecha')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function listarReclamacionesPorEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "SELECT reclamaciones.*, profesores.nombreProfesor 
            FROM reclamaciones 
            JOIN profesores ON reclamaciones.idProfesor = profesores.idProfesor
            WHERE reclamaciones.idEstudiante = $idEstudiante
            ORDER BY idReclamacion DESC";
    $resultado = mysqli_query($conexion, $sql);
    $reclamaciones = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $reclamaciones[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $reclamaciones;
}

function eliminarReclamacion($id) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM reclamaciones WHERE idReclamacion = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>