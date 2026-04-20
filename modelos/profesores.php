<?php
require_once("conectar.php");

function listarProfesores() {
    $conexion = obtenerConexion();
    $sentenciaSql = "SELECT * FROM profesores ORDER BY idProfesor ASC";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $listaDeProfesores = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaDeProfesores[] = $fila;
    }
    mysqli_close($conexion);
    return $listaDeProfesores;
}

function insertarProfesor($nombre, $email, $telefono, $dni, $especialidad, $direccion) {
    $conexion = obtenerConexion();
    $sentenciaSql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, especialidad, direccionProfesor) 
            VALUES ('$nombre', '$email', '$telefono', '$dni', '$especialidad', '$direccion')";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarProfesor($idProfesor, $nombre, $email, $telefono, $dni, $especialidad, $direccion) {
    $conexion = obtenerConexion();
    $sentenciaSql = "UPDATE profesores SET nombreProfesor = '$nombre', emailProfesor = '$email', 
            telefonoProfesor = '$telefono', dniProfesor = '$dni', especialidad = '$especialidad', 
            direccionProfesor = '$direccion' WHERE idProfesor = $idProfesor";
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
    $sentenciaSql = "SELECT * FROM profesores WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sentenciaSql);
    $profesor = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $profesor;
}

function actualizarPerfilProfesor($id, $nombre, $email, $telefono) {
    $conexion = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor = '$nombre', emailProfesor = '$email', telefonoProfesor = '$telefono' WHERE idProfesor = $id";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>
