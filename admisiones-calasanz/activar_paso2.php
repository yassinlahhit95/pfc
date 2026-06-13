<?php
session_start();
$_SESSION['paso2'] = true;

if (isset($_SESSION['dni_usuario'])) {
    require_once 'admin/generar_excel/conexion.php';
    $conexion = new Conexion();
    $db = $conexion->conectarse();
    $dni = $_SESSION['dni_usuario'];
    
    $stmt = $db->prepare("UPDATE admisiones SET Paso2 = 1 WHERE DNI = ?");
    if ($stmt) {
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $stmt->close();
    }
}

http_response_code(200);
