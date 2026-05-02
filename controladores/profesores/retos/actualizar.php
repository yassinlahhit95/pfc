<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idReto = trim($_POST['idReto']);
    $nombreReto = trim($_POST['nombreReto']);
    $fechaInicio = trim($_POST['fechaInicio']);
    $fechaFin = trim($_POST['fechaFin']);
    $horasReto = trim($_POST['horasReto']);

    $hayError = false;

    if (empty($idReto)) {
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    }

    if (empty($nombreReto)) {
        $_SESSION['error'] = "El nombre del reto es obligatorio.";
        $hayError = true;
    }

    if (!$hayError) {
        $resultado = actualizarReto($idReto, $nombreReto, $fechaInicio, $fechaFin, $horasReto);
        if ($resultado) {
            $_SESSION['exito'] = "Reto actualizado.";
        } else {
            $_SESSION['error'] = "Error al actualizar el reto.";
        }
    }
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
