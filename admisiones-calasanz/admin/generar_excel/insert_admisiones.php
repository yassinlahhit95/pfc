<?php

include_once("../../db/socket.php");

$db_table_name = "admisiones";

// Establish database connection using MySQLi
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);

if ($mysqli->connect_errno) {
    die('No se ha podido conectar a la base de datos: ' . $mysqli->connect_error);
}

$mysqli->set_charset("utf8");

// Get and sanitize input data
$subs_nombre = trim((string)(isset($_POST['nombre']) ? $_POST['nombre'] : ''));
$subs_apellidos = trim((string)(isset($_POST['apellidos']) ? $_POST['apellidos'] : ''));
$subs_dni = strtoupper(trim((string)(isset($_POST['dni']) ? $_POST['dni'] : '')));
$subs_ciclo = trim((string)(isset($_POST['ciclo']) ? $_POST['ciclo'] : ''));

if ($subs_dni === '') {
    die('Error: El DNI es obligatorio.');
}

// Check if the student already exists in the database to prevent duplicates
$stmt = $mysqli->prepare("SELECT DNI FROM {$db_table_name} WHERE DNI = ? LIMIT 1");
if (!$stmt) {
    die('Error al preparar la consulta de verificación: ' . $mysqli->error);
}

$stmt->bind_param("s", $subs_dni);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Student does NOT exist, proceed with insertion
    $stmt_insert = $mysqli->prepare("INSERT INTO {$db_table_name} (DNI, Nombre, Apellidos, Ciclo) VALUES (?, ?, ?, ?)");
    if (!$stmt_insert) {
        die('Error al preparar la inserción: ' . $mysqli->error);
    }
    
    $stmt_insert->bind_param("ssss", $subs_dni, $subs_nombre, $subs_apellidos, $subs_ciclo);
    
    if ($stmt_insert->execute()) {
        header('Location: ../panel_gestion_confirm.php');
        exit;
    } else {
        die('Error al insertar los datos: ' . $stmt_insert->error);
    }
    $stmt_insert->close();

} else {
    // If student already exists, redirect back to panel with an error message
    header('Location: ../panel_gestion.php?status=error_exists');
    exit;
}

$stmt->close();
$mysqli->close();
?>