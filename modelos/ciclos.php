<?php
require_once("conectar.php");

function listarTodosLosCiclos() {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT ciclos.*, niveles.nombreNivel FROM ciclos LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel ORDER BY idCiclo ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerCiclosDeProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT ciclos.* FROM ciclos 
            JOIN ciclo_profesor ON ciclos.idCiclo = ciclo_profesor.idCiclo 
            WHERE ciclo_profesor.idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarNuevoCiclo($nombre, $descripcion, $idNivel, $profesores, $aulas) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO ciclos (nombreCiclo, descripcionCiclo, idNivel) VALUES ('$nombre', '$descripcion', $idNivel)";
    if (mysqli_query($conexion, $sql)) {
        $idCiclo = mysqli_insert_id($conexion);
        foreach ($profesores as $idProf) {
            mysqli_query($conexion, "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idCiclo, $idProf)");
        }
        foreach ($aulas as $idAula) {
            mysqli_query($conexion, "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($idCiclo, $idAula)");
        }
        mysqli_close($conexion);
        return true;
    }
    mysqli_close($conexion);
    return false;
}

function actualizarCicloExistente($id, $nombre, $descripcion, $idNivel, $profesores, $aulas) {
    $conexion = obtenerConexion();
    $sql = "UPDATE ciclos SET nombreCiclo = '$nombre', descripcionCiclo = '$descripcion', idNivel = $idNivel WHERE idCiclo = $id";
    if (mysqli_query($conexion, $sql)) {
        mysqli_query($conexion, "DELETE FROM ciclo_profesor WHERE idCiclo = $id");
        foreach ($profesores as $idProf) {
            mysqli_query($conexion, "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($id, $idProf)");
        }
        mysqli_query($conexion, "DELETE FROM ciclo_aula WHERE idCiclo = $id");
        foreach ($aulas as $idAula) {
            mysqli_query($conexion, "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($id, $idAula)");
        }
        mysqli_close($conexion);
        return true;
    }
    mysqli_close($conexion);
    return false;
}

function eliminarCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "DELETE FROM ciclos WHERE idCiclo = $idCiclo");
    mysqli_close($conexion);
    return $resultado;
}

function obtenerCicloUnico($idCiclo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT * FROM ciclos WHERE idCiclo = $idCiclo");
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function obtenerProfesoresDeUnCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT idProfesor FROM ciclo_profesor WHERE idCiclo = $idCiclo");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($conexion);
    return $lista;
}

function obtenerAulasDeUnCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $resultado = mysqli_query($conexion, "SELECT idAula FROM ciclo_aula WHERE idCiclo = $idCiclo");
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($conexion);
    return $lista;
}

function comprobarNombreEnOtroCiclo($nombre, $id) {
    $conexion = obtenerConexion();
    $sql = "SELECT idCiclo FROM ciclos WHERE nombreCiclo = '$nombre' AND idCiclo != $id";
    $resultado = mysqli_query($conexion, $sql);
    $num = mysqli_num_rows($resultado);
    mysqli_close($conexion);
    return $num > 0;
}
?>