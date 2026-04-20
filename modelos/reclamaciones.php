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
    $reclamaciones = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $reclamaciones[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $reclamaciones;
}

function obtenerReclamacionPorId($id) {
    $conexion = obtenerConexion();
    $sql = "SELECT reclamaciones.*, estudiantes.nombreEstudiante, profesores.nombreProfesor 
            FROM reclamaciones 
            JOIN estudiantes ON reclamaciones.idEstudiante = estudiantes.idEstudiante
            LEFT JOIN profesores ON reclamaciones.idProfesor = profesores.idProfesor
            WHERE idReclamacion = $id";
    $resultado = mysqli_query($conexion, $sql);
    $reclamacion = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $reclamacion;
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
    $idProfVal = $idProfesor ? $idProfesor : "NULL";
    $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, asunto, descripcion, gravedad, fecha) 
            VALUES ($idEstudiante, $idProfVal, '$asunto', '$descripcion', '$gravedad', '$fecha')";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarReclamacionCompleta($id, $asunto, $descripcion, $gravedad, $fecha, $idEstudiante = null) {
    $conexion = obtenerConexion();
    $sql = "UPDATE reclamaciones SET asunto = '$asunto', descripcion = '$descripcion', gravedad = '$gravedad', fecha = '$fecha'";
    if ($idEstudiante) {
        $sql .= ", idEstudiante = $idEstudiante";
    }
    $sql .= " WHERE idReclamacion = $id";
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
    $reclamaciones = [];
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $reclamaciones[] = $fila;
        }
    }
    mysqli_close($conexion);
    return $reclamaciones;
}

function listarReclamacionesPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT reclamaciones.*, estudiantes.nombreEstudiante 
            FROM reclamaciones 
            JOIN estudiantes ON reclamaciones.idEstudiante = estudiantes.idEstudiante
            WHERE reclamaciones.idProfesor = $idProfesor
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