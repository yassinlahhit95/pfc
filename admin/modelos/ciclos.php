<?php
require_once("conectar.php");

function listarTodosLosCiclos() {
    $conexion = obtenerConexion();
    $consulta = "SELECT ciclos.*, 
                (SELECT nombreNivel FROM niveles WHERE niveles.idNivel = ciclos.idNivel) as nombreNivel 
                 FROM ciclos 
                 ORDER BY idCiclo ASC";
    
    $resultado = mysqli_query($conexion, $consulta);
    $listaDeCiclos = [];
    
    if ($resultado) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $listaDeCiclos[] = $fila;
        }
    }
    
    mysqli_close($conexion);
    return $listaDeCiclos;
}

function insertarNuevoCiclo($nombre, $descripcion, $idNivel, $idEstado, $profesores = [], $aulas = []) {
    $conexion = obtenerConexion();
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $descripcion = mysqli_real_escape_string($conexion, $descripcion);

    $consulta = "INSERT INTO ciclos (nombreCiclo, descripcionCiclo, idNivel, idEstado) 
                 VALUES ('$nombre', '$descripcion', $idNivel, $idEstado)";
    
    if (mysqli_query($conexion, $consulta)) {
        $idCiclo = mysqli_insert_id($conexion);
        
        // Asociar profesores
        foreach ($profesores as $idProf) {
            $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idCiclo, $idProf)";
            mysqli_query($conexion, $sql);
        }

        // Asociar aulas
        foreach ($aulas as $idAula) {
            $sql = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($idCiclo, $idAula)";
            mysqli_query($conexion, $sql);
        }

        mysqli_close($conexion);
        return true;
    }

    mysqli_close($conexion);
    return false;
}

function actualizarCicloExistente($idCiclo, $nombre, $descripcion, $idNivel, $idEstado, $profesores = [], $aulas = []) {
    $conexion = obtenerConexion();
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $descripcion = mysqli_real_escape_string($conexion, $descripcion);

    $consulta = "UPDATE ciclos SET nombreCiclo = '$nombre', descripcionCiclo = '$descripcion', 
                 idNivel = $idNivel, idEstado = $idEstado WHERE idCiclo = $idCiclo";
    
    if (mysqli_query($conexion, $consulta)) {
        // Limpiar asociaciones viejas
        mysqli_query($conexion, "DELETE FROM ciclo_profesor WHERE idCiclo = $idCiclo");
        mysqli_query($conexion, "DELETE FROM ciclo_aula WHERE idCiclo = $idCiclo");

        // Asociar profesores nuevos
        foreach ($profesores as $idProf) {
            $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idCiclo, $idProf)";
            mysqli_query($conexion, $sql);
        }

        // Asociar aulas nuevas
        foreach ($aulas as $idAula) {
            $sql = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($idCiclo, $idAula)";
            mysqli_query($conexion, $sql);
        }

        mysqli_close($conexion);
        return true;
    }

    mysqli_close($conexion);
    return false;
}

function eliminarCicloPorId($idCiclo) {
    $conexion = obtenerConexion();
    $consulta = "DELETE FROM ciclos WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerCicloUnico($idCiclo) {
    $conexion = obtenerConexion();
    $consulta = "SELECT * FROM ciclos WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($conexion, $consulta);
    $datosDelCiclo = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $datosDelCiclo;
}

function comprobarNombreRepetido($nombre) {
    $conexion = obtenerConexion();
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $consulta = "SELECT idCiclo FROM ciclos WHERE nombreCiclo = '$nombre'";
    $resultado = mysqli_query($conexion, $consulta);
    $cantidad = mysqli_num_rows($resultado);
    mysqli_close($conexion);
    return ($cantidad > 0);
}

function comprobarNombreEnOtroCiclo($nombre, $idCiclo) {
    $conexion = obtenerConexion();
    $nombre = mysqli_real_escape_string($conexion, $nombre);
    $consulta = "SELECT idCiclo FROM ciclos WHERE nombreCiclo = '$nombre' AND idCiclo <> $idCiclo";
    $resultado = mysqli_query($conexion, $consulta);
    $cantidad = mysqli_num_rows($resultado);
    mysqli_close($conexion);
    return ($cantidad > 0);
}

function obtenerProfesoresDeUnCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT idProfesor FROM ciclo_profesor WHERE idCiclo = $idCiclo";
    $res = mysqli_query($conexion, $sql);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) { $lista[] = $f; }
    mysqli_close($conexion);
    return $lista;
}

function obtenerAulasDeUnCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT idAula FROM ciclo_aula WHERE idCiclo = $idCiclo";
    $res = mysqli_query($conexion, $sql);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) { $lista[] = $f; }
    mysqli_close($conexion);
    return $lista;
}
?>
