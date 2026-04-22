<?php
require_once("conectar.php");

function listarProfesores() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM profesores ORDER BY idProfesor ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarProfesor($nombre, $email, $telefono, $dni, $direccion) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, direccionProfesor) 
            VALUES ('$nombre', '$email', '$telefono', '$dni', '$direccion')";
    if (mysqli_query($conexion, $sql)) {
        $idProfesor = mysqli_insert_id($conexion);
        mysqli_close($conexion);
        return $idProfesor;
    }
    mysqli_close($conexion);
    return false;
}

function actualizarProfesor($idProfesor, $nombre, $email, $telefono, $dni, $direccion) {
    $conexion = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor = '$nombre', emailProfesor = '$email', 
            telefonoProfesor = '$telefono', dniProfesor = '$dni', 
            direccionProfesor = '$direccion' WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function asociarCicloProfesor($idCiclo, $idProfesor) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idCiclo, $idProfesor)");
    mysqli_close($conexion);
    return $resultado;
}

function asociarModuloProfesor($idModulo, $idProfesor) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "INSERT INTO profesor_modulo (idModulo, idProfesor) VALUES ($idModulo, $idProfesor)");
    mysqli_close($conexion);
    return $resultado;
}

function eliminarProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM profesores WHERE idProfesor = $idProfesor");
    mysqli_close($conexion);
    return $resultado;
}

function obtenerProfesorPorId($idProfesor) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM profesores WHERE idProfesor = $idProfesor");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function actualizarPerfilProfesor($idProfesor, $nombre, $email, $telefono) {
    $conexion = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor = '$nombre', emailProfesor = '$email', telefonoProfesor = '$telefono' WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>