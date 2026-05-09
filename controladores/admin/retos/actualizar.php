<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idRetoActualizar = trim($_POST['idReto']);
    $nombreRetoActualizar = trim($_POST['nombreReto']);
    $horasDelReto = trim($_POST['horasReto']);
    $fechaInicioDelReto = trim($_POST['fechaInicioReto']);
    $fechaFinDelReto = trim($_POST['fechaFinReto']);
    $listaModulosAsociados = $_POST['modulosReto'] ?? [];

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
    } else if (!empty($fechaInicioDelReto) && $fechaFinDelReto < $fechaInicioDelReto) {
        $listaErroresValidacion['fechaFinReto'] = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    if (!empty($fechaInicioDelReto) && !empty($fechaFinDelReto) && !empty($horasDelReto) && is_numeric($horasDelReto) && $fechaInicioDelReto <= $fechaFinDelReto) {
        $begin = new DateTime($fechaInicioDelReto);
        $end = new DateTime($fechaFinDelReto);
        $diasLaborables = 0;
        while ($begin <= $end) {
            if ($begin->format('N') < 6) {
                $diasLaborables++;
            }
            $begin->modify('+1 day');
        }
        $maxHorasPermitidas = $diasLaborables * 6;
        if ($horasDelReto > $maxHorasPermitidas) {
            $listaErroresValidacion['horasReto'] = "Las horas estimadas superan el máximo de $maxHorasPermitidas h para el periodo seleccionado (6h/día laborable).";
        }
    }
    if (empty($listaModulosAsociados)) {
        $listaErroresValidacion['modulosReto'] = "Debe seleccionar al menos un módulo.";
    } else if (is_numeric($horasDelReto)) {
        foreach ($listaModulosAsociados as $idModuloParaValidar) {
            if (!comprobarHorasDisponiblesModulo($idModuloParaValidar, $horasDelReto, $idRetoActualizar)) {
                $listaErroresValidacion['modulosReto'] = "Un módulo seleccionado no tiene suficientes horas.";
                break;
            }
        }
    }

    if (empty($listaErroresValidacion)) {
        if (actualizarReto($idRetoActualizar, $nombreRetoActualizar, $fechaInicioDelReto, $fechaFinDelReto, $horasDelReto, $listaModulosAsociados)) {
            $_SESSION['exito'] = "Reto actualizado correctamente.";
            header("Location: ../../../vistas/admin/retos/verRetos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo actualizar el reto o no hubo cambios.";
    } else {
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
?>
