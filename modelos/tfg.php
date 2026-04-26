<?php
require_once("conectar.php");

// Ver todos los TFGs
function listarTodosLosTFGs() {
    $db = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, c.nombreCiclo, c.idCiclo FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE e.archivoTFG != '' ORDER BY e.nombreEstudiante ASC";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Filtrar por ciclo
function listarTFGsFiltrados($id) {
    $db = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, e.archivoTFG, e.fechaSubidaTFG, c.nombreCiclo, c.idCiclo FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE e.archivoTFG != '' AND e.idCiclo = $id ORDER BY e.nombreEstudiante ASC";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Ver por alumno
function obtenerTFGporEstudiante($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT idEstudiante, nombreEstudiante, archivoTFG, fechaSubidaTFG FROM estudiantes WHERE idEstudiante = $id");
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return $fila;
}

// Subir archivo
function actualizarTFG($id, $file) {
    $db = obtenerConexion();
    $now = date('Y-m-d H:i:s');
    $res = mysqli_query($db, "UPDATE estudiantes SET archivoTFG = '$file', fechaSubidaTFG = '$now' WHERE idEstudiante = $id");
    mysqli_close($db);
    return $res;
}

// Quitar archivo
function eliminarTFG($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "UPDATE estudiantes SET archivoTFG = '', fechaSubidaTFG = NULL WHERE idEstudiante = $id");
    mysqli_close($db);
    return $res;
}

// Ver cuantos hay
function contarTFGsSubidos() {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT COUNT(*) as t FROM estudiantes WHERE archivoTFG != ''");
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['t']) ? $fila['t'] : 0;
}
?>