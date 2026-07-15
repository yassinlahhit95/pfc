<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarModulos() {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo, ciclos.idNivel, niveles.nombreNivel
            FROM modulos
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo
            JOIN niveles ON ciclos.idNivel = niveles.idNivel
            WHERE ciclos.activo = 1
            ORDER BY idModulo ASC";
    $resultado = mysqli_query($con, $sql);
    if (!$resultado) return [];
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarModulosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo
            FROM modulos
            JOIN modulo_profesor ON modulos.idModulo = modulo_profesor.idModulo
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo
            WHERE modulo_profesor.idProfesor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarModulosDeProfesorPorCiclo($idProfesor, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo
            FROM modulos
            JOIN modulo_profesor ON modulos.idModulo = modulo_profesor.idModulo
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo
            WHERE modulo_profesor.idProfesor = ? AND modulos.idCiclo = ?
            ORDER BY modulos.nombreModulo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarModulosDeCicloConNombre($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT modulos.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo
            FROM modulos
            JOIN ciclos ON modulos.idCiclo = ciclos.idCiclo
            WHERE modulos.idCiclo = ?
            ORDER BY modulos.nombreModulo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

function moduloPerteneceACiclo($idModulo, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM modulos WHERE idModulo = ? AND idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idModulo, $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}

function listarModulosPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idCiclo = ? ORDER BY nombreModulo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerModuloPorId($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM modulos WHERE idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res);
}

function listarProfesoresDeModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT idProfesor FROM modulo_profesor WHERE idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila['idProfesor'];
    }
    return $lista;
}

function listarNombresProfesoresDeModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT p.nombreProfesor
            FROM profesores p
            JOIN modulo_profesor pm ON p.idProfesor = pm.idProfesor
            WHERE pm.idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila['nombreProfesor'];
    }
    return $lista;
}

// Profesores de varios módulos a la vez, agrupados por idModulo => [['idProfesor','nombreProfesor'], ...].
// Evita el patrón N+1 de llamar listarProfesoresDeModulo()/listarNombresProfesoresDeModulo()
// una vez por módulo en las vistas de listado (verModulos.php).
function listarProfesoresPorModulos(array $idsModulos): array {
    if (!$idsModulos) return [];
    $con = obtenerConexion();
    $ph = implode(',', array_fill(0, count($idsModulos), '?'));
    $types = str_repeat('i', count($idsModulos));
    $sql = "SELECT pm.idModulo, p.idProfesor, p.nombreProfesor
            FROM modulo_profesor pm
            JOIN profesores p ON p.idProfesor = pm.idProfesor
            WHERE pm.idModulo IN ($ph)
            ORDER BY p.idProfesor ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$idsModulos);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $porModulo = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $porModulo[$fila['idModulo']][] = ['idProfesor' => (int)$fila['idProfesor'], 'nombreProfesor' => $fila['nombreProfesor']];
    }
    return $porModulo;
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarModulo($nombreModulo, $idCiclo, $horasMaximas, $cursoAnio = null, $creditosECTS = null, $tipoModulo = 'Específico', $codigoModulo = null) {
    $con = obtenerConexion();
    $sql = "INSERT INTO modulos (nombreModulo, codigoModulo, idCiclo, horasMaximas, cursoAnio, creditosECTS, tipoModulo) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssiisis", $nombreModulo, $codigoModulo, $idCiclo, $horasMaximas, $cursoAnio, $creditosECTS, $tipoModulo);
    if (mysqli_stmt_execute($stmt)) {
        return (int)mysqli_insert_id($con);
    }
    return 0;
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarModulo($idModulo, $nombreModulo, $idCiclo, $horasMaximas, $cursoAnio = null, $creditosECTS = null, $tipoModulo = 'Específico', $codigoModulo = null) {
    $con = obtenerConexion();
    $sql = "UPDATE modulos SET nombreModulo=?, codigoModulo=?, idCiclo=?, horasMaximas=?, cursoAnio=?, creditosECTS=?, tipoModulo=? WHERE idModulo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssiisisi", $nombreModulo, $codigoModulo, $idCiclo, $horasMaximas, $cursoAnio, $creditosECTS, $tipoModulo, $idModulo);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function limpiarProfesoresModulo($idModulo) {
    $con = obtenerConexion();
    $sql = "DELETE FROM modulo_profesor WHERE idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function checkModuloExistente($nombreModulo, $idCiclo, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idModulo FROM modulos WHERE nombreModulo = ? AND idCiclo = ? AND idModulo != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sii", $nombreModulo, $idCiclo, $idExcluir);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}
