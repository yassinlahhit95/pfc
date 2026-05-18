<?php
session_start();
require_once "../../../modelos/retos.php";

if (isset($_POST['actualizarReto'])) {
    $idReto = $_POST['idReto'];
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

    if (empty($nombreReto)) $errores['nombreReto'] = "El nombre es obligatorio.";
    if (empty($fechaInicio)) $errores['fechaInicio'] = "Fecha de inicio requerida";
    if (empty($fechaFin)) $errores['fechaFin'] = "La fecha de fin es obligatoria.";
    if (empty($horasReto)) {
        $errores['horasReto'] = "Las horas son obligatorias.";
    } elseif (!is_numeric($horasReto)) {
        $errores['horasReto'] = "Las horas deben ser un número";
    }

    if (!empty($fechaInicio) && !empty($fechaFin) && !empty($horasReto) && is_numeric($horasReto) && $fechaInicio <= $fechaFin) {
        $fechaInicioObj = new DateTime($fechaInicio);
        $fechaFinObj = new DateTime($fechaFin);
        $diasLaborables = 0;

        $tempIter = clone $fechaInicioObj;
        while ($tempIter <= $fechaFinObj) {
            if ($tempIter->format('N') < 6) {
                $diasLaborables++;
            }
            $tempIter->modify('+1 day');
        }

        $maxHorasPermitidas = $diasLaborables * 6;
        if ($horasReto > $maxHorasPermitidas) {
            $errores['horasReto'] = "Las horas estimadas ($horasReto h) superan el máximo de $maxHorasPermitidas h para el periodo seleccionado ($diasLaborables días laborables x 6h).";
        }
    }

    if (empty($modulosSeleccionados)) {
        $errores['modulos'] = "Al menos un módulo";
    } else if (is_numeric($horasReto)) {
        foreach ($modulosSeleccionados as $idModulo) {
            $detalle = obtenerDetalleHorasModulo($idModulo, $idReto);
            if ($horasReto > $detalle['disponibles']) {
                $errores['modulos'] = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
                break;
            }
        }
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
