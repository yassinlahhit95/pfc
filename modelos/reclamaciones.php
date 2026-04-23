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
        $fila['asuntoReclamacion'] = $fila['asunto'];
        $fila['fechaReclamacion'] = $fila['fecha'];
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
    if ($fila) {
        $fila['asuntoReclamacion'] = $fila['asunto'];
        $fila['descripcionReclamacion'] = $fila['descripcion'];
        $fila['fechaReclamacion'] = $fila['fecha'];
    }
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
    
    // Si no se selecciona profesor, usamos NULL en la BD si la tabla lo permite, 
    // o un ID por defecto. Segun database.sql idProfesor permite NULL o tiene FK.
    $idProfesorSQL = empty($idProfesor) ? "NULL" : $idProfesor;
    
    $sql = "INSERT INTO reclamaciones (idEstudiante, idProfesor, asunto, descripcion, fecha, estadoReclamacion) 
            VALUES ($idEstudiante, $idProfesorSQL, '$asunto', '$descripcion', '$fecha', 'pendiente')";
    
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
        $fila['asuntoReclamacion'] = $fila['asunto'];
        $fila['fechaReclamacion'] = $fila['fecha'];
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
        $fila['asuntoReclamacion'] = $fila['asunto'];
        $fila['fechaReclamacion'] = $fila['fecha'];
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