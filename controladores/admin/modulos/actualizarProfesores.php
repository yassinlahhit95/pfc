<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['actualizarProfesores'])) {
    $idModuloAsignar = trim($_POST['idModulo']);
    $idProfesorAsignar = 0;
    if (!empty($_POST['idProfesor'])) {
        $idProfesorAsignar = trim($_POST['idProfesor']);
    }
    limpiarProfesoresModulo($idModuloAsignar);
    if ($idProfesorAsignar > 0) {
        if (!asociarModuloProfesor($idModuloAsignar, $idProfesorAsignar)) {
            $hayError = true;
        }
    }

    if (!$hayError) {
        $_SESSION['exito'] = "Profesor asignado.";
    } else {
        $_SESSION['errores'] = "Error al asignar.";
    }
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>
