<?php
require_once __DIR__ . "/conectar.php";

function listarTodosLosCiclos() {
    $con = obtenerConexion();
    $sql = "SELECT ciclos.*, niveles.nombreNivel
            FROM ciclos
            LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel
            ORDER BY ciclos.idCiclo ASC";

    $resultado = mysqli_query($con, $sql);
    $listaCiclos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaCiclos[] = $fila;
    }
    mysqli_close($con);
    return $listaCiclos;
}

function obtenerCiclosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT ciclos.*, niveles.nombreNivel FROM ciclos JOIN ciclo_profesor ON ciclos.idCiclo = ciclo_profesor.idCiclo LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel WHERE ciclo_profesor.idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaCiclos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaCiclos[] = $fila;
    }
    mysqli_close($con);
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
    mysqli_close($con);
    return $existe;
}

function insertarNuevoCiclo($nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $listaIdsAulas, $precioCiclo) {
    if (checkCicloExistente($nombreCiclo, $abreviaturaCiclo)) {
        return false;
    }
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

    $sql = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    foreach ($listaIdsAulas as $idAula) {
        mysqli_stmt_bind_param($stmt, "ii", $idNuevoCiclo, $idAula);
        $resultado = mysqli_stmt_execute($stmt);
    }

    mysqli_close($con);
    return $resultado;
}

function actualizarCicloExistente($idCiclo, $nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $listaIdsAulas, $precioCiclo) {
    if (checkCicloExistente($nombreCiclo, $abreviaturaCiclo, $idCiclo)) {
        return false;
    }
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

    $sql = "DELETE FROM ciclo_aula WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);

    $sql = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    foreach ($listaIdsAulas as $idAula) {
        mysqli_stmt_bind_param($stmt, "ii", $idCiclo, $idAula);
        $resultado = mysqli_stmt_execute($stmt);
    }

    mysqli_close($con);
    return $resultado;
}

function eliminarCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM ciclos WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
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
    mysqli_close($con);
    return $datosCiclo;
}

function obtenerProfesoresDeUnCiclo($idCiclo) {
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
    mysqli_close($con);
    return $listaIdsProfesores;
}

function obtenerAulasDeUnCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT idAula FROM ciclo_aula WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaIdsAulas = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaIdsAulas[] = $fila['idAula'];
    }
    mysqli_close($con);
    return $listaIdsAulas;
}

?>
