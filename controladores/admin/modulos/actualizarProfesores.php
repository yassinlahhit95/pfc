<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['actualizarProfesores'])) {
    $idModuloAsignar = intval(trim($_POST['idModulo']));
    $idProfesorAsignar = !empty($_POST['idProfesor']) ? intval(trim($_POST['idProfesor'])) : 0;

    // 1. Limpiar asociaciones previas del mÃ³dulo
    limpiarProfesoresModulo($idModuloAsignar);

    // 2. Insertar la nueva asociaciÃ³n (si se seleccionÃ³ un profesor)
    if ($idProfesorAsignar > 0) {
        if (!asociarModuloProfesor($idModuloAsignar, $idProfesorAsignar)) {
            $hayError = true;
        }
    }

    if (!$hayError) {
        $_SESSION['exito'] = "Profesor asignado.";
    } else {
        $_SESSION['error'] = "Error al asignar.";
    }
}

header("Location: ../../../vistas/admin/modulos/verModulos.php");
exit;
