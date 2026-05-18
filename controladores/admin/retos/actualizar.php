<?php
session_start();
require_once __DIR__ . "/../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idRetoActualizar = $_POST['idReto'];
    $nombreRetoActualizar = trim($_POST['nombreReto']);
    $horasDelReto = trim($_POST['horasReto']);
    $fechaInicioDelReto = trim($_POST['fechaInicioReto']);
    $fechaFinDelReto = trim($_POST['fechaFinReto']);
    $idModuloAsociado = $_POST['modulosReto'] ?? '';

    $errores = [];

    if (empty($nombreRetoActualizar)) {
        $errores['nombreReto'] = "El nombre es obligatorio.";
    }
    if (empty($horasDelReto)) {
        $errores['horasReto'] = "Las horas son obligatorias.";
    } else {
        if (!is_numeric($horasDelReto)) {
            $errores['horasReto'] = "Las horas deben ser un número.";
        }
    }
    if (empty($fechaInicioDelReto)) {
        $errores['fechaInicioReto'] = "La fecha de inicio es obligatoria.";
    }
    if (empty($fechaFinDelReto)) {
        $errores['fechaFinReto'] = "La fecha de fin es obligatoria.";
    } else if (!empty($fechaInicioDelReto) && $fechaFinDelReto < $fechaInicioDelReto) {
        $errores['fechaFinReto'] = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    // Validación de horas según duración (6h/día laborable, 5 días/semana)
    if (!empty($fechaInicioDelReto) && !empty($fechaFinDelReto) && !empty($horasDelReto) && is_numeric($horasDelReto) && $fechaInicioDelReto <= $fechaFinDelReto) {
        $fechaInicioObj = new DateTime($fechaInicioDelReto);
        $fechaFinObj = new DateTime($fechaFinDelReto);
        $diasLaborables = 0;
        
        $tempIter = clone $fechaInicioObj;
        while ($tempIter <= $fechaFinObj) {
            if ($tempIter->format('N') < 6) {
                $diasLaborables++;
            }
            $tempIter->modify('+1 day');
        }
        
        $maxHorasPermitidas = $diasLaborables * 6;
        if ($horasDelReto > $maxHorasPermitidas) {
            $errores['horasReto'] = "Las horas estimadas ($horasDelReto h) superan el máximo de $maxHorasPermitidas h para el periodo seleccionado ($diasLaborables días laborables x 6h).";
        }
    }

    if (empty($idModuloAsociado) || !is_numeric($idModuloAsociado)) {
        $errores['modulosReto'] = "Selecciona un módulo";
    } else if (is_numeric($horasDelReto)) {
        $detalle = obtenerDetalleHorasModulo($idModuloAsociado, $idRetoActualizar);
        if ($horasDelReto > $detalle['disponibles']) {
            $errores['modulosReto'] = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
        }
    }

    if (empty($errores)) {
        if (actualizarReto($idRetoActualizar, $nombreRetoActualizar, $fechaInicioDelReto, $fechaFinDelReto, $horasDelReto, [$idModuloAsociado])) {
            $_SESSION['exito'] = "Reto actualizado correctamente.";
            header("Location: ../../../vistas/admin/retos/verRetos.php");
            exit;
        }
        $_SESSION['error'] = "No se pudo actualizar el reto o no hubo cambios.";
    } else {
        $_SESSION['errores'] = $errores;
        
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
