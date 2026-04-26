<?php
require_once("conectar.php");

// Ver directores
function listarDirectores() {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM directores ORDER BY idDirector ASC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Meter director
function insertarDirector($nom, $email, $dni, $tel, $alta, $nac = '2000-01-01', $dir = '', $ciu = '', $cp = '', $obs = '') {
    $db = obtenerConexion();
    $sql = "INSERT INTO directores (nombreDirector, emailDirector, dniDirector, telefonoDirector, fechaAltaDirector, fechaNacimientoDirector, direccionDirector, ciudadDirector, codigoPostalDirector, observacionesDirector) VALUES ('$nom', '$email', '$dni', '$tel', '$alta', '$nac', '$dir', '$ciu', '$cp', '$obs')";
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}

// Actualizar
function actualizarDirector($id, $nom, $email, $dni, $tel, $alta, $nac = '2000-01-01', $dir = '', $ciu = '', $cp = '', $obs = '') {
    $db = obtenerConexion();
    $sql = "UPDATE directores SET nombreDirector='$nom', emailDirector='$email', dniDirector='$dni', telefonoDirector='$tel', fechaAltaDirector='$alta', fechaNacimientoDirector='$nac', direccionDirector='$dir', ciudadDirector='$ciu', codigoPostalDirector='$cp', observacionesDirector='$obs' WHERE idDirector=$id";
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}

// Borrar
function eliminarDirector($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM directores WHERE idDirector = $id");
    mysqli_close($db);
    return $res;
}

// Perfil basico
function actualizarPerfilDirector($id, $nom, $email, $tel) {
    $db = obtenerConexion();
    $sql = "UPDATE directores SET nombreDirector='$nom', emailDirector='$email', telefonoDirector='$tel' WHERE idDirector=$id";
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}

// Coger por ID
function obtenerDirectorPorId($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM directores WHERE idDirector = $id");
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return $fila;
}

function actualizarPasswordDirector($id, $pass) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "UPDATE directores SET password = '$pass' WHERE idDirector = $id");
    mysqli_close($db);
    return $res;
}
?>