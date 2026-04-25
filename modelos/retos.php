<?php
require_once("conectar.php");

function listarRetos() {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM retos ORDER BY idReto ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarRetosFiltrados($idModulo) {
    $conexion = obtenerConexion();
    $sql = "SELECT DISTINCT retos.* FROM retos 
            JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
            WHERE modulo_reto.idModulo = $idModulo
            ORDER BY retos.idReto ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function obtenerRetosDeProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT DISTINCT retos.* FROM retos 
            JOIN modulo_reto ON retos.idReto = modulo_reto.idReto 
            JOIN profesor_modulo ON modulo_reto.idModulo = profesor_modulo.idModulo 
            WHERE profesor_modulo.idProfesor = $idProfesor";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarReto($nombreReto, $fechaInicio, $fechaFin, $horasReto, $modulos = []) {
    // Validar que las horas no superen el límite de 30h semanales según la duración
    $inicio = new DateTime($fechaInicio);
    $fin = new DateTime($fechaFin);
    $intervalo = $inicio->diff($fin);
    $diasTotales = $intervalo->days + 1;
    
    // Calcular semanas (proporcional)
    $semanas = $diasTotales / 7;
    $maximoHorasPermitidas = round($semanas * 30);
    
    if ($horasReto > $maximoHorasPermitidas) {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $_SESSION['error'] = "El reto supera el límite de 30h semanales para esa duración (Máx: $maximoHorasPermitidas horas en $diasTotales días).";
        return false;
    }

    $conexion = obtenerConexion();
    $sql = "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) 
            VALUES ('$nombreReto', '$fechaInicio', '$fechaFin', $horasReto)";
    if (mysqli_query($conexion, $sql)) {
        $idReto = mysqli_insert_id($conexion);
        
        // Asociar módulos
        foreach ($modulos as $idModulo) {
            $sqlMod = "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($idModulo, $idReto)";
            mysqli_query($conexion, $sqlMod);
        }
        
        mysqli_close($conexion);
        return $idReto;
    }
    mysqli_close($conexion);
    return false;
}

function comprobarHorasDisponiblesModulo($idModulo, $nuevasHoras, $idRetoActual = 0) {
    $conexion = obtenerConexion();
    
    // Horas maximas del modulo
    $sqlMod = "SELECT horasMaximas FROM modulos WHERE idModulo = $idModulo";
    $resMod = mysqli_query($conexion, $sqlMod);
    $filaMod = mysqli_fetch_assoc($resMod);
    $max = $filaMod['horasMaximas'];

    // Horas ya ocupadas por otros retos (excluyendo el actual si es una actualización)
    $sqlYa = "SELECT SUM(r.horasReto) as total FROM retos r 
              JOIN modulo_reto mr ON r.idReto = mr.idReto 
              WHERE mr.idModulo = $idModulo AND r.idReto != $idRetoActual";
    $resYa = mysqli_query($conexion, $sqlYa);
    $filaYa = mysqli_fetch_assoc($resYa);
    $ocupadas = $filaYa['total'] ?: 0;

    mysqli_close($conexion);

    if (($ocupadas + $nuevasHoras) > $max) {
        return false;
    }
    return true;
}

function actualizarReto($idReto, $nombreReto, $fechaInicio, $fechaFin, $horasReto, $modulos = []) {
    // Validar que las horas no superen el límite de 30h semanales según la duración
    $inicio = new DateTime($fechaInicio);
    $fin = new DateTime($fechaFin);
    $intervalo = $inicio->diff($fin);
    $diasTotales = $intervalo->days + 1;
    
    $semanas = $diasTotales / 7;
    $maximoHorasPermitidas = round($semanas * 30);
    
    if ($horasReto > $maximoHorasPermitidas) {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $_SESSION['error'] = "El reto supera el límite de 30h semanales para esa duración (Máx: $maximoHorasPermitidas horas en $diasTotales días).";
        return false;
    }

    $conexion = obtenerConexion();
    $sql = "UPDATE retos SET nombreReto = '$nombreReto', fechaInicio = '$fechaInicio', 
            fechaFin = '$fechaFin', horasReto = $horasReto WHERE idReto = $idReto";
    $resultado = mysqli_query($conexion, $sql);
    
    if ($resultado) {
        // Limpiar asociaciones previas
        $sqlDel = "DELETE FROM modulo_reto WHERE idReto = $idReto";
        mysqli_query($conexion, $sqlDel);
        
        // Nuevas asociaciones
        foreach ($modulos as $idModulo) {
            $sqlIns = "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($idModulo, $idReto)";
            mysqli_query($conexion, $sqlIns);
        }
    }

    mysqli_close($conexion);
    return $resultado;
}

function eliminarReto($idReto) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM retos WHERE idReto = $idReto";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerRetoPorId($idReto) {
    $conexion = obtenerConexion();
    $sql = "SELECT * FROM retos WHERE idReto = $idReto";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function obtenerRetosPorCiclo($idCiclo) {
    $conexion = obtenerConexion();
    $sql = "SELECT DISTINCT r.* FROM retos r 
            JOIN modulo_reto mr ON r.idReto = mr.idReto 
            JOIN modulos m ON mr.idModulo = m.idModulo 
            WHERE m.idCiclo = $idCiclo";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function asociarModuloReto($idModulo, $idReto) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO modulo_reto (idModulo, idReto) VALUES ($idModulo, $idReto)";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function limpiarAsociacionesReto($idReto) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM modulo_reto WHERE idReto = $idReto";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerModulosDeReto($idReto) {
    $conexion = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo FROM modulos 
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo 
            JOIN modulo_reto ON modulos.idModulo = modulo_reto.idModulo 
            WHERE modulo_reto.idReto = $idReto";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function calificarReto($idEstudiante, $idReto, $nota) {
    $conexion = obtenerConexion();
    $sqlBusqueda = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    $resultadoBusqueda = mysqli_query($conexion, $sqlBusqueda);
    if (mysqli_num_rows($resultadoBusqueda) > 0) {
        $sql = "UPDATE calificaciones_retos SET nota = $nota WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    } else {
        $sql = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES ($idEstudiante, $idReto, $nota)";
    }
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerCalificacion($idEstudiante, $idReto) {
    $conexion = obtenerConexion();
    $sql = "SELECT nota FROM calificaciones_retos WHERE idEstudiante = $idEstudiante AND idReto = $idReto";
    $resultado = mysqli_query($conexion, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    $nota = "";
    if ($fila) { $nota = $fila['nota']; }
    return $nota;
}

function listarCalificacionesRetoPorModulo($idModulo) {
    $conexion = obtenerConexion();
    // Obtenemos los retos asociados a este módulo
    $sqlRetos = "SELECT idReto FROM modulo_reto WHERE idModulo = $idModulo";
    $resRetos = mysqli_query($conexion, $sqlRetos);
    $idsRetos = array();
    while($r = mysqli_fetch_assoc($resRetos)) {
        $idsRetos[] = $r['idReto'];
    }
    
    if (empty($idsRetos)) {
        mysqli_close($conexion);
        return array();
    }
    
    $idsString = implode(",", $idsRetos);
    
    // Obtenemos la media de las notas de los retos asociados a este módulo para cada estudiante
    $sql = "SELECT idEstudiante, AVG(nota) as notaMediaReto 
            FROM calificaciones_retos 
            WHERE idReto IN ($idsString) 
            GROUP BY idEstudiante";
            
    $resultado = mysqli_query($conexion, $sql);
    $lista = array();
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[$fila['idEstudiante']] = $fila['notaMediaReto'];
    }
    mysqli_close($conexion);
    return $lista;
}
?>