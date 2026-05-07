<?php
require_once __DIR__ . "/conectar.php";

function listarRetos() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM retos ORDER BY idReto ASC";
    $resultado = mysqli_query($con, $sql);

    $listaRetos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaRetos[] = $fila;
    }
    mysqli_close($con);
    return $listaRetos;
}

function listarRetosFiltrados($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT r.* FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto WHERE mr.idModulo = ? ORDER BY r.idReto ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaFiltrada = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaFiltrada[] = $fila;
    }
    mysqli_close($con);
    return $listaFiltrada;
}

function obtenerRetosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT r.* FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto JOIN profesor_modulo pm ON mr.idModulo = pm.idModulo WHERE pm.idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaProfesor = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaProfesor[] = $fila;
    }
    mysqli_close($con);
    return $listaProfesor;
}

function insertarReto($nombreReto, $fechaInicio, $fechaFin, $horasReto, $listaIdsModulos) {
    $con = obtenerConexion();
    $sql = "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nombreReto, $fechaInicio, $fechaFin, $horasReto);
    $resultado = mysqli_stmt_execute($stmt);

    if ($resultado) {
        $idNuevoReto = mysqli_insert_id($con);

        $sql2 = "INSERT INTO modulo_reto (idModulo, idReto) VALUES (?, ?)";
        $stmt2 = mysqli_prepare($con, $sql2);
        foreach ($listaIdsModulos as $idModulo) {
            mysqli_stmt_bind_param($stmt2, "ii", $idModulo, $idNuevoReto);
            $resultado = mysqli_stmt_execute($stmt2);
        }
    }
    mysqli_close($con);
    return $resultado;
}

function comprobarHorasDisponiblesModulo($idModulo, $horasNuevas, $idRetoAExcluir = 0) {
    $con = obtenerConexion();

    $sql = "SELECT horasMaximas FROM modulos WHERE idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosModulo = mysqli_fetch_assoc($resultado);
    $limiteMaximo = (int)($datosModulo['horasMaximas'] ?? 0);

    $idRetoAExcluir = (int)$idRetoAExcluir;

    $sql = "SELECT SUM(r.horasReto) as total FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto WHERE mr.idModulo = ? AND r.idReto != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idModulo, $idRetoAExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosSuma = mysqli_fetch_assoc($resultado);
    $horasOcupadas = (int)($datosSuma['total'] ?? 0);

    mysqli_close($con);

    return (($horasOcupadas + $horasNuevas) <= $limiteMaximo);
}

function actualizarReto($idReto, $nombreReto, $fechaInicio, $fechaFin, $horasReto, $listaIdsModulos = null) {
    $con = obtenerConexion();
    $sql = "UPDATE retos SET nombreReto=?, fechaInicio=?, fechaFin=?, horasReto=? WHERE idReto=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssii", $nombreReto, $fechaInicio, $fechaFin, $horasReto, $idReto);
    $resultado = mysqli_stmt_execute($stmt);

    if ($resultado && $listaIdsModulos !== null) {
        $sql = "DELETE FROM modulo_reto WHERE idReto = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idReto);
        mysqli_stmt_execute($stmt);

        $sql = "INSERT INTO modulo_reto (idModulo, idReto) VALUES (?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        foreach ($listaIdsModulos as $idModulo) {
            mysqli_stmt_bind_param($stmt, "ii", $idModulo, $idReto);
            $resultado = mysqli_stmt_execute($stmt);
        }
    }

    mysqli_close($con);
    return $resultado;
}

function eliminarReto($idReto) {
    $con = obtenerConexion();
    $sql = "DELETE FROM retos WHERE idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReto);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerRetoPorId($idReto) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM retos WHERE idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReto);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $reto = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $reto;
}

function obtenerModulosDeReto($idReto) {
    $con = obtenerConexion();
    $sql = "SELECT m.*, c.nombreCiclo FROM modulos m JOIN ciclos c ON m.idCiclo = c.idCiclo JOIN modulo_reto mr ON m.idModulo = mr.idModulo WHERE mr.idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idReto);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaModulos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaModulos[] = $fila;
    }
    mysqli_close($con);
    return $listaModulos;
}

function calificarReto($idEstudiante, $idReto, $notaObtenida) {
    $con = obtenerConexion();

    $sql = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idReto);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        $sql = "UPDATE calificaciones_retos SET nota = ? WHERE idEstudiante = ? AND idReto = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "dii", $notaObtenida, $idEstudiante, $idReto);
    } else {
        $sql = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iid", $idEstudiante, $idReto, $notaObtenida);
    }

    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function eliminarCalificacionReto($idEstudiante, $idReto) {
    $con = obtenerConexion();
    $sql = "DELETE FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idReto);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerCalificacionReto($idEstudiante, $idReto) {
    $con = obtenerConexion();
    $sql = "SELECT nota FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idReto);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);

    $nota = "";
    if (isset($fila['nota'])) {
        $nota = $fila['nota'];
    }

    mysqli_close($con);
    return $nota;
}

function listarCalificacionesRetoPorModulo($idModulo) {
    $con = obtenerConexion();

    $sql = "SELECT idReto FROM modulo_reto WHERE idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $idsRetos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $idsRetos[] = (int)$fila['idReto'];
    }

    if (empty($idsRetos)) {
        mysqli_close($con);
        return [];
    }

    // IDs son enteros obtenidos de la BD — se construye la cláusula IN de forma segura
    $listaIds = implode(",", $idsRetos);

    $sql = "SELECT idEstudiante, AVG(nota) as promedio
                  FROM calificaciones_retos
                  WHERE idReto IN ($listaIds)
                  GROUP BY idEstudiante";
    $resultado = mysqli_query($con, $sql);

    $mapaMedias = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $mapaMedias[$fila['idEstudiante']] = $fila['promedio'];
    }

    mysqli_close($con);
    return $mapaMedias;
}

function listarCalificacionesRetoPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT r.nombreReto, cr.nota, r.fechaInicio, r.fechaFin FROM calificaciones_retos cr JOIN retos r ON cr.idReto = r.idReto WHERE cr.idEstudiante = ? ORDER BY r.fechaInicio DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaHistorial = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaHistorial[] = $fila;
    }
    mysqli_close($con);
    return $listaHistorial;
}

function obtenerRetosPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT r.* FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto JOIN modulos m ON mr.idModulo = m.idModulo WHERE m.idCiclo = ? ORDER BY r.idReto ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaRetos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaRetos[] = $fila;
    }
    mysqli_close($con);
    return $listaRetos;
}

function obtenerPromedioRetosEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT AVG(nota) as promedio FROM calificaciones_retos WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    $promedio = (float)($fila['promedio'] ?? 0);
    mysqli_close($con);
    return $promedio;
}
?>
