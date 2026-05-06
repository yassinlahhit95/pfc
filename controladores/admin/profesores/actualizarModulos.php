<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['actualizarModulos'])) {
    $idProfesorAsignacion = intval(trim($_POST['idProfesor']));
    $listaModulosSeleccionados = $_POST['modulos'] ?? [];

    // 1. Limpiar asignaciones previas
    limpiarModulosProfesor($idProfesorAsignacion);

    // 2. Insertar nuevas asignaciones
    foreach ($listaModulosSeleccionados as $idModuloParaAsociar) {
        if (!asociarModuloProfesor(intval($idModuloParaAsociar), $idProfesorAsignacion)) {
            $hayError = true;
        }
    }

    if (!$hayError) {
        $_SESSION['exito'] = "Módulos asignados.";
    } else {
        $_SESSION['error'] = "Error al asignar módulos.";
    }
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>
