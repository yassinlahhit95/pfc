<?php
require_once("conectar.php");

// Ver prestamos
function listarTodosLosPrestamos() {
    $db = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante, dispositivos.nombreDispositivo as nombreArticulo, dispositivos.idDispositivo as idArticulo 
            FROM prestamos 
            JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante 
            JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie 
            ORDER BY idPrestamo DESC";
            
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Ver inventario
function listarArticulos() {
    $db = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, numeroSerie, estadoDispositivo as estado 
            FROM dispositivos 
            ORDER BY idDispositivo ASC";
            
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

function listarPrestamosActivos() {
    $db = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante, dispositivos.nombreDispositivo as nombreArticulo 
            FROM prestamos 
            JOIN estudiantes ON prestamos.idEstudiante = estudiantes.idEstudiante 
            JOIN dispositivos ON prestamos.numeroSerie = dispositivos.numeroSerie 
            WHERE prestamos.estadoPrestamo = 'en curso' 
            ORDER BY idPrestamo DESC";
            
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Meter aparato
function insertarArticulo($nom, $serie) {
    $db = obtenerConexion();
    $sM = strtoupper($serie);
    $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) 
            VALUES ('$nom', '$sM', 'disponible')";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Borrar
function eliminarArticulo($id) {
    $db = obtenerConexion();
    $sql = "DELETE FROM dispositivos WHERE idDispositivo = $id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Prestar
function registrarPrestamo($idEst, $idArt, $fec) {
    $db = obtenerConexion();
    
    $sqlGet = "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = $idArt";
    $res = mysqli_query($db, $sqlGet);
    $fila = mysqli_fetch_assoc($res);
    $serie = $fila['numeroSerie'];

    $sqlIns = "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) 
               VALUES ($idEst, '$serie', '$fec', 'en curso')";
    mysqli_query($db, $sqlIns);
    
    $sqlUpd = "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = $idArt";
    $resultado = mysqli_query($db, $sqlUpd);
    
    mysqli_close($db);
    return $resultado;
}

// Devolver
function devolverPrestamo($id) {
    $db = obtenerConexion();
    
    $sqlGet = "SELECT numeroSerie FROM prestamos WHERE idPrestamo = $id";
    $res = mysqli_query($db, $sqlGet);
    $fila = mysqli_fetch_assoc($res);
    $serie = $fila['numeroSerie'];
    $hoy = date('Y-m-d');

    $sqlUpd1 = "UPDATE prestamos SET fechaDevolucion = '$hoy', estadoPrestamo = 'devuelto' WHERE idPrestamo = $id";
    mysqli_query($db, $sqlUpd1);
    
    $sqlUpd2 = "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = '$serie'";
    $resultado = mysqli_query($db, $sqlUpd2);
    
    mysqli_close($db);
    return $resultado;
}

// Coger por ID
function obtenerArticuloPorId($id) {
    $db = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, numeroSerie, estadoDispositivo as estado 
            FROM dispositivos 
            WHERE idDispositivo = $id";
    $resultado = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $fila;
}

// Actualizar
function actualizarArticulo($id, $nom, $serie, $estado) {
    $db = obtenerConexion();
    $sql = "UPDATE dispositivos SET nombreDispositivo='$nom', numeroSerie='$serie', estadoDispositivo='$estado' WHERE idDispositivo=$id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}
?>