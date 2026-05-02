<?php
require_once __DIR__ . "/conectar.php";

// Obtener los próximos eventos (a partir de hoy)
function listarEventosProximos() {
    $con = obtenerConexion();
    $hoy = date('Y-m-d');
    
    $sql = "SELECT * FROM eventos 
            WHERE fechaEvento >= '$hoy' 
            ORDER BY fechaEvento ASC, horaEvento ASC";
            
    $resultado = mysqli_query($con, $sql);
    $listaEventos = [];
    
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaEventos[] = $fila; 
    }
    
    mysqli_close($con);
    return $listaEventos;
}

// Insertar un nuevo evento en el calendario
function insertarEvento($titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    
    $sql = "INSERT INTO eventos (tituloEvento, descripcionEvento, fechaEvento, horaEvento, ubicacionEvento) 
            VALUES ('$titulo', '$descripcion', '$fecha', '$hora', '$ubicacion')";
            
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Eliminar un evento por su ID
function eliminarEvento($idEvento) {
    $con = obtenerConexion();
    $sql = "DELETE FROM eventos WHERE idEvento = $idEvento";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener los datos de un evento específico
function obtenerEventoPorId($idEvento) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM eventos WHERE idEvento = $idEvento";
    $resultado = mysqli_query($con, $sql);
    $evento = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $evento;
}

// Actualizar los datos de un evento existente
function actualizarEvento($idEvento, $titulo, $descripcion, $fecha, $hora, $ubicacion) {
    $con = obtenerConexion();
    
    $sql = "UPDATE eventos 
            SET tituloEvento='$titulo', descripcionEvento='$descripcion', fechaEvento='$fecha', 
                horaEvento='$hora', ubicacionEvento='$ubicacion' 
            WHERE idEvento=$idEvento";
            
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}
