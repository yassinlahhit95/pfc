<?php
require_once __DIR__ . "/conectar.php";

function listarTodosLosTFGs() {
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, c.nombreCiclo, c.idCiclo
            FROM estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE e.archivoTFG != ''
            ORDER BY e.nombreEstudiante ASC";

    $resultado = mysqli_query($con, $sql);
    $listaTFGs = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaTFGs[] = $fila;
    }
    mysqli_close($con);
    return $listaTFGs;
}

function listarTFGsFiltrados($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, c.nombreCiclo, c.idCiclo FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE e.archivoTFG != '' AND e.idCiclo = ? ORDER BY e.nombreEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaFiltrada = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaFiltrada[] = $fila;
    }
    mysqli_close($con);
    return $listaFiltrada;
}

function obtenerTFGporEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT idEstudiante, nombreEstudiante, archivoTFG, fechaSubidaTFG FROM estudiantes WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosTFG = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosTFG;
}

function actualizarTFG($idEstudiante, $nombreArchivo) {
    $con = obtenerConexion();
    $fechaHoraActual = date('Y-m-d H:i:s');
    $sql = "UPDATE estudiantes SET archivoTFG = ?, fechaSubidaTFG = ? WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $nombreArchivo, $fechaHoraActual, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function actualizarDatosTFG($idEstudiante, $tituloTFG, $nombreArchivo = null) {
    $con = obtenerConexion();
    $fechaHoraActual = date('Y-m-d H:i:s');

    if ($nombreArchivo != null) {
        $sql = "UPDATE estudiantes SET tituloTFG = ?, archivoTFG = ?, fechaSubidaTFG = ? WHERE idEstudiante = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $tituloTFG, $nombreArchivo, $fechaHoraActual, $idEstudiante);
    } else {
        $sql = "UPDATE estudiantes SET tituloTFG = ? WHERE idEstudiante = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $tituloTFG, $idEstudiante);
    }

    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function eliminarTFG($idEstudiante) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET archivoTFG = '', fechaSubidaTFG = NULL WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function contarTFGsSubidos() {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM estudiantes WHERE archivoTFG != ''";
    $resultado = mysqli_query($con, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    $total = 0;
    if ($fila != null && $fila['total'] != null) {
        $total = intval($fila['total']);
    }
    return $total;
}

function contarTFGsDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(DISTINCT e.idEstudiante) as total FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo WHERE cp.idProfesor = ? AND e.archivoTFG != ''";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    $total = 0;
    if ($fila != null && $fila['total'] != null) {
        $total = intval($fila['total']);
    }
    return $total;
}

function listarTFGsPorProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, c.nombreCiclo, c.idCiclo FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo JOIN ciclo_profesor cp ON c.idCiclo = cp.idCiclo WHERE cp.idProfesor = ? AND e.archivoTFG != '' ORDER BY e.nombreEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaTFGs = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $listaTFGs[] = $fila;
    }
    mysqli_close($con);
    return $listaTFGs;
}

function eliminarArchivoTFG($idEstudiante) {
    $con = obtenerConexion();

    $sql = "SELECT archivoTFG FROM estudiantes WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultadoBusqueda = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultadoBusqueda);

    if ($fila != null && $fila['archivoTFG'] != null) {
        $rutaFisica = __DIR__ . "/../public/uploads/pfc/" . $fila['archivoTFG'];
        if (file_exists($rutaFisica)) {
            unlink($rutaFisica);
        }
    }

    $sql = "UPDATE estudiantes SET archivoTFG = '', fechaSubidaTFG = NULL WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);

    mysqli_close($con);
    return $resultado;
}

?>
