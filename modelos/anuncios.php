<?php
require_once("conectar.php");

// Ver avisos
function listarTodosLosAnuncios() {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM anuncios ORDER BY idAnuncio DESC");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) {
        $fila['tituloAnuncio'] = $fila['titulo'];
        $fila['contenidoAnuncio'] = $fila['mensaje'];
        $lista[] = $fila;
    }
    mysqli_close($db);
    return $lista;
}

// Meter aviso
function insertarAnuncio($titulo, $msj, $para = 'todos') {
    $db = obtenerConexion();
    $hoy = date('Y-m-d H:i:s');
    $exp = date('Y-m-d', strtotime('+1 month'));
    $sql = "INSERT INTO anuncios (titulo, mensaje, fechaAnuncio, fechaExpiracion, dirigidoA) VALUES ('$titulo', '$msj', '$hoy', '$exp', '$para')";
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}

// Borrar
function eliminarAnuncio($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "DELETE FROM anuncios WHERE idAnuncio = $id");
    mysqli_close($db);
    return $res;
}

// Coger por ID
function obtenerAnuncioPorId($id) {
    $db = obtenerConexion();
    $res = mysqli_query($db, "SELECT * FROM anuncios WHERE idAnuncio = $id");
    $fila = mysqli_fetch_assoc($res);
    if (isset($fila)) {
        $fila['tituloAnuncio'] = $fila['titulo'];
        $fila['contenidoAnuncio'] = $fila['mensaje'];
    }
    mysqli_close($db);
    return $fila;
}

// Actualizar
function actualizarAnuncio($id, $tit, $msj, $exp, $para) {
    $db = obtenerConexion();
    $sql = "UPDATE anuncios SET titulo='$tit', mensaje='$msj', fechaExpiracion='$exp', dirigidoA='$para' WHERE idAnuncio=$id";
    $res = mysqli_query($db, $sql);
    mysqli_close($db);
    return $res;
}

// Ver activos
function contarAnunciosQueEstanActivos() {
    $db = obtenerConexion();
    $hoy = date('Y-m-d');
    $res = mysqli_query($db, "SELECT COUNT(*) as total FROM anuncios WHERE fechaExpiracion >= '$hoy'");
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($db);
    return isset($fila['total']) ? $fila['total'] : 0;
}

// Listar por rol
function listarAnunciosPorRol($rol) {
    $db = obtenerConexion();
    $hoy = date('Y-m-d');
    $sql = "SELECT * FROM anuncios WHERE fechaExpiracion >= '$hoy' AND (dirigidoA = '$rol' OR dirigidoA = 'todos') ORDER BY idAnuncio DESC";
    $res = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Paginados
function listarAnunciosConPaginas($lim) {
    $db = obtenerConexion();
    $offset = 0;
    if (isset($_GET['p_anuncios'])) {
        $pag = (int)$_GET['p_anuncios'];
        if ($pag > 1) { $offset = ($pag - 1) * $lim; }
    }
    $res = mysqli_query($db, "SELECT * FROM anuncios ORDER BY idAnuncio DESC LIMIT $offset, $lim");
    $lista = [];
    while($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}
?>