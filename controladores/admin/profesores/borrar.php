<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['idProfesor'])) {

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
