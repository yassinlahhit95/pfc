<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_retos');
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/log.php";
require_once __DIR__ . "/../../../include/upload_helpers.php";

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (isset($_POST['actualizarReto'])) {
    if (!Security::validateCSRFToken()) {
        $_SESSION['errores'] = 'Solicitud inválida. Inténtelo de nuevo.';
        header("Location: ../../../vistas/admin/retos/verRetos.php");
        exit;
    }
    $idReto      = (int)($_POST['idReto'] ?? 0);
    $nombre      = trim($_POST['nombreReto']);
    $horas       = trim($_POST['horasReto']);
    $fechaInicio = trim($_POST['fechaInicioReto']);
    $fechaFin    = trim($_POST['fechaFinReto']);
    $idModulo    = $_POST['modulosReto'] ?? '';

    $errores = [];
    if (empty($nombre)) $errores['nombreReto'] = "El nombre del reto es un campo obligatorio.";
    if (empty($horas)) {
        $errores['horasReto'] = "Las horas del reto son un campo obligatorio.";
    } elseif (!is_numeric($horas)) {
        $errores['horasReto'] = "Las horas deben ser un valor numérico.";
    }
    if (empty($fechaInicio)) $errores['fechaInicioReto'] = "La fecha de inicio es un campo obligatorio.";
    if (empty($fechaFin)) {
        $errores['fechaFinReto'] = "La fecha de fin es un campo obligatorio.";
    } elseif (!empty($fechaInicio) && $fechaFin < $fechaInicio) {
        $errores['fechaFinReto'] = "La fecha de fin no puede ser anterior a la de inicio.";
    }

    if (!empty($fechaInicio) && !empty($fechaFin) && !empty($horas) && is_numeric($horas) && $fechaInicio <= $fechaFin) {
        $maxHoras = calcularMaxHorasLaborables($fechaInicio, $fechaFin);
        if ($horas > $maxHoras) {
            $errores['horasReto'] = "Las horas ($horas h) superan el máximo permitido ($maxHoras h).";
        }
    }

    if (empty($idModulo) || !is_numeric($idModulo)) {
        $errores['modulosReto'] = "Debe seleccionar un módulo para el reto.";
    } elseif (is_numeric($horas)) {
        $detalle = obtenerDetalleHorasModulo($idModulo, $idReto);
        if ($horas > $detalle['disponibles']) {
            $errores['modulosReto'] = "El módulo '{$detalle['nombreModulo']}' solo tiene {$detalle['disponibles']}h disponibles (Total: {$detalle['maximo']}h, Ocupadas: {$detalle['ocupadas']}h).";
        }
    }

    if (!empty($errores)) {
        $_SESSION['errores'] = $errores;
        $datosParaSesion = $_POST;
        $datosParaSesion['fechaInicio'] = $_POST['fechaInicioReto'];
        $datosParaSesion['fechaFin']    = $_POST['fechaFinReto'];
        $_SESSION['datos_reto'] = $datosParaSesion;
        header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idReto");
        exit;
    }

    if (actualizarReto($idReto, $nombre, $fechaInicio, $fechaFin, $horas, [$idModulo])) {
        registrarAccion('actualizar', 'retos', $idReto, $nombre);
        procesarArchivosReto($idReto);
        $_SESSION['exito'] = "El reto ha sido actualizado correctamente.";
        header("Location: ../../../vistas/admin/retos/verRetos.php");
        exit;
    }
    $_SESSION['errores'] = "Ocurrió un error al intentar actualizar el reto o no se detectaron cambios.";
    header("Location: ../../../vistas/admin/retos/modificarRetos.php?idReto=$idReto");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// RESPUESTA
// ══════════════════════════════════════════════════════════════════════
header("Location: ../../../vistas/admin/retos/verRetos.php");
exit;
