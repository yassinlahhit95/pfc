<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

$hayError = false;

if (isset($_POST['actualizarReto'])) {
    $idRetoActualizar = trim($_POST['idReto']);
    $nombreRetoActualizar = trim($_POST['nombreReto']);
    $horasDelReto = trim($_POST['horasReto']);
    $fechaInicioDelReto = trim($_POST['fechaInicioReto']);
    $fechaFinDelReto = trim($_POST['fechaFinReto']);
    $listaModulosAsociados = isset($_POST['modulosReto']) ? $_POST['modulosReto'] : [];

    $listaErroresValidacion = [];

    if (empty($nombreRetoActualizar)) {
        $listaErroresValidacion['nombreReto'] = "El nombre es obligatorio.";
    }
    if (empty($horasDelReto)) {
        $listaErroresValidacion['horasReto'] = "Las horas son obligatorias.";
    } else {
        if (!is_numeric($horasDelReto)) {
            $listaErroresValidacion['horasReto'] = "Las horas deben ser un número.";
        }
    }
    if (empty($fechaInicioDelReto)) {
        $listaErroresValidacion['fechaInicioReto'] = "La fecha de inicio es obligatoria.";
    }
    if (empty($fechaFinDelReto)) {
        $listaErroresValidacion['fechaFinReto'] = "La fecha de fin es obligatoria.";
    }
    if (empty($listaModulosAsociados)) {
        $listaErroresValidacion['modulosReto'] = "Debe seleccionar al menos un módulo.";
    } else if (is_numeric($horasDelReto)) {
        // Validar que el módulo tenga suficientes horas disponibles
        foreach ($listaModulosAsociados as $idModuloParaValidar) {
            if (!comprobarHorasDisponiblesModulo($idModuloParaValidar, $horasDelReto, $idRetoActualizar)) {
                $listaErroresValidacion['modulosReto'] = "Un módulo seleccionado no tiene suficientes horas.";
                break;
            }
        }
    }

    if (empty($listaErroresValidacion)) {
        if (actualizarReto($idRetoActualizar, $nombreRetoActualizar, $fechaInicioDelReto, $fechaFinDelReto, $horasDelReto, $listaModulosAsociados)) {
            $_SESSION['exito'] = "Reto actualizado.";
            header("Location: ../../../vistas/admin/retos/verRetos.php");
            exit;
        } else {
            $hayError = true;
            $_SESSION['error'] = "Error al actualizar en la base de datos.";
        }
    } else {
        $hayError = true;
        $_SESSION['errores'] = $listaErroresValidacion;
        
        $datosParaSesion = $_POST;
        $datosParaSesion['fechaInicio'] = $_POST['fechaInicioReto'];
        $datosParaSesion['fechaFin'] = $_POST['fechaFinReto'];
        $_SESSION['datos_reto'] = $datosParaSesion;
    }

    header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idRetoActualizar");
    exit;
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
