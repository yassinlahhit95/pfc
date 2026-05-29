<?php
require_once __DIR__ . "/conectar.php";

// ── CARPETAS ──────────────────────────────────────────────

function listarCarpetasPorProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT c.*, ci.nombreCiclo,
                   (SELECT COUNT(*) FROM ejercicios e WHERE e.idCarpeta = c.idCarpeta) as totalEjercicios
            FROM carpetas_ejercicios c
            JOIN ciclos ci ON c.idCiclo = ci.idCiclo
            WHERE c.idProfesor = ?
            ORDER BY c.fechaCreacion DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    mysqli_close($con);
    return $lista;
}

function listarCarpetasPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT c.*, p.nombreProfesor,
                   (SELECT COUNT(*) FROM ejercicios e WHERE e.idCarpeta = c.idCarpeta AND e.publicado = 1) as totalEjercicios
            FROM carpetas_ejercicios c
            JOIN profesores p ON c.idProfesor = p.idProfesor
            WHERE c.idCiclo = ?
            ORDER BY c.fechaCreacion DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    mysqli_close($con);
    return $lista;
}

function obtenerCarpetaPorId($idCarpeta) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM carpetas_ejercicios WHERE idCarpeta = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarpeta);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($con);
    return $fila;
}

function insertarCarpeta($nombre, $descripcion, $color, $icono, $idProfesor, $idCiclo) {
    $con = obtenerConexion();
    $sql = "INSERT INTO carpetas_ejercicios (nombre, descripcion, color, icono, idProfesor, idCiclo) VALUES (?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssii", $nombre, $descripcion, $color, $icono, $idProfesor, $idCiclo);
    $ok = mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($con);
    mysqli_close($con);
    return $ok ? $id : false;
}

function borrarCarpeta($idCarpeta) {
    $con = obtenerConexion();
    $sql = "DELETE FROM carpetas_ejercicios WHERE idCarpeta = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCarpeta);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

// ── EJERCICIOS ────────────────────────────────────────────

function listarEjerciciosPorProfesor($idProfesor, $idCarpeta = 0) {
    $con = obtenerConexion();
    if ($idCarpeta > 0) {
        $sql = "SELECT e.*, c.nombre as nombreCarpeta, c.color as colorCarpeta,
                       (SELECT COUNT(*) FROM entregas_ejercicios en WHERE en.idEjercicio = e.idEjercicio) as totalEntregas
                FROM ejercicios e
                LEFT JOIN carpetas_ejercicios c ON e.idCarpeta = c.idCarpeta
                WHERE e.idProfesor = ? AND e.idCarpeta = ?
                ORDER BY e.fechaCreacion DESC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idCarpeta);
    } else {
        $sql = "SELECT e.*, c.nombre as nombreCarpeta, c.color as colorCarpeta,
                       (SELECT COUNT(*) FROM entregas_ejercicios en WHERE en.idEjercicio = e.idEjercicio) as totalEntregas
                FROM ejercicios e
                LEFT JOIN carpetas_ejercicios c ON e.idCarpeta = c.idCarpeta
                WHERE e.idProfesor = ?
                ORDER BY e.fechaCreacion DESC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idProfesor);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    mysqli_close($con);
    return $lista;
}

function listarEjerciciosPorCiclo($idCiclo, $idCarpeta = 0) {
    $con = obtenerConexion();
    if ($idCarpeta > 0) {
        $sql = "SELECT e.*, c.nombre as nombreCarpeta, c.color as colorCarpeta, p.nombreProfesor
                FROM ejercicios e
                LEFT JOIN carpetas_ejercicios c ON e.idCarpeta = c.idCarpeta
                JOIN profesores p ON e.idProfesor = p.idProfesor
                WHERE e.idCiclo = ? AND e.publicado = 1 AND e.idCarpeta = ?
                ORDER BY e.fechaCreacion DESC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $idCiclo, $idCarpeta);
    } else {
        $sql = "SELECT e.*, c.nombre as nombreCarpeta, c.color as colorCarpeta, p.nombreProfesor
                FROM ejercicios e
                LEFT JOIN carpetas_ejercicios c ON e.idCarpeta = c.idCarpeta
                JOIN profesores p ON e.idProfesor = p.idProfesor
                WHERE e.idCiclo = ? AND e.publicado = 1
                ORDER BY e.fechaCreacion DESC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    mysqli_close($con);
    return $lista;
}

function obtenerEjercicioPorId($idEjercicio) {
    $con = obtenerConexion();
    $sql = "SELECT e.*, c.nombre as nombreCarpeta, c.color as colorCarpeta, p.nombreProfesor
            FROM ejercicios e
            LEFT JOIN carpetas_ejercicios c ON e.idCarpeta = c.idCarpeta
            JOIN profesores p ON e.idProfesor = p.idProfesor
            WHERE e.idEjercicio = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEjercicio);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($con);
    return $fila;
}

function insertarEjercicio($titulo, $descripcion, $idCarpeta, $idProfesor, $idCiclo, $fechaLimite, $archivoAdjunto) {
    $con = obtenerConexion();
    $sql = "INSERT INTO ejercicios (titulo, descripcion, idCarpeta, idProfesor, idCiclo, fechaLimite, archivoAdjunto)
            VALUES (?,?,?,?,?,?,?)";
    $stmt = mysqli_prepare($con, $sql);
    $idCarp = $idCarpeta ?: null;
    $fecha  = $fechaLimite ?: null;
    $arch   = $archivoAdjunto ?: null;
    mysqli_stmt_bind_param($stmt, "ssiisis", $titulo, $descripcion, $idCarp, $idProfesor, $idCiclo, $fecha, $arch);
    $ok = mysqli_stmt_execute($stmt);
    $id = mysqli_insert_id($con);
    mysqli_close($con);
    return $ok ? $id : false;
}

function actualizarEjercicio($idEjercicio, $titulo, $descripcion, $idCarpeta, $fechaLimite, $publicado) {
    $con = obtenerConexion();
    $sql = "UPDATE ejercicios SET titulo=?, descripcion=?, idCarpeta=?, fechaLimite=?, publicado=? WHERE idEjercicio=?";
    $stmt = mysqli_prepare($con, $sql);
    $idCarp = $idCarpeta ?: null;
    $fecha  = $fechaLimite ?: null;
    mysqli_stmt_bind_param($stmt, "ssissi", $titulo, $descripcion, $idCarp, $fecha, $publicado, $idEjercicio);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function borrarEjercicio($idEjercicio) {
    $con = obtenerConexion();
    $sql = "DELETE FROM ejercicios WHERE idEjercicio = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEjercicio);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

// ── ENTREGAS ──────────────────────────────────────────────

function listarEntregasPorEjercicio($idEjercicio) {
    $con = obtenerConexion();
    $sql = "SELECT en.*, e.nombreEstudiante, e.emailEstudiante
            FROM entregas_ejercicios en
            JOIN estudiantes e ON en.idEstudiante = e.idEstudiante
            WHERE en.idEjercicio = ?
            ORDER BY en.fechaEntrega DESC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEjercicio);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) $lista[] = $f;
    mysqli_close($con);
    return $lista;
}

function obtenerEntregaPorEstudiante($idEjercicio, $idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM entregas_ejercicios WHERE idEjercicio = ? AND idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEjercicio, $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($con);
    return $fila;
}

function insertarOActualizarEntrega($idEjercicio, $idEstudiante, $respuesta, $archivoEntrega) {
    $con = obtenerConexion();
    $existing = "SELECT idEntrega FROM entregas_ejercicios WHERE idEjercicio = ? AND idEstudiante = ?";
    $s = mysqli_prepare($con, $existing);
    mysqli_stmt_bind_param($s, "ii", $idEjercicio, $idEstudiante);
    mysqli_stmt_execute($s);
    mysqli_stmt_store_result($s);

    if (mysqli_stmt_num_rows($s) > 0) {
        if ($archivoEntrega) {
            $sql = "UPDATE entregas_ejercicios SET respuesta=?, archivoEntrega=?, fechaEntrega=NOW(), estado='entregado' WHERE idEjercicio=? AND idEstudiante=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "ssii", $respuesta, $archivoEntrega, $idEjercicio, $idEstudiante);
        } else {
            $sql = "UPDATE entregas_ejercicios SET respuesta=?, fechaEntrega=NOW(), estado='entregado' WHERE idEjercicio=? AND idEstudiante=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "sii", $respuesta, $idEjercicio, $idEstudiante);
        }
    } else {
        $sql = "INSERT INTO entregas_ejercicios (idEjercicio, idEstudiante, respuesta, archivoEntrega) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iiss", $idEjercicio, $idEstudiante, $respuesta, $archivoEntrega);
    }
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function calificarEntrega($idEjercicio, $idEstudiante, $nota, $comentario) {
    $con = obtenerConexion();
    $sql = "UPDATE entregas_ejercicios SET nota=?, comentarioProfesor=?, estado='calificado' WHERE idEjercicio=? AND idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "dsii", $nota, $comentario, $idEjercicio, $idEstudiante);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $ok;
}

function contarEntregasPorEjercicio($idEjercicio) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total FROM entregas_ejercicios WHERE idEjercicio = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEjercicio);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($con);
    return intval($fila['total']);
}

function contarEjerciciosPendientesEstudiante($idEstudiante, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) as total
            FROM ejercicios e
            WHERE e.idCiclo = ? AND e.publicado = 1
              AND e.idEjercicio NOT IN (
                SELECT idEjercicio FROM entregas_ejercicios WHERE idEstudiante = ?
              )";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idCiclo, $idEstudiante);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($res);
    mysqli_close($con);
    return intval($fila['total']);
}
