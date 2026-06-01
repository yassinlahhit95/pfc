<?php
require_once __DIR__ . "/conectar.php";

// devuelve todos los ciclos con su nivel (medio o superior)
function listarTodosLosCiclos() {
    $con = obtenerConexion();
    $sql = "SELECT ciclos.*, niveles.nombreNivel
            FROM ciclos
            JOIN niveles ON ciclos.idNivel = niveles.idNivel
            ORDER BY ciclos.idCiclo ASC";

    $resultado = mysqli_query($con, $sql);
    $listaCiclos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaCiclos[] = $fila;
    }
    
    return $listaCiclos;
}

function listarCiclosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT c.*, n.nombreNivel
            FROM ciclos c
            JOIN niveles n ON c.idNivel = n.idNivel
            WHERE c.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
               OR c.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaCiclos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaCiclos[] = $fila;
    }
    
    return $listaCiclos;
}

function checkCicloExistente($nombreCiclo, $abreviaturaCiclo, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idCiclo FROM ciclos WHERE (nombreCiclo = ? OR abreviaturaCiclo = ?) AND idCiclo != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $nombreCiclo, $abreviaturaCiclo, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    
    return $existe;
}

function insertarNuevoCiclo($nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $precioCiclo) {
    $con = obtenerConexion();

    $sql = "INSERT INTO ciclos (nombreCiclo, abreviaturaCiclo, idNivel, precioCiclo) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssid", $nombreCiclo, $abreviaturaCiclo, $idNivel, $precioCiclo);
    $resultado = mysqli_stmt_execute($stmt);

    $idNuevoCiclo = mysqli_insert_id($con);

    $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    foreach ($listaIdsProfesores as $idProfesor) {
        mysqli_stmt_bind_param($stmt, "ii", $idNuevoCiclo, $idProfesor);
        $resultado = mysqli_stmt_execute($stmt);
    }

    
    return $resultado;
}

function actualizarCicloExistente($idCiclo, $nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $precioCiclo) {
    $con = obtenerConexion();

    $sql = "UPDATE ciclos SET nombreCiclo=?, abreviaturaCiclo=?, idNivel=?, precioCiclo=? WHERE idCiclo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssidi", $nombreCiclo, $abreviaturaCiclo, $idNivel, $precioCiclo, $idCiclo);
    $resultado = mysqli_stmt_execute($stmt);

    $sql = "DELETE FROM ciclo_profesor WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);

    $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    foreach ($listaIdsProfesores as $idProfesor) {
        mysqli_stmt_bind_param($stmt, "ii", $idCiclo, $idProfesor);
        $resultado = mysqli_stmt_execute($stmt);
    }

    
    return $resultado;
}


function obtenerCicloPorId($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM ciclos WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosCiclo = mysqli_fetch_assoc($resultado);
    
    return $datosCiclo;
}

function listarProfesoresDeUnCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT idProfesor FROM ciclo_profesor WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaIdsProfesores = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaIdsProfesores[] = $fila['idProfesor'];
    }
    
    return $listaIdsProfesores;
}

function listarNombresTutoresCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT p.nombreProfesor 
            FROM profesores p 
            JOIN ciclo_profesor cp ON p.idProfesor = cp.idProfesor 
            WHERE cp.idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $nombres = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $nombres[] = $fila['nombreProfesor'];
    }
    
    return $nombres;
}

