<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function obtenerPreMatriculaPorDni($dni) {
    $con = obtenerConexion();
    $sql = "SELECT p.*, c.nombreCiclo
            FROM pre_matriculas p
            JOIN ciclos c ON p.idCiclo = c.idCiclo
            WHERE p.dni = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $dni);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}

function obtenerPreMatriculaPorEmail($email) {
    $con = obtenerConexion();
    $sql = "SELECT p.*, c.nombreCiclo
            FROM pre_matriculas p
            JOIN ciclos c ON p.idCiclo = c.idCiclo
            WHERE p.email = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}

function obtenerPreMatriculaPorId($id) {
    $con = obtenerConexion();
    $sql = "SELECT p.*, c.nombreCiclo FROM pre_matriculas p JOIN ciclos c ON p.idCiclo = c.idCiclo WHERE p.idPreMatricula = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}

function obtenerArchivosPreMatricula($idPreMatricula) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM pre_matricula_archivos WHERE idPreMatricula = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idPreMatricula);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $archivos = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $archivos[] = $fila;
    }
    return $archivos;
}

function listarPreMatriculas($estado = null) {
    $con = obtenerConexion();
    $sql = "SELECT p.*, c.nombreCiclo FROM pre_matriculas p JOIN ciclos c ON p.idCiclo = c.idCiclo";
    if ($estado) {
        $sql .= " WHERE p.estado = ?";
    }
    $sql .= " ORDER BY p.fechaSolicitud DESC";
    $stmt = mysqli_prepare($con, $sql);
    if ($estado) {
        mysqli_stmt_bind_param($stmt, "s", $estado);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function crearPreMatricula($dni, $nombre, $apellidos, $email, $telefono, $idCiclo, $curso = '1º', $tutor = []) {
    $con = obtenerConexion();
    $sql = "INSERT INTO pre_matriculas (dni, nombre, apellidos, email, telefono, idCiclo, curso, nombreTutor, dniTutor, emailTutor, telefonoTutor, parentescoTutor)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    $tutorNombre = $tutor['nombre']      ?? null;
    $tutorDni = $tutor['dni']            ?? null;
    $tutorEmail = $tutor['email']        ?? null;
    $tutorTelefono = $tutor['telefono']  ?? null;
    $tutorParentesco = $tutor['parentesco'] ?? null;
    mysqli_stmt_bind_param($stmt, "sssssissssss",
        $dni, $nombre, $apellidos, $email, $telefono, $idCiclo, $curso,
        $tutorNombre, $tutorDni, $tutorEmail, $tutorTelefono, $tutorParentesco
    );
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($con);
    }
    return false;
}

function registrarArchivoPreMatricula($idPreMatricula, $tipo, $nombre, $ruta) {
    $con = obtenerConexion();
    $sql = "INSERT INTO pre_matricula_archivos (idPreMatricula, tipoDocumento, nombreArchivo, rutaArchivo) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $idPreMatricula, $tipo, $nombre, $ruta);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarEstadoPreMatricula($id, $estado, $observaciones = null) {
    $con = obtenerConexion();
    $sql = "UPDATE pre_matriculas SET estado = ?, observaciones = ? WHERE idPreMatricula = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $estado, $observaciones, $id);
    return mysqli_stmt_execute($stmt);
}
