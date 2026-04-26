<?php
session_start();
require_once "../../../modelos/profesores.php";

if (isset($_POST['actualizarModulos'])) {
    $idProfesor = intval($_POST['idProfesor']);
    $modulos = isset($_POST['modulos']) ? $_POST['modulos'] : [];

    // 1. Limpiar asignaciones previas
    limpiarModulosProfesor($idProfesor);

    // 2. Insertar nuevas asignaciones
    $error = false;
    foreach ($modulos as $idMod) {
        if (!asociarModuloProfesor(intval($idMod), $idProfesor)) {
            $error = true;
        }
    }

    if (!$error) {
        $_SESSION['exito'] = "Módulos asignados correctamente.";
    } else {
        $_SESSION['error'] = "Hubo un problema al asignar algunos módulos.";
    }
}

header("Location: /pfc/vistas/admin/profesores/verProfesores.php");
exit;

