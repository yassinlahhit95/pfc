<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['idEstudiante'])) {

    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
    
    if (eliminarEstudiante($idEstudiante)) {
        $_SESSION['exito'] = "Estudiante eliminado.";
    } else {
        $_SESSION['errores'] = "No se pudo eliminar el estudiante.";
    }
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
