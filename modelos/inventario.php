<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarTodosLosPrestamos() {
    $con = obtenerConexion();
    $sql = "SELECT prestamos.*, estudiantes.nombreEstudiante,
                   dispositivos.nombreDispositivo AS nombreArticulo,
                   dispositivos.idDispositivo AS idArticulo
            FROM prestamos
            JOIN estudiantes  ON prestamos.idEstudiante = estudiantes.idEstudiante
            JOIN dispositivos ON prestamos.idDispositivo  = dispositivos.idDispositivo
            WHERE prestamos.deleted_at IS NULL
            ORDER BY prestamos.idPrestamo DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarArticulos() {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo AS idArticulo, nombreDispositivo AS nombreArticulo,
                   numeroSerie, estadoDispositivo AS estado, foto
            FROM dispositivos
            WHERE deleted_at IS NULL
            ORDER BY idDispositivo ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerArticuloPorId($idArticulo) {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo AS idArticulo, nombreDispositivo AS nombreArticulo,
                   numeroSerie, estadoDispositivo AS estado, foto
            FROM dispositivos WHERE idDispositivo = ? AND deleted_at IS NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarArticulo($nombreArticulo, $numeroSerie, $foto = null) {
    $con = obtenerConexion();
    $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo, foto) VALUES (?, ?, 'disponible', ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $nombreArticulo, $numeroSerie, $foto);
    return mysqli_stmt_execute($stmt);
}

function registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo) {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $stmt = mysqli_prepare($con, "SELECT idDispositivo FROM dispositivos WHERE idDispositivo = ? AND estadoDispositivo = 'disponible' AND deleted_at IS NULL");
        mysqli_stmt_bind_param($stmt, "i", $idArticulo);
        mysqli_stmt_execute($stmt);
        $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$fila) throw new \RuntimeException('dispositivo no disponible');

        $stmt2 = mysqli_prepare($con, "INSERT INTO prestamos (idEstudiante, idDispositivo, fechaPrestamo, estadoPrestamo) VALUES (?, ?, ?, 'activo')");
        mysqli_stmt_bind_param($stmt2, "iis", $idEstudiante, $idArticulo, $fechaPrestamo);
        if (!mysqli_stmt_execute($stmt2)) throw new \RuntimeException('insert prestamos');

        $stmt3 = mysqli_prepare($con, "UPDATE dispositivos SET estadoDispositivo = 'prestado' WHERE idDispositivo = ?");
        mysqli_stmt_bind_param($stmt3, "i", $idArticulo);
        if (!mysqli_stmt_execute($stmt3)) throw new \RuntimeException('update dispositivos');

        mysqli_commit($con);
        return true;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        return false;
    }
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoDispositivo, $foto = null) {
    $con = obtenerConexion();
    $sql = "UPDATE dispositivos SET nombreDispositivo=?, numeroSerie=?, estadoDispositivo=?, foto=COALESCE(?, foto) WHERE idDispositivo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $nombreArticulo, $numeroSerie, $estadoDispositivo, $foto, $idArticulo);
    return mysqli_stmt_execute($stmt);
}

function devolverPrestamo($idPrestamo) {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $stmt = mysqli_prepare($con, "SELECT idDispositivo FROM prestamos WHERE idPrestamo = ? AND deleted_at IS NULL");
        mysqli_stmt_bind_param($stmt, "i", $idPrestamo);
        mysqli_stmt_execute($stmt);
        $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$fila) throw new \RuntimeException('prestamo no encontrado');
        $idDispositivo = $fila['idDispositivo'];

        $fechaHoy = date('Y-m-d');
        $stmt2 = mysqli_prepare($con, "UPDATE prestamos SET fechaDevolucion = ?, estadoPrestamo = 'devuelto' WHERE idPrestamo = ?");
        mysqli_stmt_bind_param($stmt2, "si", $fechaHoy, $idPrestamo);
        if (!mysqli_stmt_execute($stmt2)) throw new \RuntimeException('update prestamos');

        $stmt3 = mysqli_prepare($con, "UPDATE dispositivos SET estadoDispositivo = 'disponible' WHERE idDispositivo = ?");
        mysqli_stmt_bind_param($stmt3, "i", $idDispositivo);
        if (!mysqli_stmt_execute($stmt3)) throw new \RuntimeException('update dispositivos');

        mysqli_commit($con);
        return true;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        return false;
    }
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarArticulo($idArticulo) {
    $con = obtenerConexion();
    $sql = "UPDATE dispositivos SET deleted_at = NOW() WHERE idDispositivo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function checkArticuloExistente($numeroSerie, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idDispositivo FROM dispositivos WHERE numeroSerie = ? AND idDispositivo != ? AND deleted_at IS NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $numeroSerie, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($resultado) > 0;
}

// ══════════════════════════════════════════════════════════════════════
// INVENTARIO CRUD (generic inventory items, separate from devices)
// ══════════════════════════════════════════════════════════════════════

function listarInventario($limite = 100, $offset = 0) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM inventario
            WHERE deleted_at IS NULL
            ORDER BY nombreArticulo ASC
            LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $limite, $offset);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerInventarioPorId($idInventario) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM inventario WHERE idInventario = ? AND deleted_at IS NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idInventario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

function crearInventario($nombreArticulo, $descripcion = null, $cantidad = 0) {
    $con = obtenerConexion();
    $sql = "INSERT INTO inventario (nombreArticulo, descripcion, cantidad) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $nombreArticulo, $descripcion, $cantidad);
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($con);
    }
    return false;
}

function actualizarInventario($idInventario, $nombreArticulo, $descripcion = null, $cantidad = null) {
    $con = obtenerConexion();
    if ($cantidad === null) {
        $sql = "UPDATE inventario SET nombreArticulo = ?, descripcion = ?, updated_at = NOW() WHERE idInventario = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $nombreArticulo, $descripcion, $idInventario);
    } else {
        $sql = "UPDATE inventario SET nombreArticulo = ?, descripcion = ?, cantidad = ?, updated_at = NOW() WHERE idInventario = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssii", $nombreArticulo, $descripcion, $cantidad, $idInventario);
    }
    return mysqli_stmt_execute($stmt);
}

function eliminarInventario($idInventario) {
    $con = obtenerConexion();
    $sql = "UPDATE inventario SET deleted_at = NOW() WHERE idInventario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idInventario);
    return mysqli_stmt_execute($stmt);
}

function actualizarCantidadInventario($idInventario, $cantidad) {
    $con = obtenerConexion();
    $sql = "UPDATE inventario SET cantidad = ? WHERE idInventario = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $cantidad, $idInventario);
    return mysqli_stmt_execute($stmt);
}
