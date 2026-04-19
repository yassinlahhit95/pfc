<?php
require_once("conectar.php");

function listarProfesores() {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT *, (SELECT nombreEstado FROM estados WHERE estados.idEstado = profesores.idEstado) as nombreEstado 
            FROM profesores 
            ORDER BY idProfesor ASC";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $listaDeProfesores = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeProfesores[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeProfesores;
}

function insertarProfesor($nombre, $email, $telefono, $dni, $especialidad, $direccion, $idEstado) {
    $conexion = obtenerConexion();
    $sentenciaSql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, especialidad, direccionProfesor, idEstado) 
            VALUES ('$nombre', '$email', '$telefono', '$dni', '$especialidad', '$direccion', $idEstado)";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarProfesor($idProfesor, $nombre, $email, $telefono, $dni, $especialidad, $direccion, $idEstado) {
    $conexion = obtenerConexion();
    $sentenciaSql = "UPDATE profesores SET nombreProfesor = '$nombre', emailProfesor = '$email', 
            telefonoProfesor = '$telefono', dniProfesor = '$dni', especialidad = '$especialidad', 
            direccionProfesor = '$direccion', idEstado = $idEstado WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function eliminarProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sentenciaSql = "DELETE FROM profesores WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerProfesorPorId($idProfesor) {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT *, (SELECT nombreEstado FROM estados WHERE estados.idEstado = profesores.idEstado) as nombreEstado 
            FROM profesores 
            WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $profesor = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $profesor;
}
?>
