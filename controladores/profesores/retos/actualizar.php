<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idReto = trim($_POST['idReto']);
    $nombreReto = trim($_POST['nombreReto']);
    $fechaInicio = trim($_POST['fechaInicio']);
    $fechaFin = trim($_POST['fechaFin']);
    $horasReto = trim($_POST['horasReto']);
    $modulosSeleccionados = $_POST['modulos'] ?? [];

    $errores = [];

    if (empty($idReto)) {
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    }

    if (empty($nombreReto)) $errores['nombreReto'] = "El nombre del reto es obligatorio.";
    if (empty($fechaInicio)) $errores['fechaInicio'] = "La fecha de inicio es obligatoria.";
    if (empty($fechaFin)) $errores['fechaFin'] = "La fecha de fin es obligatoria.";
    if (empty($horasReto)) {
        $errores['horasReto'] = "Las horas son obligatorias.";
    } elseif (!is_numeric($horasReto)) {
        $errores['horasReto'] = "Las horas deben ser un valor numérico.";
    }

    if (empty($modulosSeleccionados)) {
        $errores['modulos'] = "Debe seleccionar al menos un módulo para este reto.";
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
        exit;
    }

    $resultado = actualizarReto($idReto, $nombreReto, $fechaInicio, $fechaFin, $horasReto, $modulosSeleccionados);
    if ($resultado) {
        $_SESSION['exito'] = "Reto actualizado correctamente.";
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al actualizar el reto.";
        header("Location: ../../../vistas/profesores/retos/editar.php?id=$idReto");
        exit;
    }
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
?>
