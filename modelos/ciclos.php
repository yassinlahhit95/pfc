<?php
require_once("conectar.php");

// Ver todos los ciclos
function listarTodosLosCiclos() {
    $db = obtenerConexion();
    $sql = "SELECT ciclos.*, niveles.nombreNivel FROM ciclos LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel ORDER BY ciclos.idCiclo ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Ciclos de un profesor
function obtenerCiclosDeProfesor($idProfesor) {
    $db = obtenerConexion();
    $sql = "SELECT ciclos.*, niveles.nombreNivel FROM ciclos JOIN ciclo_profesor ON ciclos.idCiclo = ciclo_profesor.idCiclo LEFT JOIN niveles ON ciclos.idNivel = niveles.idNivel WHERE ciclo_profesor.idProfesor = $idProfesor";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Crear ciclo nuevo
function insertarNuevoCiclo($nombre, $abreviatura, $idNivel, $listaProfesores, $listaAulas, $precio) {
    $db = obtenerConexion();
    $sql = "INSERT INTO ciclos (nombreCiclo, abreviaturaCiclo, idNivel, precioCiclo) VALUES ('$nombre', '$abreviatura', $idNivel, $precio)";
    $resultado = mysqli_query($db, $sql);

    // Cogemos el ID mas alto (el que acabamos de crear)
    $sqlId = "SELECT MAX(idCiclo) as ultimoId FROM ciclos";
    $resId = mysqli_query($db, $sqlId);
    $filaId = mysqli_fetch_assoc($resId);
    $idNuevo = $filaId['ultimoId'];

    // Relacionar con profesores
    foreach ($listaProfesores as $idP) {
        $sqlP = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idNuevo, $idP)";
        mysqli_query($db, $sqlP);
    }

    // Relacionar con aulas
    foreach ($listaAulas as $idA) {
        $sqlA = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($idNuevo, $idA)";
        mysqli_query($db, $sqlA);
    }

    mysqli_close($db);
    return $resultado;
}

// Actualizar un ciclo
function actualizarCicloExistente($id, $nombre, $abreviatura, $idNivel, $listaProfesores, $listaAulas, $precio) {
    $db = obtenerConexion();
    
    // 1. Actualizamos datos basicos
    $sql = "UPDATE ciclos SET nombreCiclo='$nombre', abreviaturaCiclo='$abreviatura', idNivel=$idNivel, precioCiclo=$precio WHERE idCiclo=$id";
    $resultado = mysqli_query($db, $sql);

    // 2. Limpiar y poner profesores nuevos
    $sqlDelProf = "DELETE FROM ciclo_profesor WHERE idCiclo = $id";
    mysqli_query($db, $sqlDelProf);
    foreach ($listaProfesores as $idP) {
        $sqlInsProf = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($id, $idP)";
        mysqli_query($db, $sqlInsProf);
    }

    // 3. Limpiar y poner aulas nuevas
    $sqlDelAula = "DELETE FROM ciclo_aula WHERE idCiclo = $id";
    mysqli_query($db, $sqlDelAula);
    foreach ($listaAulas as $idA) {
        $sqlInsAula = "INSERT INTO ciclo_aula (idCiclo, idAula) VALUES ($id, $idA)";
        mysqli_query($db, $sqlInsAula);
    }

    mysqli_close($db);
    return $resultado;
}

// Borrar ciclo
function eliminarCiclo($id) {
    $db = obtenerConexion();
    $sql = "DELETE FROM ciclos WHERE idCiclo = $id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Buscar por ID
function obtenerCicloPorId($id) {
    $db = obtenerConexion();
    $sql = "SELECT * FROM ciclos WHERE idCiclo = $id";
    $resultado = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $fila;
}

// Mirar si el nombre esta repetido
function comprobarNombreEnOtroCiclo($nombre, $idActual) {
    $db = obtenerConexion();
    $sql = "SELECT idCiclo FROM ciclos WHERE nombreCiclo = '$nombre' AND idCiclo != $idActual";
    $resultado = mysqli_query($db, $sql);
    $cuenta = mysqli_num_rows($resultado);
    mysqli_close($db);
    
    if ($cuenta > 0) {
        return true;
    }
    return false;
}
?>
