<?php
session_start();
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../../include/Security.php";

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

if (isset($_POST['idDirector'])) {
    $idDirector = trim($_POST['idDirector']);
    
    if (eliminarDirector($idDirector)) {
        $_SESSION['exito'] = "Director eliminado correctamente.";
    } else {
        $_SESSION['errores'] = "Error al eliminar el director.";
    }
}

header("Location: ../../../vistas/admin/directores/verDirectores.php");
exit;
?>
