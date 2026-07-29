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
    $sql = "SELECT d.idDispositivo AS idArticulo, d.nombreDispositivo AS nombreArticulo,
                   d.numeroSerie, d.estadoDispositivo AS estado, d.foto, d.cantidad,
                   (SELECT COUNT(*) FROM prestamos p WHERE p.idDispositivo = d.idDispositivo AND p.estadoPrestamo = 'activo' AND p.deleted_at IS NULL) AS prestados
            FROM dispositivos d
            WHERE d.deleted_at IS NULL
            ORDER BY d.idDispositivo ASC";
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
    $sql = "SELECT d.idDispositivo AS idArticulo, d.nombreDispositivo AS nombreArticulo,
                   d.numeroSerie, d.estadoDispositivo AS estado, d.foto, d.cantidad,
                   (SELECT COUNT(*) FROM prestamos p WHERE p.idDispositivo = d.idDispositivo AND p.estadoPrestamo = 'activo' AND p.deleted_at IS NULL) AS prestados
            FROM dispositivos d WHERE d.idDispositivo = ? AND d.deleted_at IS NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idArticulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarArticulo($nombreArticulo, $numeroSerie, $cantidad = 1, $foto = null) {
    $con = obtenerConexion();
    $sql = "INSERT INTO dispositivos (nombreDispositivo, numeroSerie, estadoDispositivo, cantidad, foto) VALUES (?, ?, 'disponible', ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssis", $nombreArticulo, $numeroSerie, $cantidad, $foto);
    return mysqli_stmt_execute($stmt);
}

function registrarPrestamo($idEstudiante, $idArticulo, $fechaPrestamo) {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        // Verificar que el estudiante no tenga ya un préstamo activo de este dispositivo
        $stmtCheck = mysqli_prepare($con, "
            SELECT COUNT(*) as cnt FROM prestamos
            WHERE idEstudiante = ? AND idDispositivo = ? AND estadoPrestamo = 'activo' AND deleted_at IS NULL
        ");
        mysqli_stmt_bind_param($stmtCheck, "ii", $idEstudiante, $idArticulo);
        mysqli_stmt_execute($stmtCheck);
        $checkResult = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCheck));
        if ($checkResult['cnt'] > 0) throw new \RuntimeException('El estudiante ya tiene un préstamo activo de este dispositivo');

        $stmt = mysqli_prepare($con, "
            SELECT d.cantidad,
                   (SELECT COUNT(*) FROM prestamos p WHERE p.idDispositivo = d.idDispositivo AND p.estadoPrestamo = 'activo' AND p.deleted_at IS NULL) as prestados
            FROM dispositivos d WHERE d.idDispositivo = ? AND d.deleted_at IS NULL FOR UPDATE
        ");
        mysqli_stmt_bind_param($stmt, "i", $idArticulo);
        mysqli_stmt_execute($stmt);
        $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!$fila) throw new \RuntimeException('dispositivo no encontrado');
        if ($fila['prestados'] >= $fila['cantidad']) throw new \RuntimeException('no hay stock disponible');

        $stmt2 = mysqli_prepare($con, "INSERT INTO prestamos (idEstudiante, idDispositivo, fechaPrestamo, estadoPrestamo) VALUES (?, ?, ?, 'activo')");
        mysqli_stmt_bind_param($stmt2, "iis", $idEstudiante, $idArticulo, $fechaPrestamo);
        if (!mysqli_stmt_execute($stmt2)) throw new \RuntimeException('insert prestamos');

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

function actualizarArticulo($idArticulo, $nombreArticulo, $numeroSerie, $estadoDispositivo, $cantidad = 1, $foto = null) {
    $con = obtenerConexion();
    $sql = "UPDATE dispositivos SET nombreDispositivo=?, numeroSerie=?, estadoDispositivo=?, cantidad=?, foto=COALESCE(?, foto) WHERE idDispositivo=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssisi", $nombreArticulo, $numeroSerie, $estadoDispositivo, $cantidad, $foto, $idArticulo);
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
