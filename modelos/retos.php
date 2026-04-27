<?php
require_once("conectar.php");

// Ver lista de retos
function listarRetos() {
    $db = obtenerConexion();
    $sql = "SELECT * FROM retos ORDER BY idReto ASC";
    $resultado = mysqli_query($db, $sql);
    
    $listaDeRetos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaDeRetos[] = $fila; 
    }
    mysqli_close($db);
    return $listaDeRetos;
}

// Filtrar retos por un modulo
function listarRetosFiltrados($idModuloRecibido) {
    $db = obtenerConexion();
    $sql = "SELECT DISTINCT retos.* FROM retos 
            JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
            WHERE modulo_reto.idModulo = $idModuloRecibido 
            ORDER BY retos.idReto ASC";
            
    $resultado = mysqli_query($db, $sql);
    $listaFiltrada = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaFiltrada[] = $fila; 
    }
    mysqli_close($db);
    return $listaFiltrada;
}

// Retos que tiene asignados un profesor
function obtenerRetosDeProfesor($idProfesorRecibido) {
    $db = obtenerConexion();
    $sql = "SELECT DISTINCT retos.* FROM retos 
            JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
            JOIN profesor_modulo ON modulo_reto.idModulo = profesor_modulo.idModulo 
            WHERE profesor_modulo.idProfesor = $idProfesorRecibido";
            
    $resultado = mysqli_query($db, $sql);
    $listaDelProfesor = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaDelProfesor[] = $fila; 
    }
    mysqli_close($db);
    return $listaDelProfesor;
}

// Guardar un reto nuevo
function insertarReto($nombreReto, $fechaInicio, $fechaFin, $horasReto, $listaDeModulos) {
    $db = obtenerConexion();
    $sql = "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) 
            VALUES ('$nombreReto', '$fechaInicio', '$fechaFin', $horasReto)";
    $resultado = mysqli_query($db, $sql);
    
    if ($resultado) {
        // Sacamos el ID del reto que acabamos de crear
        $sqlMaximo = "SELECT MAX(idReto) as ultimoId FROM retos";
        $resultadoMax = mysqli_query($db, $sqlMaximo);
        $filaId = mysqli_fetch_assoc($resultadoMax);
        $idNuevoReto = $filaId['ultimoId'];

        // Lo unimos con los modulos elegidos
        foreach ($listaDeModulos as $idModuloIndividual) { 
            $sqlRelacion = "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($idModuloIndividual, $idNuevoReto)";
            mysqli_query($db, $sqlRelacion); 
        }
    }
    mysqli_close($db);
    return $resultado;
}

// Mirar si el modulo tiene horas libres para este reto
function comprobarHorasDisponiblesModulo($idModulo, $horasNuevas, $idRetoAExcluir) {
    $db = obtenerConexion();
    
    // Horas totales que permite el modulo
    $sqlModulo = "SELECT horasMaximas FROM modulos WHERE idModulo = $idModulo";
    $resModulo = mysqli_query($db, $sqlModulo);
    $datosDelModulo = mysqli_fetch_assoc($resModulo);
    $maximoPermitido = $datosDelModulo['horasMaximas'];

    // Horas que ya estan ocupadas por otros retos
    $sqlSuma = "SELECT SUM(retos.horasReto) as totalOcupado FROM retos 
               JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
               WHERE modulo_reto.idModulo = $idModulo AND retos.idReto != $idRetoAExcluir";
               
    $resSuma = mysqli_query($db, $sqlSuma);
    $datosDeSuma = mysqli_fetch_assoc($resSuma);
    
    $horasGastadas = 0;
    if (isset($datosDeSuma['totalOcupado'])) {
        $horasGastadas = $datosDeSuma['totalOcupado'];
    }
    
    mysqli_close($db);
    
    // Si la suma no pasa del maximo, devolvemos true
    if (($horasGastadas + $horasNuevas) <= $maximoPermitido) {
        return true;
    }
    return false;
}

// Actualizar los datos de un reto
function actualizarReto($idReto, $nombre, $inicio, $fin, $horas, $listaModulos = null) {
    $db = obtenerConexion();
    $sql = "UPDATE retos SET nombreReto='$nombre', fechaInicio='$inicio', fechaFin='$fin', horasReto=$horas WHERE idReto=$idReto";
    $resultado = mysqli_query($db, $sql);

    if ($resultado && $listaModulos !== null) {
        // Borramos las uniones viejas y ponemos las nuevas
        $sqlBorrar = "DELETE FROM modulo_reto WHERE idReto = $idReto";
        mysqli_query($db, $sqlBorrar);

        foreach ($listaModulos as $idModuloIndividual) { 
            $sqlInsertar = "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($idModuloIndividual, $idReto)";
            mysqli_query($db, $sqlInsertar);
        }
    }

    mysqli_close($db);
    return $resultado;
}

// Borrar reto
function eliminarReto($idRetoABorrar) {
    $db = obtenerConexion();
    $sql = "DELETE FROM retos WHERE idReto = $idRetoABorrar";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Coger datos por ID
function obtenerRetoPorId($idRetoBuscado) {
    $db = obtenerConexion();
    $sql = "SELECT * FROM retos WHERE idReto = $idRetoBuscado";
    $resultado = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $fila;
}

// Ver que modulos estan en este reto
function obtenerModulosDeReto($idRetoConsultado) {
    $db = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo FROM modulos 
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            JOIN modulo_reto ON modulos.idModulo = modulo_reto.idModulo 
            WHERE modulo_reto.idReto = $idRetoConsultado";
            
    $resultado = mysqli_query($db, $sql);
    $listaFinal = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $listaFinal[] = $fila; 
    }
    mysqli_close($db);
    return $listaFinal;
}

// Poner nota a un alumno en un reto
function calificarReto($idEstudiante, $idReto, $notaRecibida) {
    $db = obtenerConexion();
    $sqlCheck = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    $resCheck = mysqli_query($db, $sqlCheck);
    
    if (mysqli_num_rows($resCheck) > 0) {
        $sql = "UPDATE calificaciones_retos SET nota = $notaRecibida WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    } else {
        $sql = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES ($idEstudiante, $idReto, $notaRecibida)";
    }
    
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Ver nota de un alumno en un reto
function obtenerCalificacion($idEstudiante, $idReto) {
    $db = obtenerConexion();
    $sql = "SELECT nota FROM calificaciones_retos WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    $resultado = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    
    $valorNota = "";
    if (isset($fila['nota'])) {
        $valorNota = $fila['nota'];
    }
    
    mysqli_close($db);
    return $valorNota;
}

// Media de notas de retos de un modulo
function listarCalificacionesRetoPorModulo($idModulo) {
    $db = obtenerConexion();
    
    // Primero buscamos los retos del modulo
    $sqlRetos = "SELECT idReto FROM modulo_reto WHERE idModulo = $idModulo";
    $resRetos = mysqli_query($db, $sqlRetos);
    
    $idsDeRetos = [];
    while($filaReto = mysqli_fetch_assoc($resRetos)) { 
        $idsDeRetos[] = $filaReto['idReto']; 
    }
    
    if (count($idsDeRetos) == 0) { 
        mysqli_close($db); 
        return []; 
    }
    
    $cadenaDeIds = implode(",", $idsDeRetos);
    
    // Sacamos el promedio por cada alumno
    $sqlMedia = "SELECT idEstudiante, AVG(nota) as promedio FROM calificaciones_retos WHERE idReto IN ($cadenaDeIds) GROUP BY idEstudiante";
    $resultado = mysqli_query($db, $sqlMedia);
    
    $mapaDeNotas = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $mapaDeNotas[$fila['idEstudiante']] = $fila['promedio']; 
    }
    
    mysqli_close($db);
    return $mapaDeNotas;
}

// Historial completo de un alumno
function listarCalificacionesRetoPorEstudiante($idEstudiante) {
    $db = obtenerConexion();
    $sql = "SELECT retos.nombreReto, calificaciones_retos.nota, retos.fechaInicio, retos.fechaFin 
            FROM calificaciones_retos 
            JOIN retos ON calificaciones_retos.idReto = retos.idReto 
            WHERE calificaciones_retos.idEstudiante = $idEstudiante 
            ORDER BY retos.fechaInicio DESC";
            
    $resultado = mysqli_query($db, $sql);
    $listaFinal = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaFinal[] = $fila; 
    }
    mysqli_close($db);
    return $listaFinal;
}
// Retos de un ciclo (via sus modulos)
function obtenerRetosPorCiclo($idCic) {
    $db = obtenerConexion();
    $sql = "SELECT DISTINCT retos.* FROM retos 
            JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
            JOIN modulos ON modulo_reto.idModulo = modulos.idModulo 
            WHERE modulos.idCiclo = $idCic 
            ORDER BY retos.idReto ASC";
            
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}
?>
