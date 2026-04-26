<?php
session_start();
require_once "../../../modelos/modulos.php";
require_once "../../../modelos/profesores.php";

if (isset($_POST['actualizarProfesores'])) {
    $idModulo = intval($_POST['idModulo']);
    $profesores = isset($_POST['profesores']) ? $_POST['profesores'] : [];

    // 1. Limpiar asociaciones previas
    limpiarProfesoresModulo($idModulo);

    // 2. Insertar nuevas asociaciones
    $error = false;
    foreach ($profesores as $idProf) {
        if (!asociarModuloProfesor($idModulo, intval($idProf))) {
            $error = true;
        }
    }

    if (!$error) {
        $_SESSION['exito'] = "Profesores asignados al módulo correctamente.";
    } else {
        $_SESSION['error'] = "Hubo un error al asignar algunos profesores.";
    }
}

header("Location: /pfc/vistas/admin/modulos/verModulos.php");
exit;
