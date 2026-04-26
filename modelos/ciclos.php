<?php
require_once("conectar.php");

function listarTodosLosCiclos() {
    $conexion = obtenerConexion();
    $sql = "SELECT ciclos.*, niveles.nombreNivel FROM ciclos 
            LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel ORDER BY idCiclo ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = array();
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerCiclosDeProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT ciclos.*, niveles.nombreNivel FROM ciclos 
            JOIN ciclo_profesor ON ciclos.idCiclo = ciclo_profesor.idCiclo 
            LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel
            WHERE ciclo_profesor.idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    $lista = array();
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarNuevoCiclo($nombre, $abreviatura, $idNivel, $profesores, $aulas, $precio = 1000.00) {
    $conexion = obtenerConexion();
    // descripcionCiclo se inserta como vacío ya que se eliminó del formulario
    $sql = "INSERT INTO ciclos (nombreCiclo, abreviaturaCiclo, descripcionCiclo, idNivel, precioCiclo) 
            VALUES ('$nombre', '$abreviatura', '', $idNivel, $precio)";
    if (mysqli_query($conexion, $sql)) {
        $idCiclo = mysqli_insert_id($conexion);
        foreach ($profesores as $idProf) {
            $sqlProf = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idCiclo, $idProf)";
            mysqli_query($conexion, $sqlProf);
        }
        foreach ($aulas as $idAula) {
            $sqlAula = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($idCiclo, $idAula)";
            mysqli_query($conexion, $sqlAula);
        }
        mysqli_close($conexion);
        return true;
    }
    mysqli_close($conexion);
    return false;
}

function actualizarCicloExistente($id, $nombre, $abreviatura, $idNivel, $profesores, $aulas, $precio = 1000.00) {
    $conexion = obtenerConexion();
    // descripcionCiclo no se actualiza (se mantiene o se deja vacío)
    $sql = "UPDATE ciclos SET nombreCiclo = '$nombre', abreviaturaCiclo = '$abreviatura', idNivel = $idNivel, precioCiclo = $precio 
            WHERE idCiclo = $id";
    if (mysqli_query($conexion, $sql)) {
        $sqlDelProf = "DELETE FROM ciclo_profesor WHERE idCiclo = $id";
        mysqli_query($conexion, $sqlDelProf);
        foreach ($profesores as $idProf) {
            $sqlInsProf = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($id, $idProf)";
            mysqli_query($conexion, $sqlInsProf);
        }
        $sqlDelAula = "DELETE FROM ciclo_aula WHERE idCiclo = $id";
        mysqli_query($conexion, $sqlDelAula);
        foreach ($aulas as $idAula) {
            $sqlInsAula = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($id, $idAula)";
            mysqli_query($conexion, $sqlInsAula);
        }
        mysqli_close($conexion);
        return true;
    }
    mysqli_close($conexion);
    return false;
}

function eliminarCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM ciclos WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerCicloPorId($id) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM ciclos WHERE idCiclo = $id";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function obtenerProfesoresDeUnCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT idProfesor FROM ciclo_profesor WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($conexion, $sql);
    $lista = array();
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($conexion);
    return $lista;
}

function obtenerAulasDeUnCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT idAula FROM ciclo_aula WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($conexion, $sql);
    $lista = array();
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