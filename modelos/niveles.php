<?php
require_once __DIR__ . "/conectar.php";

function listarNiveles() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM niveles ORDER BY idNivel ASC";
    $resultado = mysqli_query($con, $sql);

    $listaNiveles = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaNiveles[] = $fila;
    }
    mysqli_close($con);
    return $listaNiveles;
}

?>
