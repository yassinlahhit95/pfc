<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todos los ciclos formativos registrados
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

// Obtener los ciclos formativos que tiene asignados un profesor
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

// Comprobar si ya existe un ciclo con el mismo nombre o abreviatura
function checkCicloExistente($nombreCiclo, $abreviaturaCiclo, $idExcluir = null) {
    $con = obtenerConexion();
    if ($idExcluir) {
        $sql = "SELECT idCiclo FROM ciclos WHERE (nombreCiclo = ? OR abreviaturaCiclo = ?) AND idCiclo != ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $nombreCiclo, $abreviaturaCiclo, $idExcluir);
    } else {
        $sql = "SELECT idCiclo FROM ciclos WHERE (nombreCiclo = ? OR abreviaturaCiclo = ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $nombreCiclo, $abreviaturaCiclo);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

// Registrar un nuevo ciclo formativo y sus relaciones (profesores y aulas)
function insertarNuevoCiclo($nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $listaIdsAulas, $precioCiclo) {
    if (checkCicloExistente($nombreCiclo, $abreviaturaCiclo)) {
        return false;
    }
    $con = obtenerConexion();

    // Insertamos los datos básicos del ciclo
    $sql = "INSERT INTO ciclos (nombreCiclo, abreviaturaCiclo, idNivel, precioCiclo) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssid", $nombreCiclo, $abreviaturaCiclo, $idNivel, $precioCiclo);
    $resultado = mysqli_stmt_execute($stmt);

    // Obtenemos el ID del ciclo que acabamos de crear
    $idNuevoCiclo = mysqli_insert_id($con);

    // Relacionamos con los profesores seleccionados
    $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    foreach ($listaIdsProfesores as $idProfesor) {
        mysqli_stmt_bind_param($stmt, "ii", $idNuevoCiclo, $idProfesor);
        $resultado = mysqli_stmt_execute($stmt);
    }

    // Relacionamos con las aulas seleccionadas
    $sql = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    foreach ($listaIdsAulas as $idAula) {
        mysqli_stmt_bind_param($stmt, "ii", $idNuevoCiclo, $idAula);
        $resultado = mysqli_stmt_execute($stmt);
    }

    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos de un ciclo formativo existente y sus relaciones
function actualizarCicloExistente($idCiclo, $nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $listaIdsAulas, $precioCiclo) {
    if (checkCicloExistente($nombreCiclo, $abreviaturaCiclo, $idCiclo)) {
        return false;
    }
    $con = obtenerConexion();

    // 1. Actualizamos los datos básicos del ciclo
    $sql = "UPDATE ciclos SET nombreCiclo=?, abreviaturaCiclo=?, idNivel=?, precioCiclo=? WHERE idCiclo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssdii", $nombreCiclo, $abreviaturaCiclo, $precioCiclo, $idNivel, $idCiclo);
    $resultado = mysqli_stmt_execute($stmt);

    // 2. Limpiamos y reasignamos los profesores
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

    // 3. Limpiamos y reasignamos las aulas
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

// Eliminar un ciclo formativo por su ID
function eliminarCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM ciclos WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un ciclo formativo específico
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

// Obtener la lista de IDs de profesores asignados a un ciclo
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

// Obtener la lista de IDs de aulas asignadas a un ciclo
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

// Comprobar si un nombre de ciclo ya está siendo usado por otro ciclo diferente
function comprobarNombreEnOtroCiclo($nombreCiclo, $idCicloActual) {
    $con = obtenerConexion();
    $sql = "SELECT idCiclo FROM ciclos WHERE nombreCiclo = ? AND idCiclo != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nombreCiclo, $idCicloActual);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $totalCoincidencias = mysqli_num_rows($resultado);
    mysqli_close($con);
    return ($totalCoincidencias > 0);
}
?>
