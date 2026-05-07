<?php
/**
 * Redirección al Dashboard Principal de Administración
 * 
 * Este archivo sirve como punto de entrada para el panel de administración,
 * redirigiendo al usuario a la vista actual del dashboard situada en la subcarpeta 'inicio/'.
 */

session_start();

// Verificamos si el usuario tiene una sesión de administrador activa
if (empty($_SESSION['idAdmin'])) {
    header("Location: ../login.php");
    exit;
}

// Redireccionamos a la ubicación actual del dashboard
header("Location: inicio/dashboard.php");
exit;
?>
