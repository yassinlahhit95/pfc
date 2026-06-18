<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['actualizarProfesores'])) {
    $idModuloAsignar = (int)($_POST['idModulo'] ?? 0);
    $idProfesorAsignar = 0;
    if (!empty($_POST['idProfesor'])) {
        $idProfesorAsignar = (int)($_POST['idProfesor'] ?? 0);
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
        $_SESSION['errores'] = "No se pudo asignar el profesor al módulo.";
    }
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
?>
