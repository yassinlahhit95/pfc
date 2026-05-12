<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idEstudiante = intval($_POST['idEstudiante'] ?? 0);

    if ($idEstudiante > 0) {
        $resultado = eliminarEstudiante($idEstudiante);
        if ($resultado) {
            $_SESSION['exito'] = "Estudiante eliminado correctamente.";
        } else {
            $_SESSION['error'] = "No se pudo eliminar al estudiante.";
        }
    }
}

header("Location: ../../../vistas/profesores/estudiantes/lista.php");
exit;
?>
