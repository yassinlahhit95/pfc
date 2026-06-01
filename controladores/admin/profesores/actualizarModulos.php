<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../include/Security.php";

$hayError = false;

if (isset($_POST['actualizarModulos'])) {
    // Validar CSRF
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = "Solicitud no válida o expirada (CSRF).";
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '../../../vistas/admin/profesores/verProfesores.php'));
        exit;
    }

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
