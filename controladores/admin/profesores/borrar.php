<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idProfesor'])) {
    $idProfesorBorrar = trim($_POST['idProfesor']);
    if (eliminarProfesor($idProfesorBorrar)) {
        $_SESSION['exito'] = "Profesor eliminado.";
    } else {
        $hayError = true;
        $_SESSION['error'] = "Error al eliminar el profesor.";
    }
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
