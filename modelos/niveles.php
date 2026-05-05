<?php
require_once __DIR__ . "/conectar.php";

// Obtener la lista de todos los niveles educativos registrados
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

// Eliminar un nivel educativo por su nombre (Uso administrativo)
function borrarNivelPorNombre($nombreNivel) {
    $con = obtenerConexion();
    $sql = "DELETE FROM niveles WHERE nombreNivel = '$nombreNivel'";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

