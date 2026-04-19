<?php
require_once("conectar.php");

function obtenerNotasModulo($idEstudiante, $idModulo) {
    $conexion = obtenerConexion();
    $consulta = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    $resultado = mysqli_query($conexion, $consulta);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($conexion);
    return $fila;
}

function calificarModuloCompleto($idEstudiante, $idModulo, $n1ev, $n1f, $n2ev, $n2f) {
    $conexion = obtenerConexion();
    
    // Buscamos si ya existe para decidir si INSERT o UPDATE
    $consultaBusqueda = "SELECT idCalificacion FROM calificaciones_modulos WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    $resultadoBusqueda = mysqli_query($conexion, $consultaBusqueda);
    
    if (mysqli_num_rows($resultadoBusqueda) > 0) {
        // Modo simple: actualizamos usando comillas simples
        $consulta = "UPDATE calificaciones_modulos 
                SET nota_1ev = '$n1ev', nota_1final = '$n1f', nota_2ev = '$n2ev', nota_2final = '$n2f' 
                WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    } else {
        // Modo simple: insertamos
        $consulta = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final) 
                VALUES ($idEstudiante, $idModulo, '$n1ev', '$n1f', '$n2ev', '$n2f')";
    }
    
    $resultado = mysqli_query($conexion, $consulta);
    mysqli_close($conexion);
    return $resultado;
}
?>
