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
    $stmt = mysqli_prepare($con, "DELETE FROM niveles WHERE nombreNivel = ?");
    mysqli_stmt_bind_param($stmt, "s", $nombreNivel);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}
