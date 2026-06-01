<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../include/Security.php";

$hayError = false;

if (isset($_POST['idProfesor'])) {
    // Validar CSRF
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../../../vistas/admin/profesores/verProfesores.php'));
        exit;
    }

    $idProfesorBorrar = trim($_POST['idProfesor']);
    if (eliminarProfesor($idProfesorBorrar)) {
        $_SESSION['exito'] = "Profesor eliminado.";
    } else {
        $hayError = true;
        $_SESSION['errores'] = "Error al eliminar el profesor.";
    }
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>
