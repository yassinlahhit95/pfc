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
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    return $lista;
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
    // Borrado lógico: preserva la integridad referencial de los gastos existentes
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

// Nº de gastos de varias categorías a la vez => [idCategoria => total].
// Evita el patrón N+1 de llamar contarGastosPorCategoria() una vez por categoría
// en las vistas de listado (categorias.php de admin y secretaría).
function contarGastosPorCategorias(array $idsCategorias): array {
    if (!$idsCategorias) return [];
    $con = obtenerConexion();
    $ph = implode(',', array_fill(0, count($idsCategorias), '?'));
    $types = str_repeat('i', count($idsCategorias));
    $sql = "SELECT idCategoria, COUNT(*) AS total FROM gastos WHERE idCategoria IN ($ph) GROUP BY idCategoria";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$idsCategorias);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $totales = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $totales[$fila['idCategoria']] = (int)$fila['total'];
    }
    return $totales;
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
                   c.nombreCiclo, c.abreviaturaCiclo,
                   COALESCE(d.nombreDirector, s.nombreSecretaria) AS nombreCreador
            FROM gastos g
            JOIN categorias_gasto cg ON g.idCategoria = cg.idCategoria
            LEFT JOIN ciclos c ON g.idCiclo = c.idCiclo
            LEFT JOIN directores d ON g.creadoPorRol = 'director' AND g.creadoPorId = d.idDirector
            LEFT JOIN secretarias s ON g.creadoPorRol = 'secretaria' AND g.creadoPorId = s.idSecretaria
            WHERE " . implode(' AND ', $where) . "
            ORDER BY g.fecha DESC, g.idGasto DESC";

    $stmt = mysqli_prepare($con, $sql);
    if ($params) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res   = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
    return $lista;
}

function obtenerGastoPorId($id) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT g.*, cg.nombre AS nombreCategoria, cg.color,
                c.nombreCiclo, c.abreviaturaCiclo,
                COALESCE(d.nombreDirector, s.nombreSecretaria) AS nombreCreador
         FROM gastos g
         JOIN categorias_gasto cg ON g.idCategoria = cg.idCategoria
         LEFT JOIN ciclos c ON g.idCiclo = c.idCiclo
         LEFT JOIN directores d ON g.creadoPorRol = 'director' AND g.creadoPorId = d.idDirector
         LEFT JOIN secretarias s ON g.creadoPorRol = 'secretaria' AND g.creadoPorId = s.idSecretaria
         WHERE g.idGasto = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// ══════════════════════════════════════════════════════════════════════
// GASTOS — ESCRITURA
// ══════════════════════════════════════════════════════════════════════

function insertarGasto($idCategoria, $idCiclo, $concepto, $importe, $fecha,
                        $tipoJustificante, $numeroReferencia, $archivoJustificante, $observaciones,
                        $creadoPorId = null, $creadoPorRol = null) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO gastos
            (idCategoria, idCiclo, concepto, importe, fecha,
             tipoJustificante, numeroReferencia, archivoJustificante, observaciones, creadoPorId, creadoPorRol)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iisdsssssis",
        $idCategoria, $idCiclo, $concepto, $importe, $fecha,
        $tipoJustificante, $numeroReferencia, $archivoJustificante, $observaciones, $creadoPorId, $creadoPorRol);
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
    // Obtener el nombre del fichero para borrarlo físicamente tras el borrado en BD
    $stmt = mysqli_prepare($con, "SELECT archivoJustificante FROM gastos WHERE idGasto = ?");
    mysqli_stmt_bind_param($stmt, "i", $idGasto);
    mysqli_stmt_execute($stmt);
    $row  = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    $archivo = $row['archivoJustificante'] ?? null;

    $stmt2 = mysqli_prepare($con, "DELETE FROM gastos WHERE idGasto = ?");
    mysqli_stmt_bind_param($stmt2, "i", $idGasto);
    if (mysqli_stmt_execute($stmt2)) {
        if ($archivo) {
            require_once __DIR__ . "/../include/R2Client.php";
            $archivos = json_decode($archivo, true);
            $archivos = is_array($archivos) ? $archivos : [$archivo]; // compatibilidad con el formato anterior (string suelto)
            foreach ($archivos as $nombreArchivo) {
                $ruta = __DIR__ . "/../public/uploads/justificantes/" . $nombreArchivo;
                if (file_exists($ruta)) { @unlink($ruta); }
                R2Client::deleteObject('justificantes/' . $nombreArchivo);
            }
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
    while ($fila = mysqli_fetch_assoc($res)) { $lista[] = $fila; }
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

