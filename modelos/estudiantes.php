<?php
require_once("conectar.php");

function listarEstudiantes() {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
            LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo ORDER BY idEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) 
            VALUES ('$nombre', '$email', '$telefono', '$fechaNacimiento', '$dni', '$fechaAlta', '$direccion', '$ciudad', '$codigoPostal', '$observaciones', $idCiclo)";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    $conexion = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$email', telefonoEstudiante = '$telefono', fechaNacimientoEstudiante = '$fechaNacimiento', dniEstudiante = '$dni', fechaAltaEstudiante = '$fechaAlta', direccionEstudiante = '$direccion', ciudadEstudiante = '$ciudad', codigoPostalEstudiante = '$codigoPostal', observacionesEstudiante = '$observaciones', idCiclo = $idCiclo 
            WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function listarEstudiantesPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor) 
            ORDER BY nombreEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarEstudiantesPorCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
            LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.idCiclo = $idCiclo ORDER BY idEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function eliminarEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM estudiantes WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerEstudiantePorId($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
            LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono) {
    $conexion = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$email', telefonoEstudiante = '$telefono' 
            WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>