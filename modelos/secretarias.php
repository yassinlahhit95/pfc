<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
//  AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════

function validarLoginSecretaria($email, $password) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM secretarias WHERE emailSecretaria = ? AND activoSecretaria = 1");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $secretaria = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($secretaria && password_verify($password, $secretaria['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'secretarias', 'idSecretaria', $secretaria['idSecretaria'], $password, $secretaria['password']);
        return $secretaria;
    }
    return null;
}

// ══════════════════════════════════════════════════════════════════════
//  CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function obtenerSecretariaPorId($id) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM secretarias WHERE idSecretaria = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

function listarSecretarias() {
    $con  = obtenerConexion();
    $res  = mysqli_query($con, "SELECT * FROM secretarias ORDER BY nombreSecretaria ASC");
    $lista = [];
    while ($f = mysqli_fetch_assoc($res)) { $lista[] = $f; }
    return $lista;
}

// ══════════════════════════════════════════════════════════════════════
//  ESCRITURA
// ══════════════════════════════════════════════════════════════════════

function insertarSecretaria($nombre, $email, $password) {
    $con  = obtenerConexion();
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = mysqli_prepare($con, "INSERT INTO secretarias (nombreSecretaria, emailSecretaria, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $hash);
    return mysqli_stmt_execute($stmt) ? mysqli_insert_id($con) : false;
}

function actualizarSecretaria($id, $nombre, $email) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE secretarias SET nombreSecretaria=?, emailSecretaria=? WHERE idSecretaria=?");
    mysqli_stmt_bind_param($stmt, "ssi", $nombre, $email, $id);
    return mysqli_stmt_execute($stmt);
}

function actualizarPasswordSecretaria($id, $password) {
    $con  = obtenerConexion();
    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = mysqli_prepare($con, "UPDATE secretarias SET password=?, pwd_changed_at=NOW(), must_change_password=0 WHERE idSecretaria=?");
    mysqli_stmt_bind_param($stmt, "si", $hash, $id);
    return mysqli_stmt_execute($stmt);
}

function actualizarTokenFCMSecretaria($id, $token) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE secretarias SET token_fcm=? WHERE idSecretaria=?");
    mysqli_stmt_bind_param($stmt, "si", $token, $id);
    return mysqli_stmt_execute($stmt);
}
