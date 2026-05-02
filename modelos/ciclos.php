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
    $sql = "SELECT ciclos.*, niveles.nombreNivel 
            FROM ciclos 
            JOIN ciclo_profesor ON ciclos.idCiclo = ciclo_profesor.idCiclo 
            LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel 
            WHERE ciclo_profesor.idProfesor = $idProfesor";
            
    $resultado = mysqli_query($con, $sql);
    $listaCiclos = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaCiclos[] = $fila; 
    }
    mysqli_close($con);
    return $listaCiclos;
}

// Registrar un nuevo ciclo formativo y sus relaciones (profesores y aulas)
function insertarNuevoCiclo($nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $listaIdsAulas, $precioCiclo) {
    $con = obtenerConexion();
    
    // Insertamos los datos básicos del ciclo
    $sql = "INSERT INTO ciclos (nombreCiclo, abreviaturaCiclo, idNivel, precioCiclo) 
            VALUES ('$nombreCiclo', '$abreviaturaCiclo', $idNivel, $precioCiclo)";
    $resultado = mysqli_query($con, $sql);

    // Obtenemos el ID del ciclo que acabamos de crear
    $idNuevoCiclo = mysqli_insert_id($con);

    // Relacionamos con los profesores seleccionados
    foreach ($listaIdsProfesores as $idProfesor) {
        $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idNuevoCiclo, $idProfesor)";
        $resultado = mysqli_query($con, $sql);
    }

    // Relacionamos con las aulas seleccionadas
    foreach ($listaIdsAulas as $idAula) {
        $sql = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($idNuevoCiclo, $idAula)";
        $resultado = mysqli_query($con, $sql);
    }

    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos de un ciclo formativo existente y sus relaciones
function actualizarCicloExistente($idCiclo, $nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $listaIdsAulas, $precioCiclo) {
    $con = obtenerConexion();
    
    // 1. Actualizamos los datos básicos del ciclo
    $sql = "UPDATE ciclos 
            SET nombreCiclo='$nombreCiclo', abreviaturaCiclo='$abreviaturaCiclo', idNivel=$idNivel, precioCiclo=$precioCiclo 
            WHERE idCiclo=$idCiclo";
    $resultado = mysqli_query($con, $sql);

    // 2. Limpiamos y reasignamos los profesores
    $sql = "DELETE FROM ciclo_profesor WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($con, $sql);
    foreach ($listaIdsProfesores as $idProfesor) {
        $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idCiclo, $idProfesor)";
        $resultado = mysqli_query($con, $sql);
    }

    // 3. Limpiamos y reasignamos las aulas
    $sql = "DELETE FROM ciclo_aula WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($con, $sql);
    foreach ($listaIdsAulas as $idAula) {
        $sql = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($idCiclo, $idAula)";
        $resultado = mysqli_query($con, $sql);
    }

    mysqli_close($con);
    return $resultado;
}

// Eliminar un ciclo formativo por su ID
function eliminarCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM ciclos WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un ciclo formativo específico
function obtenerCicloPorId($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM ciclos WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($con, $sql);
    $datosCiclo = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosCiclo;
}

// Obtener la lista de IDs de profesores asignados a un ciclo
function obtenerProfesoresDeUnCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT idProfesor FROM ciclo_profesor WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($con, $sql);
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
    $sql = "SELECT idAula FROM ciclo_aula WHERE idCiclo = $idCiclo";
    $resultado = mysqli_query($con, $sql);
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
    $sql = "SELECT idCiclo FROM ciclos 
            WHERE nombreCiclo = '$nombreCiclo' AND idCiclo != $idCicloActual";
    $resultado = mysqli_query($con, $sql);
    $totalCoincidencias = mysqli_num_rows($resultado);
    mysqli_close($con);
    
    return ($totalCoincidencias > 0);
}
