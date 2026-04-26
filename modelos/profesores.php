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

function insertarProfesor($nombre, $email, $telefono, $dni, $direccion, $especialidad = "", $fechaNacimiento = '1980-01-01', $fechaAlta = '2026-01-01', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, direccionProfesor, especialidad, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor)
            VALUES ('$nombre', '$email', '$telefono', '$dni', '$direccion', '$especialidad', '$fechaNacimiento', '$fechaAlta', '$ciudad', '$codigoPostal', '$observaciones')";
    if (mysqli_query($conexion, $sql)) {
        $idProfesor = mysqli_insert_id($conexion);
        mysqli_close($conexion);
        return $idProfesor;
    }
    mysqli_close($conexion);
    return false;
}

function actualizarProfesor($idProfesor, $nombre, $email, $telefono, $dni, $direccion, $especialidad = "", $fechaNacimiento = '1980-01-01', $fechaAlta = '2026-01-01', $ciudad = '', $codigoPostal = '', $observaciones = '') {
    $conexion = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor = '$nombre', emailProfesor = '$email',
            telefonoProfesor = '$telefono', dniProfesor = '$dni',
            direccionProfesor = '$direccion', especialidad = '$especialidad',
            fechaNacimientoProfesor = '$fechaNacimiento', fechaAltaProfesor = '$fechaAlta',
            ciudadProfesor = '$ciudad', codigoPostalProfesor = '$codigoPostal', 
            observacionesProfesor = '$observaciones'
            WHERE idProfesor = $idProfesor";
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

function listarProfesoresPorCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT p.* FROM profesores p 
            JOIN ciclo_profesor cp ON p.idProfesor = cp.idProfesor 
            WHERE cp.idCiclo = $idCiclo 
            ORDER BY p.nombreProfesor ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerIdsModulosDeProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT idModulo FROM profesor_modulo WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    $ids = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $ids[] = $fila['idModulo'];
    }
    mysqli_close($conexion);
    return $ids;
}

function limpiarModulosProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM profesor_modulo WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarPerfilProfesor($idProfesor, $nombre, $email, $telefono) {
    $conexion = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor = '$nombre', emailProfesor = '$email', telefonoProfesor = '$telefono' WHERE idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>