<?php
require_once("conectar.php");

// Ver niveles
function listarNiveles() {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM niveles ORDER BY idNivel ASC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Borrar nivel por nombre
function borrarNivelPorNombre($nom) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM niveles WHERE nombreNivel = '$nom'");
    mysqli_close($db);
    return $res;
}
?>