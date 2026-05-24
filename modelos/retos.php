<?php
require_once __DIR__ . "/conectar.php";

// trae todos los retos ordenados por id
function listarRetos() {
    $con = obtenerConexion();
    $sql1 = "SELECT * FROM retos ORDER BY idReto ASC";
    $resultado = mysqli_query($con, $sql1);

    $listaRetos = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaRetos[] = $fila;
    }
    mysqli_close($con);
    return $listaRetos;
}

function listarRetosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql1 = "SELECT DISTINCT r.* FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto JOIN modulo_profesor pm ON mr.idModulo = pm.idModulo WHERE pm.idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idProfesor);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $listaProfesor = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $listaProfesor[] = $fila;
    }
    mysqli_close($con);
    return $listaProfesor;
}

function insertarReto($nombreReto, $fechaInicio, $fechaFin, $horasReto, $listaIdsModulos) {
    $con = obtenerConexion();
    $sql1 = "INSERT INTO retos (nombreReto, fechaInicio, fechaFin, horasReto) VALUES (?, ?, ?, ?)";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "sssi", $nombreReto, $fechaInicio, $fechaFin, $horasReto);
    $ok = mysqli_stmt_execute($resultado);

    if ($ok) {
        $idNuevoReto = mysqli_insert_id($con);

        $sql2 = "INSERT INTO modulo_reto (idModulo, idReto) VALUES (?, ?)";
        $resultado = mysqli_prepare($con, $sql2);
        foreach ($listaIdsModulos as $idModulo) {
            mysqli_stmt_bind_param($resultado, "ii", $idModulo, $idNuevoReto);
            $ok = mysqli_stmt_execute($resultado);
        }
    }
    mysqli_close($con);
    return $ok;
}

function obtenerDetalleHorasModulo($idModulo, $idRetoAExcluir = 0) {
    $con = obtenerConexion();
    $idRetoAExcluir = intval($idRetoAExcluir);
    $sql1 = "SELECT m.nombreModulo, m.horasMaximas, SUM(r.horasReto) AS horasOcupadas
            FROM modulos m
            LEFT JOIN modulo_reto mr ON m.idModulo = mr.idModulo
            LEFT JOIN retos r ON mr.idReto = r.idReto AND r.idReto != ?
            WHERE m.idModulo = ?
            GROUP BY m.idModulo, m.nombreModulo, m.horasMaximas";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ii", $idRetoAExcluir, $idModulo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($con);

    $detalle = [
        'nombreModulo' => '',
        'maximo' => 0,
        'ocupadas' => 0,
        'disponibles' => 0
    ];

    if ($fila) {
        $detalle['nombreModulo'] = $fila['nombreModulo'];
        $detalle['maximo'] = intval($fila['horasMaximas']);
        $detalle['ocupadas'] = intval($fila['horasOcupadas'] ?? 0);
        $detalle['disponibles'] = $detalle['maximo'] - $detalle['ocupadas'];
    }

    return $detalle;
}

function actualizarReto($idReto, $nombreReto, $fechaInicio, $fechaFin, $horasReto, $listaIdsModulos = null) {
    $con = obtenerConexion();
    $sql1 = "UPDATE retos SET nombreReto=?, fechaInicio=?, fechaFin=?, horasReto=? WHERE idReto=?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "sssii", $nombreReto, $fechaInicio, $fechaFin, $horasReto, $idReto);
    $ok = mysqli_stmt_execute($resultado);

    if ($ok && $listaIdsModulos != null) {
        $sql2 = "DELETE FROM modulo_reto WHERE idReto = ?";
        $resultado = mysqli_prepare($con, $sql2);
        mysqli_stmt_bind_param($resultado, "i", $idReto);
        mysqli_stmt_execute($resultado);

        $sql3 = "INSERT INTO modulo_reto (idModulo, idReto) VALUES (?, ?)";
        $resultado = mysqli_prepare($con, $sql3);
        foreach ($listaIdsModulos as $idModulo) {
            mysqli_stmt_bind_param($resultado, "ii", $idModulo, $idReto);
            $ok = mysqli_stmt_execute($resultado);
        }
    }

    mysqli_close($con);
    return $ok;
}

function eliminarReto($idReto) {
    $con = obtenerConexion();
    $sql1 = "DELETE FROM retos WHERE idReto = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idReto);
    $ok = mysqli_stmt_execute($resultado);
    mysqli_close($con);
    return $ok;
}

function obtenerRetoPorId($idReto) {
    $con = obtenerConexion();
    $sql1 = "SELECT * FROM retos WHERE idReto = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idReto);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $reto = mysqli_fetch_assoc($res);
    mysqli_close($con);
    return $reto;
}

function listarModulosDeReto($idReto) {
    $con = obtenerConexion();
    $sql1 = "SELECT m.*, c.nombreCiclo FROM modulos m JOIN ciclos c ON m.idCiclo = c.idCiclo JOIN modulo_reto mr ON m.idModulo = mr.idModulo WHERE mr.idReto = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idReto);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $listaModulos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $listaModulos[] = $fila;
    }
    mysqli_close($con);
    return $listaModulos;
}

// inserta la nota o la actualiza si ya exsite
function calificarReto($idEstudiante, $idReto, $notaObtenida) {
    $con = obtenerConexion();

    $sql1 = "SELECT idCalificacion FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ii", $idEstudiante, $idReto);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);

    if (mysqli_num_rows($res) > 0) {
        $sql2 = "UPDATE calificaciones_retos SET nota = ? WHERE idEstudiante = ? AND idReto = ?";
        $resultado = mysqli_prepare($con, $sql2);
        mysqli_stmt_bind_param($resultado, "dii", $notaObtenida, $idEstudiante, $idReto);
    } else {
        $sql2 = "INSERT INTO calificaciones_retos (idEstudiante, idReto, nota) VALUES (?, ?, ?)";
        $resultado = mysqli_prepare($con, $sql2);
        mysqli_stmt_bind_param($resultado, "iid", $idEstudiante, $idReto, $notaObtenida);
    }

    $ok = mysqli_stmt_execute($resultado);
    mysqli_close($con);
    return $ok;
}

function eliminarCalificacionReto($idEstudiante, $idReto) {
    $con = obtenerConexion();
    $sql1 = "DELETE FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ii", $idEstudiante, $idReto);
    $ok = mysqli_stmt_execute($resultado);
    mysqli_close($con);
    return $ok;
}

function obtenerCalificacionReto($idEstudiante, $idReto) {
    $con = obtenerConexion();
    $sql1 = "SELECT nota FROM calificaciones_retos WHERE idEstudiante = ? AND idReto = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ii", $idEstudiante, $idReto);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $fila = mysqli_fetch_assoc($res);

    $nota = "";
    if ($fila) {
        $nota = $fila['nota'];
    }

    mysqli_close($con);
    return $nota;
}

function listarCalificacionesRetoPorModulo($idModulo) {
    $con = obtenerConexion();
    $sql1 = "SELECT cr.idEstudiante, AVG(cr.nota) AS promedio
            FROM calificaciones_retos cr
            JOIN modulo_reto mr ON cr.idReto = mr.idReto
            WHERE mr.idModulo = ?
            GROUP BY cr.idEstudiante";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $medias = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $medias[$fila['idEstudiante']] = $fila['promedio'];
    }
    mysqli_close($con);
    return $medias;
}

function listarCalificacionesRetoPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql1 = "SELECT r.nombreReto, cr.nota, r.fechaInicio, r.fechaFin FROM calificaciones_retos cr JOIN retos r ON cr.idReto = r.idReto WHERE cr.idEstudiante = ? ORDER BY r.fechaInicio DESC";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idEstudiante);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $listaHistorial = [];
    while($fila = mysqli_fetch_assoc($res)) {
        $listaHistorial[] = $fila;
    }
    mysqli_close($con);
    return $listaHistorial;
}

function listarRetosPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql1 = "SELECT DISTINCT r.* FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto JOIN modulos m ON mr.idModulo = m.idModulo WHERE m.idCiclo = ? ORDER BY r.idReto ASC";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idCiclo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $listaRetos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $listaRetos[] = $fila;
    }
    mysqli_close($con);
    return $listaRetos;
}

function listarRetosPorCicloDeProfesor($idCiclo, $idProfesor) {
    $con = obtenerConexion();
    $sql1 = "SELECT DISTINCT r.* FROM retos r
            JOIN modulo_reto mr ON r.idReto = mr.idReto
            JOIN modulos m ON mr.idModulo = m.idModulo
            JOIN modulo_profesor pm ON m.idModulo = pm.idModulo
            WHERE m.idCiclo = ? AND pm.idProfesor = ?
            ORDER BY r.idReto ASC";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ii", $idCiclo, $idProfesor);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

function obtenerPromedioRetosEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql1 = "SELECT AVG(nota) as promedio FROM calificaciones_retos WHERE idEstudiante = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idEstudiante);
    mysqli_stmt_execute($resultado);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($resultado));
    mysqli_close($con);
    return $row['promedio'] ? floatval($row['promedio']) : 0;
}
