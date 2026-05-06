<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todos los módulos registrados
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

// Obtener los módulos que imparte un profesor específico
function obtenerModulosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM modulos JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo WHERE profesor_modulo.idProfesor = ?");
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

// Obtener módulos de un profesor dentro de un ciclo formativo concreto
function obtenerModulosDeProfesorPorCiclo($idProfesor, $idCiclo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM modulos JOIN profesor_modulo ON modulos.idModulo = profesor_modulo.idModulo JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo WHERE profesor_modulo.idProfesor = ? AND modulos.idCiclo = ?");
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

// Obtener todos los módulos pertenecientes a un ciclo formativo
function obtenerModulosPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM modulos WHERE idCiclo = ?");
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

// Comprobar si ya existe un módulo con el mismo nombre en el mismo ciclo
function checkModuloExistente($nombreModulo, $idCiclo, $idExcluir = null) {
    $con = obtenerConexion();
    if ($idExcluir) {
        $stmt = mysqli_prepare($con, "SELECT idModulo FROM modulos WHERE nombreModulo = ? AND idCiclo = ? AND idModulo != ?");
        mysqli_stmt_bind_param($stmt, "sii", $nombreModulo, $idCiclo, $idExcluir);
    } else {
        $stmt = mysqli_prepare($con, "SELECT idModulo FROM modulos WHERE nombreModulo = ? AND idCiclo = ?");
        mysqli_stmt_bind_param($stmt, "si", $nombreModulo, $idCiclo);
    }
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $existe = mysqli_num_rows($resultado) > 0;
    mysqli_close($con);
    return $existe;
}

// Insertar un nuevo módulo en la base de datos
function insertarModulo($nombreModulo, $idCiclo, $horasMaximas) {
    if (checkModuloExistente($nombreModulo, $idCiclo)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sii", $nombreModulo, $idCiclo, $horasMaximas);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Actualizar los datos de un módulo existente
function actualizarModulo($idModulo, $nombreModulo, $idCiclo, $horasMaximas) {
    if (checkModuloExistente($nombreModulo, $idCiclo, $idModulo)) {
        return false;
    }
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE modulos SET nombreModulo=?, idCiclo=?, horasMaximas=? WHERE idModulo=?");
    mysqli_stmt_bind_param($stmt, "siii", $nombreModulo, $idCiclo, $horasMaximas, $idModulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un módulo por su ID
function eliminarModulo($idModulo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM modulos WHERE idModulo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un módulo específico
function obtenerModuloPorId($idModulo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM modulos WHERE idModulo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosModulo = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosModulo;
}

// Obtener los IDs de los profesores que imparten un módulo
function obtenerProfesoresDeModulo($idModulo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT idProfesor FROM profesor_modulo WHERE idModulo = ?");
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

// Eliminar todas las asociaciones de profesores de un módulo
function limpiarProfesoresModulo($idModulo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM profesor_modulo WHERE idModulo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

// Calcular la suma de horas de todos los retos asociados a un módulo
function obtenerHorasTotalesRetosModulo($idModulo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT SUM(r.horasReto) as total FROM retos r JOIN modulo_reto mr ON r.idReto = mr.idReto WHERE mr.idModulo = ?");
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosSuma = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return (int)($datosSuma['total'] ?? 0);
}
