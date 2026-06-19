<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

// Devuelve la etiqueta legible de una planta (0 = planta baja).
function etiquetaPlanta($planta) {
    $planta = (int)$planta;
    return $planta === 0 ? 'Planta Baja' : 'Planta ' . $planta;
}

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarAulas() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aulas ORDER BY planta ASC, numero ASC";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

// Solo devuelve aulas activas (para desplegables de asignación).
function listarAulasActivas() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aulas WHERE activa = 1 ORDER BY planta ASC, numero ASC";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerAulaPorId($idAula) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM aulas WHERE idAula = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idAula);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarAula($planta, $numero, $nombre, $tipo, $capacidad, $activa) {
    $con = obtenerConexion();
    $sql = "INSERT INTO aulas (planta, numero, nombreAula, tipoAula, capacidad, activa) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iissii", $planta, $numero, $nombre, $tipo, $capacidad, $activa);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarAula($idAula, $planta, $numero, $nombre, $tipo, $capacidad, $activa) {
    $con = obtenerConexion();
    $sql = "UPDATE aulas SET planta = ?, numero = ?, nombreAula = ?, tipoAula = ?, capacidad = ?, activa = ? WHERE idAula = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iissiii", $planta, $numero, $nombre, $tipo, $capacidad, $activa, $idAula);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarAula($idAula) {
    $con = obtenerConexion();
    $sql = "DELETE FROM aulas WHERE idAula = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idAula);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

// Excluye $idExcluir para no marcar como duplicado al editar el propio registro.
function checkAulaExistente($planta, $numero, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idAula FROM aulas WHERE planta = ? AND numero = ? AND idAula != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $planta, $numero, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado) !== null;
}
