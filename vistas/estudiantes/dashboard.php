<?php
/**
 * Redirección al Dashboard Principal de Estudiantes
 * 
 * Este archivo sirve como punto de entrada para el portal de estudiantes,
 * redirigiendo al usuario a la vista actual del dashboard situada en la subcarpeta 'inicio/'.
 */

session_start();

// Verificamos si el usuario tiene una sesión de estudiante activa
if (empty($_SESSION['idEstudiante'])) {
    header("Location: ../login.php");
    exit;
}

// Redireccionamos a la ubicación actual del dashboard
header("Location: inicio/dashboard.php");
exit;
?>
