<?php
require_once("conectar.php");

function listarReclamaciones() {
    $conexion = obtenerConexion();
    $sql = "SELECT *, 
            (SELECT nombreEstudiante FROM estudiantes WHERE idEstudiante = reclamaciones.idEstudiante) as nombreEstudiante,
            (SELECT nombreProfesor FROM profesores WHERE idProfesor = reclamaciones.idProfesor) as nombreProfesor 
            FROM reclamaciones 
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
    $nuevoEstado = mysqli_real_escape_string($conexion, $nuevoEstado);
    $sql = "UPDATE reclamaciones SET estadoReclamacion = '$nuevoEstado' WHERE idReclamacion = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function insertarReclamacion($idEstudiante, $idProfesor, $asunto, $descripcion, $gravedad, $fecha) {
    $conexion = obtenerConexion();
    $asunto = mysqli_real_escape_string($conexion, $asunto);
    $descripcion = mysqli_real_escape_string($conexion, $descripcion);
    $gravedad = mysqli_real_escape_string($conexion, $gravedad);
    $fecha = mysqli_real_escape_string($conexion, $fecha);

    $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, asunto, descripcion, gravedad, fecha) 
            VALUES ($idEstudiante, $idProfesor, '$asunto', '$descripcion', '$gravedad', '$fecha')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarReclamacion($id) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM reclamaciones WHERE idReclamacion = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>
