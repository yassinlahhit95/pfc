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

    $fallos = [];
    if (empty($nombreRetoActualizar)) $fallos['nombreReto'] = "El nombre es obligatorio.";
    if (empty($horasDelReto)) {
        $fallos['horasReto'] = "Las horas son obligatorias.";
    } elseif (!is_numeric($horasDelReto)) {
        $fallos['horasReto'] = "Las horas deben ser un número.";
    }
    if (empty($fechaInicioDelReto)) $fallos['fechaInicioReto'] = "La fecha de inicio es obligatoria.";
    if (empty($fechaFinDelReto)) {
        $fallos['fechaFinReto'] = "La fecha de fin es obligatoria.";
    } else if (!empty($fechaInicioDelReto) && $fechaFinDelReto < $fechaInicioDelReto) {
        $fallos['fechaFinReto'] = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    if (!empty($fechaInicioDelReto) && !empty($fechaFinDelReto) && !empty($horasDelReto) && is_numeric($horasDelReto) && $fechaInicioDelReto <= $fechaFinDelReto) {
        $fechaInicioObj = new DateTime($fechaInicioDelReto);
        $fechaFinObj = new DateTime($fechaFinDelReto);
        $diasLaborables = 0;
        $tempIter = clone $fechaInicioObj;
        while ($tempIter <= $fechaFinObj) {
            if ($tempIter->format('N') < 6) $diasLaborables++;
            $tempIter->modify('+1 day');
        }
        $maxHorasPermitidas = $diasLaborables * 6;
        if ($horasDelReto > $maxHorasPermitidas) {
            $fallos['horasReto'] = "Las horas ($horasDelReto h) superan el máximo permitido ($maxHorasPermitidas h).";
        }
    }

    if (empty($idModuloAsociado) || !is_numeric($idModuloAsociado)) {
        $fallos['modulosReto'] = "Selecciona un módulo";
    } else if (is_numeric($horasDelReto)) {
        $detalle = obtenerDetalleHorasModulo($idModuloAsociado, $idRetoActualizar);
        if ($horasDelReto > $detalle['disponibles']) {
            $fallos['modulosReto'] = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
        }
    }

    if (!empty($fallos)) {
        $_SESSION['errores'] = $fallos;
        $datosParaSesion = $_POST;
        $datosParaSesion['fechaInicio'] = $_POST['fechaInicioReto'];
        $datosParaSesion['fechaFin'] = $_POST['fechaFinReto'];
        $_SESSION['datos_reto'] = $datosParaSesion;
        header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idRetoActualizar");
        exit;
    }

    if (actualizarReto($idRetoActualizar, $nombreRetoActualizar, $fechaInicioDelReto, $fechaFinDelReto, $horasDelReto, [$idModuloAsociado])) {
        $_SESSION['exito'] = "Reto actualizado correctamente.";
        header("Location: ../../../vistas/admin/retos/verRetos.php");
        exit;
    }
    $_SESSION['error'] = "No se pudo actualizar el reto o no hubo cambios.";
    header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idRetoActualizar");
    exit;
}

header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
?>
