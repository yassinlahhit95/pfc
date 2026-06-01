<?php
require_once __DIR__ . "/conectar.php";

// saca todos los modulos con su ciclo
function listarModulos() {
    $con = obtenerConexion();
    $sql1 = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo, ciclos.idNivel
            FROM modulos
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo
            ORDER BY idModulo ASC";

    $resultado = mysqli_query($con, $sql1);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaModulos[] = $fila;
    }
    
    return $listaModulos;
}

function listarModulosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql1 = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM modulos JOIN modulo_profesor ON modulos.idModulo = modulo_profesor.idModulo JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo WHERE modulo_profesor.idProfesor = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idProfesor);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($res)) {
        $listaModulos[] = $fila;
    }
    
    return $listaModulos;
}

function listarModulosDeProfesorPorCiclo($idProfesor, $idCiclo) {
    $con = obtenerConexion();
    $sql1 = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo FROM modulos JOIN modulo_profesor ON modulos.idModulo = modulo_profesor.idModulo JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo WHERE modulo_profesor.idProfesor = ? AND modulos.idCiclo = ? ORDER BY modulos.nombreModulo ASC";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "ii", $idProfesor, $idCiclo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($res)) {
        $listaModulos[] = $fila;
    }
    
    return $listaModulos;
}

function listarModulosPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql1 = "SELECT * FROM modulos WHERE idCiclo = ? ORDER BY nombreModulo ASC";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idCiclo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $listaModulos = [];
    while($fila = mysqli_fetch_assoc($res)) {
        $listaModulos[] = $fila;
    }
    
    return $listaModulos;
}

function checkModuloExistente($nombreModulo, $idCiclo, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql1 = "SELECT idModulo FROM modulos WHERE nombreModulo = ? AND idCiclo = ? AND idModulo != ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "sii", $nombreModulo, $idCiclo, $idExcluir);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $existe = mysqli_num_rows($res) > 0;
    
    return $existe;
}

function insertarModulo($nombreModulo, $idCiclo, $horasMaximas) {
    $con = obtenerConexion();
    $sql1 = "INSERT INTO modulos (nombreModulo, idCiclo, horasMaximas) VALUES (?, ?, ?)";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "sii", $nombreModulo, $idCiclo, $horasMaximas);
    $ok = mysqli_stmt_execute($resultado);
    
    return $ok;
}

function actualizarModulo($idModulo, $nombreModulo, $idCiclo, $horasMaximas) {
    $con = obtenerConexion();
    $sql1 = "UPDATE modulos SET nombreModulo=?, idCiclo=?, horasMaximas=? WHERE idModulo=?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "siii", $nombreModulo, $idCiclo, $horasMaximas, $idModulo);
    $ok = mysqli_stmt_execute($resultado);
    
    return $ok;
}


function obtenerModuloPorId($idModulo) {
    $con = obtenerConexion();
    $sql1 = "SELECT * FROM modulos WHERE idModulo = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $datosModulo = mysqli_fetch_assoc($res);
    
    return $datosModulo;
}

function listarProfesoresDeModulo($idModulo) {
    $con = obtenerConexion();
    $sql1 = "SELECT idProfesor FROM modulo_profesor WHERE idModulo = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $listaIdsProfesores = [];
    while($fila = mysqli_fetch_assoc($res)) {
        $listaIdsProfesores[] = $fila['idProfesor'];
    }
    
    return $listaIdsProfesores;
}

function limpiarProfesoresModulo($idModulo) {
    $con = obtenerConexion();
    $sql1 = "DELETE FROM modulo_profesor WHERE idModulo = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    $ok = mysqli_stmt_execute($resultado);
    
    return $ok;
}

function listarNombresProfesoresDeModulo($idModulo) {
    $con = obtenerConexion();
    $sql1 = "SELECT p.nombreProfesor
            FROM profesores p
            JOIN modulo_profesor pm ON p.idProfesor = pm.idProfesor
            WHERE pm.idModulo = ?";
    $resultado = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($resultado, "i", $idModulo);
    mysqli_stmt_execute($resultado);
    $res = mysqli_stmt_get_result($resultado);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila['nombreProfesor'];
    }
    
    return $lista;
}
