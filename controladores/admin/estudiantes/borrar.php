<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../include/Security.php";

if (isset($_POST['idEstudiante'])) {
    // Validar CSRF
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../../../vistas/admin/estudiantes/verEstudiantes.php'));
        exit;
    }

    $idEstudiante = trim($_POST['idEstudiante']);
    
    if (eliminarEstudiante($idEstudiante)) {
        $_SESSION['exito'] = "Estudiante eliminado.";
    } else {
        $_SESSION['errores'] = "Error al eliminar.";
    }
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
