<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";

$hayError = false;

if (isset($_POST['actualizarModulos'])) {
    $idProfesorAsignacion = trim($_POST['idProfesor']);
    $listaModulosSeleccionados = $_POST['modulos'] ?? [];

    limpiarModulosProfesor($idProfesorAsignacion);

    foreach ($listaModulosSeleccionados as $idModuloParaAsociar) {
        if (!asociarModuloProfesor($idModuloParaAsociar, $idProfesorAsignacion)) {
            $hayError = true;
        }
    }

    if (!$hayError) {
        $_SESSION['exito'] = "Módulos asignados.";
    } else {
        $_SESSION['errores'] = "Error al asignar módulos.";
    }
}

header("Location: ../../../vistas/admin/profesores/verProfesores.php");
exit;
?>
