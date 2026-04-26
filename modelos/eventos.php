<?php
require_once("conectar.php");

// Proximos eventos
function listarEventosProximos() {
    $db = obtenerConexion();
    $hoy = date('Y-m-d');
    $res = mysqli_query($db, "SELECT * FROM eventos WHERE fechaEvento >= '$hoy' ORDER BY fechaEvento ASC, horaEvento ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Meter evento
function insertarEvento($tit, $desc, $fec, $hora, $ubi) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "INSERT INTO eventos (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento) VALUES ('$tit', '$desc', '$fec', '$hora', '$ubi')");
    mysqli_close($db);
    return $res;
}

// Borrar
function eliminarEvento($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM eventos WHERE idEvento = $id");
    mysqli_close($db);
    return $res;
}

// Coger por ID
function obtenerEventoPorId($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM eventos WHERE idEvento = $id");
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return $fila;
}

// Actualizar
function actualizarEvento($id, $tit, $desc, $fec, $hora, $ubi) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "UPDATE eventos SET tituloEvento='$tit', descripcionEvento='$desc', fechaEvento='$fec', horaEvento='$hora', ubicacionEvento='$ubi' WHERE idEvento=$id");
    mysqli_close($db);
    return $res;
}
?>