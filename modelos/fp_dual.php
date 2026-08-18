<?php
require_once __DIR__ . "/conectar.php";

function listarEmpresas() {
    $con = obtenerConexion();
    $sql = "SELECT * FROM fp_empresas ORDER BY idEmpresa ASC";
    $res = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerEmpresaPorId($idEmpresa) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM fp_empresas WHERE idEmpresa = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEmpresa);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function insertarEmpresa($nombre, $cif, $direccion, $contacto, $telefono, $email, $password = null) {
    $con = obtenerConexion();
    $hash = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
    $sql = "INSERT INTO fp_empresas (nombre, cif, direccion, persona_contacto, telefono, email, activo, password) VALUES (?, ?, ?, ?, ?, ?, 1, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssss", $nombre, $cif, $direccion, $contacto, $telefono, $email, $hash);
    return mysqli_stmt_execute($stmt);
}

function actualizarEmpresa($idEmpresa, $nombre, $cif, $direccion, $contacto, $telefono, $email, $activo, $password = null) {
    $con = obtenerConexion();
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE fp_empresas SET nombre=?, cif=?, direccion=?, persona_contacto=?, telefono=?, email=?, activo=?, password=? WHERE idEmpresa=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssisi", $nombre, $cif, $direccion, $contacto, $telefono, $email, $activo, $hash, $idEmpresa);
    } else {
        $sql = "UPDATE fp_empresas SET nombre=?, cif=?, direccion=?, persona_contacto=?, telefono=?, email=?, activo=? WHERE idEmpresa=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssii", $nombre, $cif, $direccion, $contacto, $telefono, $email, $activo, $idEmpresa);
    }
    return mysqli_stmt_execute($stmt);
}

function actualizarPasswordEmpresa($idEmpresa, $password) {
    $con = obtenerConexion();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE fp_empresas SET password=? WHERE idEmpresa=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $hash, $idEmpresa);
    return mysqli_stmt_execute($stmt);
}

function eliminarEmpresa($idEmpresa) {
    $con = obtenerConexion();
    $sql = "DELETE FROM fp_empresas WHERE idEmpresa = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEmpresa);
    return mysqli_stmt_execute($stmt);
}
