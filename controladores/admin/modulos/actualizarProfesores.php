<?php
session_start();
require_once "../../../modelos/modulos.php";
require_once "../../../modelos/profesores.php";

if (isset($_POST['actualizarProfesores'])) {
    $idModulo = intval($_POST['idModulo']);
    $idProfesor = !empty($_POST['idProfesor']) ? intval($_POST['idProfesor']) : 0;

    // 1. Limpiar asociaciones previas del módulo
    limpiarProfesoresModulo($idModulo);

    // 2. Insertar la nueva asociación (si se seleccionó un profesor)
    $error = false;
    if ($idProfesor > 0) {
        if (!asociarModuloProfesor($idModulo, $idProfesor)) {
            $error = true;
        }
    }

    if (!$error) {
        $_SESSION['exito'] = strtoupper("PROFESOR ASIGNADO AL MÓDULO CORRECTAMENTE.");
    } else {
        $_SESSION['error'] = strtoupper("HUBO UN ERROR AL ASIGNAR EL PROFESOR.");
    }
}

header("Location: /pfc/vistas/admin/modulos/verModulos.php");
exit;
?>
