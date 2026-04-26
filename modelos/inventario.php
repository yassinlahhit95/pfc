<?php
require_once("conectar.php");

// Ver prestamos
function listarTodosLosPrestamos() {
    $db = obtenerConexion();
    $sql = "SELECT p.*, e.nombreEstudiante, d.nombreDispositivo as nombreArticulo, d.idDispositivo as idArticulo FROM prestamos p JOIN estudiantes e ON p.idEstudiante = e.idEstudiante JOIN dispositivos d ON p.numeroSerie = d.numeroSerie ORDER BY idPrestamo DESC";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Ver inventario
function listarArticulos() {
    $db = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, numeroSerie, estadoDispositivo as estado FROM dispositivos ORDER BY idDispositivo ASC";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

function listarPrestamosActivos() {
    $db = obtenerConexion();
    $sql = "SELECT p.*, e.nombreEstudiante, d.nombreDispositivo as nombreArticulo FROM prestamos p JOIN estudiantes e ON p.idEstudiante = e.idEstudiante JOIN dispositivos d ON p.numeroSerie = d.numeroSerie WHERE p.estadoPrestamo = 'en curso' ORDER BY idPrestamo DESC";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Meter aparato
function insertarArticulo($nom, $serie) {
    $db = obtenerConexion();
    $sM = strtoupper($serie);
    $res = mysqli_query($db, "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo) VALUES ('$nom', '$sM', 'disponible')");
    mysqli_close($db);
    return $res;
}

// Borrar
function eliminarArticulo($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM dispositivos WHERE idDispositivo = $id");
    mysqli_close($db);
    return $res;
}

// Prestar
function registrarPrestamo($idEst, $idArt, $fec) {
    $db = obtenerConexion();
    $f = mysqli_fetch_assoc(mysqli_query($db, "SELECT numeroSerie FROM dispositivos WHERE idDispositivo = $idArt"));
    $serie = $f['numeroSerie'];

    mysqli_query($db, "INSERT INTO prestamos (idEstudiante, numeroSerie, fechaPrestamo, estadoPrestamo) VALUES ($idEst, '$serie', '$fec', 'en curso')");
    $res = mysqli_query($db, "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = $idArt");
    mysqli_close($db);
    return $res;
}

// Devolver
function devolverPrestamo($id) {
    $db = obtenerConexion();
    $f = mysqli_fetch_assoc(mysqli_query($db, "SELECT numeroSerie FROM prestamos WHERE idPrestamo = $id"));
    $serie = $f['numeroSerie'];
    $hoy = date('Y-m-d');

    mysqli_query($db, "UPDATE prestamos SET fechaDevolucion = '$hoy', estadoPrestamo = 'devuelto' WHERE idPrestamo = $id");
    $res = mysqli_query($db, "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE numeroSerie = '$serie'");
    mysqli_close($db);
    return $res;
}

// Coger por ID
function obtenerArticuloPorId($id) {
    $db = obtenerConexion();
    $sql = "SELECT idDispositivo as idArticulo, nombreDispositivo as nombreArticulo, numeroSerie, estadoDispositivo as estado FROM dispositivos WHERE idDispositivo = $id";
    $res = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return $fila;
}

// Actualizar
function actualizarArticulo($id, $nom, $serie, $estado) {
    $db = obtenerConexion();
    $sql = "UPDATE dispositivos SET nombreDispositivo='$nom', numeroSerie='$serie', estadoDispositivo='$estado' WHERE idDispositivo=$id";
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}
?>