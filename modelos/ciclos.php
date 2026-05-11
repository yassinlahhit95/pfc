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
    $sql = "SELECT DISTINCT c.*, n.nombreNivel 
            FROM ciclos c 
            LEFT JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo 
            LEFT JOIN niveles n ON c.idNivel = n.idNivel 
            LEFT JOIN modulos m ON c.idCiclo = m.idCiclo
            LEFT JOIN profesor_modulo pm ON m.idModulo = pm.idModulo
            WHERE (cp.idProfesor = ? OR pm.idProfesor = ?)";
            
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
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

function insertarNuevoCiclo($nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $precioCiclo) {
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

    mysqli_close($con);
    return $resultado;
}

function actualizarCicloExistente($idCiclo, $nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $precioCiclo) {
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

?>
