<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['idProfesor'])) {

    $idProfesorBorrar = (int)($_POST['idProfesor'] ?? 0);
    if (eliminarProfesor($idProfesorBorrar)) {
        $_SESSION['exito'] = "El profesor ha sido eliminado correctamente.";
    } else {
        $hayError = true;
        $_SESSION['errores'] = "Ocurrió un error al intentar eliminar al profesor.";
    }
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>
