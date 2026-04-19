<?php
function obtenerConexion() {
    $bd = new mysqli("localhost", "root", "", "pfc");
    if ($bd->connect_error) {
        die('<br>Conexión fallida: ' . $bd->connect_error);
    }
    $bd->set_charset("utf8mb4");
    return $bd;
}
?>
