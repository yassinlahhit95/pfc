<?php
require_once __DIR__ . '/../../../include/ProfesorGuard.php';
require_once "../../../modelos/retos.php";

if (isset($_POST['insertarReto'])) {
    $nombreReto = trim($_POST['nombreReto']);
    $fechaInicio = trim($_POST['fechaInicio']);
    $fechaFin = trim($_POST['fechaFin']);
    $horasReto = trim($_POST['horasReto']);
    $modulosSeleccionados = $_POST['modulos'] ?? [];

    $errores = '';

    if (empty($nombreReto)) $errores = "Falta el nombre del reto";
    if (empty($fechaInicio)) $errores = "La fecha de inicio es obligatoria.";
    if (empty($fechaFin)) $errores = "Falta la fecha de fin";
    if (empty($horasReto)) {
        $errores = "Horas requeridas";
    } elseif (!is_numeric($horasReto)) {
        $errores = "Pon un número de horas";
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
            $errores = "Las horas ($horasReto h) superan el máximo permitido ($maxHorasPermitidas h).";
        }
    }

    if (empty($modulosSeleccionados)) {
        $errores = "Selecciona al menos un módulo";
    } else if (is_numeric($horasReto)) {
        foreach ($modulosSeleccionados as $idModulo) {
            $detalle = obtenerDetalleHorasModulo($idModulo);
            if ($horasReto > $detalle['disponibles']) {
                $errores = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
                break;
            }
        }
    }

    if ($errores) {
        $_SESSION['errores'] = $errores;
        $_SESSION['datos_reto'] = $_POST;
        header("Location: ../../../vistas/profesores/retos/agregar.php");
        exit;
    }

    $resultado = insertarReto($nombreReto, $fechaInicio, $fechaFin, $horasReto, $modulosSeleccionados);
    if ($resultado) {
        $_SESSION['exito'] = "Reto insertado correctamente.";
        header("Location: ../../../vistas/profesores/retos/lista.php");
        exit;
    } else {
        $_SESSION['errores'] = "Error al insertar el reto.";
        $_SESSION['datos_reto'] = $_POST;
        header("Location: ../../../vistas/profesores/retos/agregar.php");
        exit;
    }
}

header("Location: ../../../vistas/profesores/retos/lista.php");
exit;
?>
