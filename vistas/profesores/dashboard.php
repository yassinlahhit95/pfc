<?php
/**
 * Redirección al Dashboard Principal de Profesores
 * 
 * Este archivo sirve como punto de entrada para el portal de profesores,
 * redirigiendo al usuario a la vista actual del dashboard situada en la subcarpeta 'inicio/'.
 */

session_start();

// Verificamos si el usuario tiene una sesión de profesor activa
if (empty($_SESSION['idProfesor'])) {
    header("Location: ../login.php");
    exit;
}

// Redireccionamos a la ubicación actual del dashboard
header("Location: inicio/dashboard.php");
exit;
?>
