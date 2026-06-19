<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

if (isset($_POST['idEstudiante'])) {

    $idEstudiante = (int)($_POST['idEstudiante'] ?? 0);
    
    if (eliminarEstudiante($idEstudiante)) {
        $_SESSION['exito'] = "El estudiante ha sido eliminado correctamente.";
    } else {
        $_SESSION['errores'] = "Ocurrió un error al intentar eliminar al estudiante seleccionado.";
    }
}

header("Location: ../../../vistas/admin/estudiantes/verEstudiantes.php");
exit;
?>
