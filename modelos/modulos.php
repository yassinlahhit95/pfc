<?php
require_once __DIR__ . "/conectar.php";

function listarModulos() {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo
            FROM modulos
            LEFT JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo
            ORDER BY idModulo ASC";

    $resultado = mysqli_query($con, $sql);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaModulos[] = $fila;
    }
    mysqli_close($con);
    return $listaModulos;
}

function obtenerModulosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM modulos JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo WHERE profesor_modulo.idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaModulos[] = $fila;
    }
    mysqli_close($con);
    return $listaModulos;
}

function obtenerModulosDeProfesorPorCiclo($idProfesor, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM modulos JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo WHERE profesor_modulo.idProfesor = ? AND modulos.idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaModulos[] = $fila;
    }
    mysqli_close($con);
    return $listaModulos;
}

function obtenerModulosPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaModulos[] = $fila;
    }
    mysqli_close($con);
    return $listaModulos;
}

function checkModuloExistente($nombreModulo, $idCiclo, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idModulo FROM modulos WHERE nombreModulo = ? AND idCiclo = ? AND idModulo != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $nombreModulo, $idCiclo, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

function insertarModulo($nombreModulo, $idCiclo, $horasMaximas) {
    if (checkModuloExistente($nombreModulo, $idCiclo)) {
        return false;
    }
    $con = obtenerConexion();
    $sql = "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $nombreModulo, $idCiclo, $horasMaximas);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function actualizarModulo($idModulo, $nombreModulo, $idCiclo, $horasMaximas) {
    if (checkModuloExistente($nombreModulo, $idCiclo, $idModulo)) {
        return false;
    }
    $con = obtenerConexion();
    $sql = "UPDATE modulos SET nombreModulo=?, idCiclo=?, horasMaximas=? WHERE idModulo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "siii", $nombreModulo, $idCiclo, $horasMaximas, $idModulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function eliminarModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM modulos WHERE idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerModuloPorId($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosModulo = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosModulo;
}

function obtenerProfesoresDeModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT idProfesor FROM profesor_modulo WHERE idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaIdsProfesores = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaIdsProfesores[] = $fila['idProfesor'];
    }
    mysqli_close($con);
    return $listaIdsProfesores;
}

function limpiarProfesoresModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM profesor_modulo WHERE idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function obtenerHorasTotalesRetosModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT SUM(r.horasReto) as total FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto WHERE mr.idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosSuma = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($datosSuma['total'] ?? 0);
}
?>
