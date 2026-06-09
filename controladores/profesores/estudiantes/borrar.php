<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../../vistas/login.php");
    exit;
}

if (!empty($_POST['idEstudiante'])) {
    $idEstudiante = $_POST['idEstudiante'] ?? 0;

    if ($idEstudiante > 0) {
        $resultado = eliminarEstudiante($idEstudiante);
        if ($resultado) {
            $_SESSION['exito'] = "Estudiante eliminado correctamente.";
        } else {
            $_SESSION['errores'] = "No se pudo eliminar al estudiante.";
        }
    }
}

header("Location: ../../../vistas/profesores/estudiantes/lista.php");
exit;
?>
