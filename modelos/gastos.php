<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CATEGORÍAS
// ══════════════════════════════════════════════════════════════════════

function listarCategorias() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM categorias_gasto WHERE activo = 1 ORDER BY nombre ASC";
    $res = mysqli_query($con, $sql);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) { $lista[] = $f; }
    return $lista;
}

function obtenerCategoriaPorId($id) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM categorias_gasto WHERE idCategoria = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function insertarCategoria($nombre, $presupuestoAnual, $color) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "INSERT INTO categorias_gasto (nombre, presupuestoAnual, color) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sds", $nombre, $presupuestoAnual, $color);
    return mysqli_stmt_execute($stmt) ? mysqli_insert_id($con) : false;
}

function actualizarCategoria($id, $nombre, $presupuestoAnual, $color) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE categorias_gasto SET nombre=?, presupuestoAnual=?, color=? WHERE idCategoria=?");
    mysqli_stmt_bind_param($stmt, "sdsi", $nombre, $presupuestoAnual, $color, $id);
    return mysqli_stmt_execute($stmt);
}

function borrarCategoria($id) {
    $con  = obtenerConexion();
    // Soft-delete: preserve referential integrity on existing gastos
    $stmt = mysqli_prepare($con, "UPDATE categorias_gasto SET activo = 0 WHERE idCategoria = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}

function contarGastosPorCategoria($id) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT COUNT(*) AS total FROM gastos WHERE idCategoria = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (int)($row['total'] ?? 0);
}

// ══════════════════════════════════════════════════════════════════════
// GASTOS — CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarGastos($anyo = null, $idCategoria = null, $idCiclo = null) {
    $con    = obtenerConexion();
    $params = [];
    $types  = '';
    $where  = ["1=1"];

    if ($anyo) {
        $where[]  = "YEAR(g.fecha) = ?";
        $params[] = (int)$anyo;
        $types   .= 'i';
    }
    if ($idCategoria) {
        $where[]  = "g.idCategoria = ?";
        $params[] = (int)$idCategoria;
        $types   .= 'i';
    }
    if ($idCiclo) {
        $where[]  = "g.idCiclo = ?";
        $params[] = (int)$idCiclo;
        $types   .= 'i';
    }

    $sql = "SELECT g.*, cg.nombre AS nombreCategoria, cg.color,
                   c.nombreCiclo, c.abreviaturaCiclo
            FROM gastos g
            JOIN categorias_gasto cg ON g.idCategoria = cg.idCategoria
            LEFT JOIN ciclos c ON g.idCiclo = c.idCiclo
            WHERE " . implode(' AND ', $where) . "
            ORDER BY g.fecha DESC, g.idGasto DESC";

    $stmt = mysqli_prepare($con, $sql);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res   = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) { $lista[] = $f; }
    return $lista;
}

function obtenerGastoPorId($id) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT g.*, cg.nombre AS nombreCategoria, cg.color,
                c.nombreCiclo, c.abreviaturaCiclo
         FROM gastos g
         JOIN categorias_gasto cg ON g.idCategoria = cg.idCategoria
         LEFT JOIN ciclos c ON g.idCiclo = c.idCiclo
         WHERE g.idGasto = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// ══════════════════════════════════════════════════════════════════════
// GASTOS — ESCRITURA
// ══════════════════════════════════════════════════════════════════════

function insertarGasto($idCategoria, $idCiclo, $concepto, $importe, $fecha,
                        $tipoJustificante, $numeroReferencia, $archivoJustificante, $observaciones) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO gastos
            (idCategoria, idCiclo, concepto, importe, fecha,
             tipoJustificante, numeroReferencia, archivoJustificante, observaciones)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iisdsssss",
        $idCategoria, $idCiclo, $concepto, $importe, $fecha,
        $tipoJustificante, $numeroReferencia, $archivoJustificante, $observaciones);
    return mysqli_stmt_execute($stmt) ? mysqli_insert_id($con) : false;
}

function actualizarGasto($idGasto, $idCategoria, $idCiclo, $concepto, $importe, $fecha,
                          $tipoJustificante, $numeroReferencia, $archivoJustificante, $observaciones) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE gastos SET
            idCategoria=?, idCiclo=?, concepto=?, importe=?, fecha=?,
            tipoJustificante=?, numeroReferencia=?, archivoJustificante=?, observaciones=?
         WHERE idGasto=?");
    mysqli_stmt_bind_param($stmt, "iisdsssssi",
        $idCategoria, $idCiclo, $concepto, $importe, $fecha,
        $tipoJustificante, $numeroReferencia, $archivoJustificante, $observaciones, $idGasto);
    return mysqli_stmt_execute($stmt);
}

function borrarGasto($idGasto) {
    $con  = obtenerConexion();
    // Retrieve filename to delete the physical file after DB delete
    $stmt = mysqli_prepare($con, "SELECT archivoJustificante FROM gastos WHERE idGasto = ?");
    mysqli_stmt_bind_param($stmt, "i", $idGasto);
    mysqli_stmt_execute($stmt);
    $row  = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $archivo = $row['archivoJustificante'] ?? null;

    $stmt2 = mysqli_prepare($con, "DELETE FROM gastos WHERE idGasto = ?");
    mysqli_stmt_bind_param($stmt2, "i", $idGasto);
    if (mysqli_stmt_execute($stmt2)) {
        if ($archivo) {
            $ruta = __DIR__ . "/../public/uploads/justificantes/" . $archivo;
            if (file_exists($ruta)) { @unlink($ruta); }
        }
        return true;
    }
    return false;
}

// ══════════════════════════════════════════════════════════════════════
// DASHBOARD / PRESUPUESTO
// ══════════════════════════════════════════════════════════════════════

function resumenPresupuestoPorCategoria($anyo) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT cg.idCategoria, cg.nombre, cg.presupuestoAnual, cg.color,
                COALESCE(SUM(g.importe), 0) AS gastado
         FROM categorias_gasto cg
         LEFT JOIN gastos g ON g.idCategoria = cg.idCategoria AND YEAR(g.fecha) = ?
         WHERE cg.activo = 1
         GROUP BY cg.idCategoria
         ORDER BY cg.nombre ASC");
    mysqli_stmt_bind_param($stmt, "i", $anyo);
    mysqli_stmt_execute($stmt);
    $res   = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) { $lista[] = $f; }
    return $lista;
}

function totalGastadoEnAnyo($anyo) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT COALESCE(SUM(importe), 0) AS total FROM gastos WHERE YEAR(fecha) = ?");
    mysqli_stmt_bind_param($stmt, "i", $anyo);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (float)($row['total'] ?? 0);
}

function totalGastadoEnMes($anyo, $mes) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT COALESCE(SUM(importe), 0) AS total FROM gastos WHERE YEAR(fecha)=? AND MONTH(fecha)=?");
    mysqli_stmt_bind_param($stmt, "ii", $anyo, $mes);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (float)($row['total'] ?? 0);
}

function contarGastos() {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT COUNT(*) AS total FROM gastos");
    $row = mysqli_fetch_assoc($res);
    return (int)($row['total'] ?? 0);
}
