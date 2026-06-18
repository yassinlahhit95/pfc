<?php
require_once __DIR__ . "/conectar.php";

/**
 * Valida el login de un tutor
 */
function validarLoginTutor($email, $password) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM tutores WHERE emailTutor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $tutor = mysqli_fetch_assoc($res);

    if ($tutor && password_verify($password, $tutor['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'tutores', 'idTutor', $tutor['idTutor'], $password, $tutor['password']);
        return $tutor;
    }
    return null;
}

/**
 * Obtiene los datos de un tutor por su ID
 */
function obtenerTutorPorId($idTutor) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM tutores WHERE idTutor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idTutor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}

/**
 * Obtiene los estudiantes vinculados a un tutor
 */
function listarEstudiantesPorTutor($idTutor) {
    $con = obtenerConexion();
    $sql = "SELECT e.*, c.nombreCiclo, et.parentesco 
            FROM estudiantes e 
            JOIN estudiante_tutor et ON e.idEstudiante = et.idEstudiante 
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE et.idTutor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idTutor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

/**
 * Actualiza el token FCM de un tutor
 */
function actualizarTokenFCMTutor($idTutor, $nuevoToken) {
    $con = obtenerConexion();
    $sql = "UPDATE tutores SET fcm_token = ? WHERE idTutor = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nuevoToken, $idTutor);
    return mysqli_stmt_execute($stmt);
}

/**
 * Cuenta el total de tutores en el sistema
 */
function contarTutores() {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT COUNT(*) as total FROM tutores");
    $fila = mysqli_fetch_assoc($res);
    return intval($fila['total']);
}

/**
 * Lista todos los tutores con información básica
 */
function listarTodosLosTutores() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM tutores ORDER BY nombreTutor ASC";
    $res = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}
?>
